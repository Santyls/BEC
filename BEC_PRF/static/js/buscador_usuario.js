/**
 * Buscador de ciudadano por nombre o teléfono, sin recargar la página.
 * Reemplaza los <select> gigantes en Asignar Voluntario, Inscribir (detalle
 * de voluntariado) y Registrar Donación.
 *
 * Uso:
 *   inicializarBuscadorUsuario({
 *     inputBusquedaId: 'usuario_busqueda',
 *     inputOcultoId: 'usuario_id',
 *     resultadosId: 'usuario_resultados',
 *     usuarios: [{id, nombre, apellido_paterno, apellido_materno, telefono, correo}, ...],
 *     formId: 'form-inscribir',                 // opcional: engancha confirmación al enviar
 *     requerido: true,                          // opcional (default true): exige selección
 *     obtenerMensajeConfirmacion: function (u) { // opcional
 *       return `Se inscribirá a ${u.nombre}. ¿Continuar?`;
 *     },
 *   });
 */
function inicializarBuscadorUsuario(opciones) {
    const {
        inputBusquedaId,
        inputOcultoId,
        resultadosId,
        usuarios,
        formId,
        requerido = true,
        obtenerMensajeConfirmacion,
    } = opciones;

    const inputBusqueda = document.getElementById(inputBusquedaId);
    const inputOculto = document.getElementById(inputOcultoId);
    const contenedorResultados = document.getElementById(resultadosId);
    if (!inputBusqueda || !inputOculto || !contenedorResultados) return;

    const TOPE_RESULTADOS = 8;
    let usuarioSeleccionado = null;

    function nombreCompleto(u) {
        return [u.nombre, u.apellido_paterno, u.apellido_materno].filter(Boolean).join(' ');
    }

    function ocultarResultados() {
        contenedorResultados.classList.add('hidden');
        contenedorResultados.innerHTML = '';
    }

    function limpiarSeleccion() {
        usuarioSeleccionado = null;
        inputOculto.value = '';
    }

    function seleccionar(usuario) {
        usuarioSeleccionado = usuario;
        inputOculto.value = usuario.id;
        inputBusqueda.value = nombreCompleto(usuario);
        ocultarResultados();
    }

    inputBusqueda.addEventListener('input', function () {
        limpiarSeleccion();
        const texto = inputBusqueda.value.trim().toLowerCase();
        if (texto.length < 1) {
            ocultarResultados();
            return;
        }

        const coincidencias = usuarios
            .filter(function (u) {
                const telefono = (u.telefono || '').toLowerCase();
                return nombreCompleto(u).toLowerCase().includes(texto) || telefono.includes(texto);
            })
            .slice(0, TOPE_RESULTADOS);

        if (coincidencias.length === 0) {
            contenedorResultados.innerHTML =
                '<div class="px-4 py-3 text-sm text-slate-500 dark:text-slate-400">Sin coincidencias.</div>';
            contenedorResultados.classList.remove('hidden');
            return;
        }

        contenedorResultados.innerHTML = coincidencias
            .map(function (u) {
                return (
                    '<button type="button" data-id="' + u.id + '" class="w-full text-left px-4 py-2.5 hover:bg-slate-100 dark:hover:bg-slate-800 text-sm text-slate-800 dark:text-slate-200 border-b border-slate-100 dark:border-slate-800/50 last:border-0 transition-colors">' +
                    '<span class="font-medium">' + nombreCompleto(u) + '</span>' +
                    '<span class="block text-xs text-slate-500 dark:text-slate-400">Tel. ' + (u.telefono || 'sin teléfono') + (u.correo ? ' · ' + u.correo : '') + '</span>' +
                    '</button>'
                );
            })
            .join('');
        contenedorResultados.classList.remove('hidden');

        contenedorResultados.querySelectorAll('button[data-id]').forEach(function (boton) {
            boton.addEventListener('click', function () {
                const id = boton.getAttribute('data-id');
                const usuario = usuarios.find(function (u) {
                    return String(u.id) === id;
                });
                if (usuario) seleccionar(usuario);
            });
        });
    });

    document.addEventListener('click', function (evento) {
        if (!inputBusqueda.contains(evento.target) && !contenedorResultados.contains(evento.target)) {
            ocultarResultados();
        }
    });

    if (formId) {
        const formulario = document.getElementById(formId);
        if (formulario) {
            // `confirmado` evita un ciclo infinito: el modal es asíncrono, así que
            // al aceptar se vuelve a enviar el formulario y esta vez debe pasar
            // de largo sin volver a preguntar.
            let confirmado = false;

            formulario.addEventListener('submit', function (evento) {
                if (requerido && !inputOculto.value) {
                    evento.preventDefault();
                    alert('Selecciona un ciudadano de la lista de resultados.');
                    return;
                }
                if (confirmado) return;

                if (inputOculto.value && typeof obtenerMensajeConfirmacion === 'function') {
                    const mensaje = obtenerMensajeConfirmacion(usuarioSeleccionado);
                    if (mensaje) {
                        evento.preventDefault();
                        // Usa el modal del portal; si por alguna razón no está
                        // cargado, cae al confirm() del navegador como respaldo.
                        if (typeof window.becConfirmar === 'function') {
                            window.becConfirmar(mensaje, function () {
                                confirmado = true;
                                formulario.submit();
                            });
                        } else if (confirm(mensaje)) {
                            confirmado = true;
                            formulario.submit();
                        }
                    }
                }
            });
        }
    }
}
