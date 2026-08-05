/**
 * Menú lateral deslizable en pantallas chicas.
 *
 * En escritorio (lg+) la barra es fija y este archivo no hace nada visible:
 * las clases `lg:flex lg:static` de Tailwind mandan sobre el `hidden`. En móvil
 * la barra está oculta y se muestra/oculta al tocar la hamburguesa o el fondo.
 *
 * Se alterna `display` (hidden) en vez de moverla con translate/left: esas
 * utilidades no se aplicaban de forma confiable sobre este elemento en este
 * montaje (CDN de Tailwind + .glass con backdrop-filter), y la barra se
 * quedaba encima del contenido tapándolo.
 */
(function () {
    function inicializar() {
        var boton = document.getElementById('sidebar-toggle');
        var barra = document.getElementById('sidebar');
        var fondo = document.getElementById('sidebar-backdrop');
        if (!boton || !barra || !fondo) return;

        function abrir() {
            barra.classList.remove('hidden');
            fondo.classList.remove('hidden');
        }

        function cerrar() {
            barra.classList.add('hidden');
            fondo.classList.add('hidden');
        }

        boton.addEventListener('click', function () {
            if (barra.classList.contains('hidden')) abrir();
            else cerrar();
        });

        fondo.addEventListener('click', cerrar);

        // Al navegar a otra sección desde el menú, ciérralo — si no, en móvil
        // la página nueva aparece con la barra encima tapando el contenido.
        barra.querySelectorAll('a').forEach(function (enlace) {
            enlace.addEventListener('click', cerrar);
        });

        // Si el usuario gira el teléfono o agranda la ventana hasta escritorio,
        // el fondo oscuro debe desaparecer (la barra ya es visible por CSS).
        window.addEventListener('resize', function () {
            if (window.innerWidth >= 1024) cerrar();
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', inicializar);
    } else {
        inicializar();
    }
})();
