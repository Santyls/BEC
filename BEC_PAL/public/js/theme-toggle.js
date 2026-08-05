/**
 * Toggle de tema claro/oscuro.
 *
 * El tema por defecto es siempre claro. Si el usuario alguna vez le da clic al
 * boton #theme-toggle (icono sol/luna en el header), se alterna la clase `dark`
 * en <html> y se guarda la preferencia en localStorage bajo la llave 'bec-theme'.
 * El script inline en layouts/admin.blade.php lee ese valor al cargar la pagina
 * (antes de pintar) para evitar el parpadeo de tema incorrecto.
 */
(function () {
    function init() {
        var toggle = document.getElementById('theme-toggle');
        if (!toggle) return;

        toggle.addEventListener('click', function () {
            var isDark = document.documentElement.classList.toggle('dark');
            try {
                localStorage.setItem('bec-theme', isDark ? 'dark' : 'light');
            } catch (e) {}

            if (window.lucide) {
                lucide.createIcons();
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
