/**
 * Modal de confirmacion reutilizable para reemplazar el confirm() nativo del navegador.
 *
 * Uso: en vez de <form onsubmit="return confirm('...')">, usa
 * <form data-confirm="Mensaje a mostrar...">. Este script intercepta el submit,
 * muestra el modal (definido en layouts/admin.blade.php) y solo envia el form
 * si el usuario confirma.
 */
(function () {
    var modal, backdrop, card, messageEl, cancelBtn, acceptBtn;
    var pendingForm = null;

    function init() {
        modal = document.getElementById('confirm-modal');
        if (!modal) return;

        backdrop = document.getElementById('confirm-modal-backdrop');
        card = document.getElementById('confirm-modal-card');
        messageEl = document.getElementById('confirm-modal-message');
        cancelBtn = document.getElementById('confirm-modal-cancel');
        acceptBtn = document.getElementById('confirm-modal-accept');

        cancelBtn.addEventListener('click', closeModal);
        backdrop.addEventListener('click', closeModal);
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
        });

        acceptBtn.addEventListener('click', function () {
            var form = pendingForm;
            closeModal();
            if (form) form.submit();
        });

        // Se delega en document para que funcione con forms renderizados dinamicamente.
        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!(form instanceof HTMLFormElement)) return;

            var mensaje = form.getAttribute('data-confirm');
            if (mensaje === null) return;

            e.preventDefault();
            pendingForm = form;
            messageEl.textContent = mensaje;

            // Heuristica simple: acciones destructivas (desactivar/eliminar/cancelar/vetar)
            // usan boton rojo; el resto (reactivar, quitar veto, confirmaciones neutrales)
            // usa el boton primario azul.
            var destructivo = /desactiv|eliminar|cancel|vetar a|finalizada/i.test(mensaje);
            acceptBtn.className = 'px-5 py-2.5 rounded-xl text-sm font-bold text-white transition-colors shadow-lg ' +
                (destructivo ? 'bg-rose-600 hover:bg-rose-500' : 'bg-blue-600 hover:bg-blue-500');

            openModal();
        });
    }

    function openModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        requestAnimationFrame(function () {
            card.classList.remove('scale-95', 'opacity-0');
            card.classList.add('scale-100', 'opacity-100');
        });
    }

    function closeModal() {
        card.classList.remove('scale-100', 'opacity-100');
        card.classList.add('scale-95', 'opacity-0');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        pendingForm = null;
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
