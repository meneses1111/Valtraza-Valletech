"""Comprueba por HTTPS las páginas del GitBook después de desplegarlas."""
import os
import re
import time
from pathlib import Path
from urllib.error import HTTPError, URLError
from urllib.parse import urljoin, urlsplit
from urllib.request import urlopen

ROOT = Path(__file__).resolve().parents[2]
origin = os.environ.get('GITBOOK_PUBLIC_URL', '').rstrip('/') + '/'
url = urlsplit(origin)
if url.scheme != 'https' or url.netloc != 'meneses1111.github.io' or url.path != '/Valtraza-Valletech/':
    raise SystemExit('La URL no corresponde al GitBook público de Valtraza.')

def read(relative, retries=1):
    for attempt in range(retries):
        try:
            with urlopen(urljoin(origin, relative), timeout=15) as response:
                if response.status != 200:
                    raise RuntimeError('HTTP inesperado en ' + relative)
                body = response.read()
                if not body:
                    raise RuntimeError('Respuesta vacía en ' + relative)
                return body
        except (HTTPError, URLError, TimeoutError):
            if attempt + 1 == retries:
                raise
            time.sleep(4)

summary = (ROOT / 'docs/SUMMARY.md').read_text(encoding='utf-8')
pages = re.findall(r'\]\(([^)]+\.md)\)', summary)
for index, source in enumerate(pages):
    relative = re.sub(r'README\.md$', '', source)
    relative = re.sub(r'\.md$', '.html', relative)
    html = read(relative, retries=15 if index == 0 else 3).decode('utf-8')
    if 'markdown-section' not in html or 'Valtraza' not in html:
        raise RuntimeError('No aparece el contenido de GitBook en ' + relative)
    print('HTTPS 200 con contenido: ' + (relative or '/'))

for resource in ['gitbook/style.css', 'gitbook/theme.js', 'assets/modulos.svg', 'assets/relaciones-datos.svg']:
    read(resource, retries=3)
    print('Recurso público correcto: ' + resource)

print(f'Publicación verificada: {len(pages)} páginas y 4 recursos mediante HTTPS. {origin}')
