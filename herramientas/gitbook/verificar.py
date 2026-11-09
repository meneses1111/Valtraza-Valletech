"""Valida las fuentes y, opcionalmente, la salida real de GitBook."""

import argparse
import json
import re
from html.parser import HTMLParser
from pathlib import Path
from urllib.parse import unquote, urlsplit
from xml.etree import ElementTree

ROOT = Path(__file__).resolve().parents[2]
DOCS = ROOT / 'docs'
ERRORS = []


def links(text):
    text = re.sub(r'^```[^\n]*\n.*?^```\s*$', '', text, flags=re.M | re.S)
    return re.findall(r'!?\[[^\]]*\]\(([^\s)]+)(?:\s+"[^"]*")?\)', text)


def local_target(base, link, boundary):
    url = urlsplit(link)
    if url.scheme or url.netloc:
        return None
    target = (base / unquote(url.path)).resolve() if url.path else base
    if not target.is_relative_to(boundary.resolve()):
        ERRORS.append(f'Enlace fuera del libro: {link}')
        return None
    return target


class Page(HTMLParser):
    def __init__(self):
        super().__init__()
        self.links = []
        self.ids = set()
        self.content = []
        self.depth = 0

    def handle_starttag(self, tag, attributes):
        attrs = dict(attributes)
        if attrs.get('id'):
            self.ids.add(attrs['id'])
        if tag == 'a' and attrs.get('name'):
            self.ids.add(attrs['name'])
        for key in ('href', 'src'):
            if attrs.get(key):
                self.links.append(attrs[key])
        if self.depth:
            if tag not in {'area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source', 'track', 'wbr'}:
                self.depth += 1
        elif 'markdown-section' in attrs.get('class', '').split():
            self.depth = 1

    def handle_endtag(self, tag):
        if tag in {'area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source', 'track', 'wbr'}:
            return
        if self.depth:
            self.depth -= 1

    def handle_data(self, data):
        if self.depth:
            self.content.append(data)


def output_path(relative):
    page = Path(relative)
    return page.with_name('index.html') if page.name == 'README.md' else page.with_suffix('.html')


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument('--html', type=Path)
    args = parser.parse_args()
    summary = (DOCS / 'SUMMARY.md').read_text(encoding='utf-8')
    pages = [p for p in links(summary) if urlsplit(p).path.endswith('.md')]
    if len(pages) != len(set(pages)):
        ERRORS.append('El índice repite una página.')
    if not pages:
        ERRORS.append('El índice no tiene páginas.')
    for relative in pages:
        target = local_target(DOCS, relative, DOCS)
        if target is None or not target.is_file():
            ERRORS.append(f'Página del índice inexistente: {relative}')
        elif len(target.read_text(encoding='utf-8').strip()) < 100:
            ERRORS.append(f'Página del índice vacía o incompleta: {relative}')
    all_pages = {p.relative_to(DOCS).as_posix() for p in DOCS.rglob('*.md') if p.name != 'SUMMARY.md'}
    if all_pages != set(pages):
        ERRORS.append('Hay páginas fuera del índice o referencias sin página: ' + ', '.join(sorted(all_pages ^ set(pages))))
    for document in DOCS.rglob('*.md'):
        text = document.read_text(encoding='utf-8')
        if '\ufffd' in text:
            ERRORS.append(f'Carácter de sustitución en {document.relative_to(DOCS)}')
        for link in links(text):
            target = local_target(document.parent, link, DOCS)
            if target is not None and not target.exists():
                ERRORS.append(f'Enlace roto en {document.relative_to(DOCS)}: {link}')
    config = json.loads((DOCS / 'book.json').read_text(encoding='utf-8'))
    if config.get('gitbook') != '3.2.2':
        ERRORS.append('El libro debe mantener GitBook 3.2.2.')
    for svg in (DOCS / 'assets').glob('*.svg'):
        ElementTree.parse(svg)

    html_count = 0
    if args.html:
        build = (ROOT / args.html).resolve()
        parsed = {}
        for relative in pages:
            html_file = build / output_path(relative)
            if not html_file.is_file():
                ERRORS.append(f'No se generó {html_file.relative_to(build)}')
                continue
            page = Page()
            page.feed(html_file.read_text(encoding='utf-8'))
            parsed[html_file.resolve()] = page
            if len(' '.join(page.content).strip()) < 80:
                ERRORS.append(f'Sección HTML vacía: {html_file.relative_to(build)}')
        for html_file, page in parsed.items():
            html_count += 1
            for link in page.links:
                url = urlsplit(link)
                if url.scheme or url.netloc:
                    continue
                if not url.path:
                    target = html_file
                else:
                    target = local_target(html_file.parent, link, build)
                    if target is None:
                        continue
                if target.is_dir():
                    target /= 'index.html'
                if not target.is_file():
                    ERRORS.append(f'Recurso HTML inexistente en {html_file.relative_to(build)}: {link}')
                elif url.fragment and target.suffix == '.html':
                    target_page = parsed.get(target.resolve())
                    if target_page is None:
                        target_page = Page()
                        target_page.feed(target.read_text(encoding='utf-8'))
                    if unquote(url.fragment) not in target_page.ids:
                        ERRORS.append(f'Ancla HTML inexistente: {link}')
        if not (build / 'gitbook').is_dir():
            ERRORS.append('Faltan los recursos del tema GitBook.')

    if ERRORS:
        raise SystemExit('\n'.join(ERRORS))
    print(json.dumps({'fuentes': 'correctas', 'paginas': len(pages), 'html_verificados': html_count, 'enlaces_internos': 'correctos', 'svg': 'validos'}, ensure_ascii=False))


if __name__ == '__main__':
    main()
