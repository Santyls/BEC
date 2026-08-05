/**
 * Agrega un botón de ojo a todos los campos de contraseña para poder ver lo
 * que se escribió (útil sobre todo al capturar una contraseña y su
 * confirmación, para verificar que coincidan antes de guardar).
 *
 * Funciona solo: no hay que tocar los formularios, busca cualquier
 * input[type=password] de la página y lo envuelve.
 */
(function () {
    var OJO =
        '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" ' +
        'stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
        '<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>';
    var OJO_TACHADO =
        '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" ' +
        'stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
        '<path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>' +
        '<path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>' +
        '<path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>' +
        '<line x1="2" x2="22" y1="2" y2="22"/></svg>';

    function inicializar() {
        document.querySelectorAll('input[type="password"]').forEach(function (campo) {
            if (campo.dataset.conOjo) return;
            campo.dataset.conOjo = '1';

            // Se envuelve para poder colocar el botón encima del campo sin
            // alterar el ancho ni el flujo del formulario.
            var envoltura = document.createElement('div');
            envoltura.className = 'relative';
            campo.parentNode.insertBefore(envoltura, campo);
            envoltura.appendChild(campo);
            campo.classList.add('pr-11');

            var boton = document.createElement('button');
            boton.type = 'button';
            boton.tabIndex = -1;
            boton.title = 'Mostrar u ocultar la contraseña';
            boton.className =
                'absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 ' +
                'dark:hover:text-slate-200 transition-colors';
            boton.innerHTML = OJO;
            envoltura.appendChild(boton);

            boton.addEventListener('click', function () {
                var oculto = campo.type === 'password';
                campo.type = oculto ? 'text' : 'password';
                boton.innerHTML = oculto ? OJO_TACHADO : OJO;
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', inicializar);
    } else {
        inicializar();
    }
})();
