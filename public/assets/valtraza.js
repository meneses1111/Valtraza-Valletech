document.addEventListener('DOMContentLoaded', () => {
    const pestana = ['#diagnosticos', '#trabajos', '#repuestos', '#historial'].includes(window.location.hash)
        ? document.querySelector(`[data-bs-target="${window.location.hash}"]`)
        : null;
    if (pestana && window.bootstrap) bootstrap.Tab.getOrCreateInstance(pestana).show();
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
    // Validación mientras se escribe: solo orienta; el servidor sigue validando cada envío.
    const formularios = Array.from(document.querySelectorAll('main form[method="post"]'));
    const camposDe = (formulario) => Array.from(formulario.elements).filter((campo) => campo.willValidate && campo.type !== 'checkbox');
    const etiquetaDe = (campo) => (campo.labels?.[0]?.textContent || campo.name).replace('*', '').replace(/\s*\(opcional\)/i, '').trim();
    const correoSinDominio = (valor) => /^[^@\s]+@[^@\s.]+$/.test(valor.trim());
    const revisarPersonalizado = (campo) => {
        let mensaje = '';
        if (campo.type === 'email' && campo.value.trim() !== '' && correoSinDominio(campo.value)) {
            mensaje = 'Al correo le falta el dominio, por ejemplo .com.';
        }
        if (campo.dataset.confirma) {
            const original = document.getElementById(campo.dataset.confirma);
            if (original && campo.value !== '' && campo.value !== original.value) mensaje = 'Las contraseñas no coinciden.';
        }
        campo.setCustomValidity(mensaje);
    };
    const mensajeDe = (campo) => {
        const v = campo.validity;
        if (v.valueMissing) return campo.dataset.mensajeRequerido || (campo.tagName === 'SELECT' ? 'Elige una opción.' : 'Completa este campo.');
        if (v.customError) return campo.validationMessage;
        if (v.typeMismatch && campo.type === 'email') return campo.value.includes('@') ? 'Revisa el correo electrónico.' : 'Al correo le falta la @.';
        if (v.tooShort) {
            const faltan = campo.minLength - campo.value.length;
            return `Faltan ${faltan} ${faltan === 1 ? 'carácter' : 'caracteres'}.`;
        }
        if (v.tooLong) return 'El valor supera la longitud permitida.';
        if (v.rangeUnderflow) return `El valor mínimo es ${campo.min}.`;
        if (v.rangeOverflow) return 'El valor supera el máximo permitido.';
        if (v.badInput || v.stepMismatch) return campo.type === 'number' ? 'Escribe un número entero.' : 'Revisa el formato del dato.';
        return v.valid ? '' : 'Revisa este dato.';
    };
    const avisoDe = (campo) => {
        const id = `${campo.id}-error`;
        let aviso = document.getElementById(id);
        if (!aviso) {
            aviso = document.createElement('div');
            aviso.id = id;
            aviso.className = 'invalid-feedback';
            campo.insertAdjacentElement('afterend', aviso);
        }
        const descritos = (campo.getAttribute('aria-describedby') || '').split(' ').filter(Boolean);
        if (!descritos.includes(id)) campo.setAttribute('aria-describedby', [...descritos, id].join(' '));
        return aviso;
    };
    const mostrar = (campo) => {
        if (!campo.id) return;
        revisarPersonalizado(campo);
        const mensaje = mensajeDe(campo);
        campo.classList.toggle('is-invalid', mensaje !== '');
        if (mensaje !== '') {
            avisoDe(campo).textContent = mensaje;
            campo.setAttribute('aria-invalid', 'true');
        } else {
            document.getElementById(`${campo.id}-error`)?.replaceChildren();
            campo.removeAttribute('aria-invalid');
        }
    };
    const resumenDe = (formulario) => {
        const boton = formulario.querySelector('button[type="submit"]');
        if (!boton) return null;
        let resumen = formulario.querySelector('[data-falta-para-continuar]');
        if (!resumen) {
            resumen = document.createElement('p');
            resumen.className = 'falta-para-continuar';
            resumen.dataset.faltaParaContinuar = '';
            resumen.setAttribute('aria-live', 'polite');
            boton.insertAdjacentElement('beforebegin', resumen);
        }
        return resumen;
    };
    const resumir = (formulario) => {
        const resumen = resumenDe(formulario);
        if (!resumen) return;
        camposDe(formulario).forEach(revisarPersonalizado);
        const pendientes = [...new Set(camposDe(formulario).filter((campo) => !campo.validity.valid).map(etiquetaDe))];
        resumen.textContent = pendientes.length ? `Para continuar, revisa: ${pendientes.join(', ')}.` : '';
        resumen.hidden = pendientes.length === 0;
    };
    formularios.forEach((formulario) => {
        const tocados = new WeakSet(camposDe(formulario).filter((campo) => campo.classList.contains('is-invalid')));
        let intentado = false;
        const alCambiar = (evento) => {
            const campo = evento.target;
            // Al pulsar enviar no se muestran avisos nuevos: moverían el botón y el clic se perdería.
            if (evento.type === 'focusout' && evento.relatedTarget?.closest?.('button[type="submit"]')) {
                tocados.add(campo);
                return;
            }
            if (!campo.willValidate || campo.type === 'checkbox') {
                if (intentado) resumir(formulario);
                return;
            }
            if (evento.type === 'focusout') tocados.add(campo);
            if (tocados.has(campo)) mostrar(campo);
            if (campo.type === 'password') {
                formulario.querySelectorAll('[data-confirma]').forEach((confirmacion) => { if (tocados.has(confirmacion)) mostrar(confirmacion); });
            }
            if (intentado) resumir(formulario);
        };
        formulario.addEventListener('input', alCambiar);
        formulario.addEventListener('change', alCambiar);
        formulario.addEventListener('focusout', alCambiar);
        formulario.addEventListener('invalid', (evento) => {
            evento.preventDefault();
            tocados.add(evento.target);
            mostrar(evento.target);
            if (!intentado) {
                intentado = true;
                setTimeout(() => { resumir(formulario); formulario.querySelector('.is-invalid')?.focus(); });
            }
        }, true);
        formulario.addEventListener('submit', () => camposDe(formulario).forEach(revisarPersonalizado), true);
    });
    const reglas = document.querySelector('[data-reglas-contrasena]');
    const nueva = document.getElementById('password');
    const confirmacion = document.getElementById('password_confirmation');
    if (reglas && nueva && confirmacion) {
        const marcar = (regla, cumple) => {
            const item = reglas.querySelector(`[data-regla="${regla}"]`);
            item?.classList.toggle('cumple', cumple);
            item?.setAttribute('aria-label', `${item.textContent}: ${cumple ? 'cumplido' : 'pendiente'}`);
        };
        const evaluar = () => {
            marcar('longitud', nueva.value.length >= 8);
            marcar('coincide', nueva.value !== '' && nueva.value === confirmacion.value);
        };
        nueva.addEventListener('input', evaluar);
        confirmacion.addEventListener('input', evaluar);
        evaluar();
    }
    const sinFallas = document.querySelector('[data-sin-fallas]');
    const resultado = document.getElementById('resultado');
    if (sinFallas && resultado) {
        const actualizar = () => {
            resultado.disabled = sinFallas.checked;
            resultado.required = !sinFallas.checked;
            if (sinFallas.checked) {
                resultado.classList.remove('is-invalid');
                document.getElementById('resultado-error')?.replaceChildren();
            }
        };
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
