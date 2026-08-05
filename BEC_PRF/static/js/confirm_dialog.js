/**
 * Modal de confirmación reutilizable, reemplaza a window.confirm().
 *
 * Cualquier <form> con el atributo data-confirm="mensaje" mostrará este
 * modal al enviarse. El formulario solo se envía si el usuario confirma;
 * si cancela, el envío se cancela por completo.
 *
 * Uso:
 *   <form method="POST" action="..." data-confirm="¿Seguro que deseas continuar?">
 *
 * El estilo del botón de confirmación (rojo/destructivo o neutro/primario)
 * se decide automáticamente según el texto del mensaje: si menciona una
 * acción destructiva (cancelar, eliminar, quitar, borrar) se usa el estilo
 * rojo; en cualquier otro caso (finalizar, guardar, asignar, reactivar...)
 * se usa el estilo neutro/primario.
 */
(function () {
    var PALABRAS_DESTRUCTIVAS = ['cancelar', 'eliminar', 'quitar', 'borrar'];

    var formPendiente = null;
    // Alternativa a formPendiente: una función a ejecutar si se acepta, usada
    // por window.becConfirmar (ver más abajo).
    var accionPendiente = null;

    function esDestructivo(mensaje) {
        var texto = mensaje.toLowerCase();
        return PALABRAS_DESTRUCTIVAS.some(function (palabra) {
            return texto.indexOf(palabra) !== -1;
        });
    }

    function inicializar() {
        var modal = document.getElementById('confirm-modal');
        if (!modal) return;

        var backdrop = document.getElementById('confirm-modal-backdrop');
        var panel = document.getElementById('confirm-modal-panel');
        var mensajeEl = document.getElementById('confirm-modal-message');
        var iconoDanger = document.getElementById('confirm-modal-icon-danger');
        var iconoInfo = document.getElementById('confirm-modal-icon-info');
        var btnCancelar = document.getElementById('confirm-modal-cancel');
        var btnAceptar = document.getElementById('confirm-modal-accept');

        function mostrar(mensaje) {
            mensajeEl.textContent = mensaje;

            var destructivo = esDestructivo(mensaje);
            iconoDanger.classList.toggle('hidden', !destructivo);
            iconoInfo.classList.toggle('hidden', destructivo);
            btnAceptar.className = 'px-4 py-2 rounded-xl text-sm font-bold text-white transition-colors shadow-lg ' +
                (destructivo ? 'bg-rose-600 hover:bg-rose-500' : 'bg-blue-600 hover:bg-blue-500');

            modal.classList.remove('hidden');
            // Forzar reflow para que la transición de entrada se anime.
            void panel.offsetWidth;
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'scale-95');
        }

        function ocultar() {
            backdrop.classList.add('opacity-0');
            panel.classList.add('opacity-0', 'scale-95');
            window.setTimeout(function () {
                modal.classList.add('hidden');
            }, 150);
            formPendiente = null;
            accionPendiente = null;
        }

        btnCancelar.addEventListener('click', ocultar);
        backdrop.addEventListener('click', ocultar);

        document.addEventListener('keydown', function (evento) {
            if (evento.key === 'Escape' && !modal.classList.contains('hidden')) {
                ocultar();
            }
        });

        btnAceptar.addEventListener('click', function () {
            var form = formPendiente;
            var accion = accionPendiente;
            modal.classList.add('hidden');
            backdrop.classList.add('opacity-0');
            panel.classList.add('opacity-0', 'scale-95');
            formPendiente = null;
            accionPendiente = null;
            if (form) form.submit();
            else if (typeof accion === 'function') accion();
        });

        document.querySelectorAll('form[data-confirm]').forEach(function (form) {
            form.addEventListener('submit', function (evento) {
                evento.preventDefault();
                formPendiente = form;
                mostrar(form.dataset.confirm);
            });
        });

        // Se expone para el código que necesita confirmar algo que no viene de
        // un data-confirm (por ejemplo el buscador de ciudadano, que arma el
        // mensaje con el nombre de la persona elegida). Así todo el portal usa
        // el mismo modal y ya no queda ningún confirm() nativo.
        window.becConfirmar = function (mensaje, alAceptar) {
            formPendiente = null;
            accionPendiente = alAceptar;
            mostrar(mensaje);
        };
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', inicializar);
    } else {
        inicializar();
    }
})();
