/**
 * Alterna entre tema claro (por defecto) y oscuro.
 *
 * El estado inicial ya lo decide un <script> inline en el <head> de
 * layout.html (antes de que cargue Tailwind) leyendo localStorage para
 * evitar el parpadeo de tema incorrecto. Este archivo solo se encarga de
 * reaccionar al clic del botón y de mantener sincronizados el icono y el
 * valor persistido.
 */
(function () {
    var CLAVE_ALMACENAMIENTO = 'bec-theme';

    function inicializar() {
        var boton = document.getElementById('theme-toggle');
        var iconoSol = document.getElementById('theme-toggle-icon-sun');
        var iconoLuna = document.getElementById('theme-toggle-icon-moon');
        if (!boton) return;

        function actualizarIcono() {
            var esOscuro = document.documentElement.classList.contains('dark');
            if (iconoSol) iconoSol.classList.toggle('hidden', !esOscuro);
            if (iconoLuna) iconoLuna.classList.toggle('hidden', esOscuro);
        }

        actualizarIcono();

        boton.addEventListener('click', function () {
            var esOscuro = document.documentElement.classList.toggle('dark');
            localStorage.setItem(CLAVE_ALMACENAMIENTO, esOscuro ? 'dark' : 'light');
            actualizarIcono();
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', inicializar);
    } else {
        inicializar();
    }
})();
