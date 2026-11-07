document.addEventListener('DOMContentLoaded', () => {
    const ventana = document.getElementById('confirmacion');
    const titulo = document.getElementById('confirmacion-titulo');
    const confirmar = (mensaje) => {
        if (!ventana || !titulo || !window.bootstrap) return;
        titulo.textContent = mensaje;
        bootstrap.Modal.getOrCreateInstance(ventana).show();
    };
    if (ventana?.dataset.mostrar === 'si') confirmar(titulo.textContent);
    document.querySelectorAll('[data-copiar]').forEach((boton) => {
        boton.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(boton.dataset.copiar);
                confirmar('Código copiado');
            } catch {
                const aviso = document.createElement('p');
                aviso.className = 'small text-body-secondary mt-2';
                aviso.textContent = 'Selecciona el código y cópialo manualmente.';
                boton.replaceWith(aviso);
            }
        });
    });
    const sinFallas = document.querySelector('[data-sin-fallas]');
    const resultado = document.getElementById('resultado');
    if (sinFallas && resultado) {
        const actualizar = () => { resultado.disabled = sinFallas.checked; resultado.required = !sinFallas.checked; };
        sinFallas.addEventListener('change', actualizar);
        actualizar();
    }
    const lista = document.querySelector('[data-lista-repuestos]');
    const plantilla = document.getElementById('plantilla-repuesto');
    const agregar = document.querySelector('[data-agregar-repuesto]');
    if (lista && plantilla && agregar) {
        let siguiente = Math.max(-1, ...Array.from(lista.querySelectorAll('[data-indice]'), (fila) => Number(fila.dataset.indice) || 0)) + 1;
        agregar.addEventListener('click', () => {
            lista.insertAdjacentHTML('beforeend', plantilla.innerHTML.replaceAll('__INDICE__', String(siguiente++)));
            lista.lastElementChild.querySelector('input')?.focus();
        });
        lista.addEventListener('click', (evento) => {
            const boton = evento.target.closest('[data-quitar-repuesto]');
            if (!boton) return;
            boton.closest('[data-fila-repuesto]').remove();
            confirmar('Repuesto quitado del formulario');
        });
    }
});
