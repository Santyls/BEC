import re

from flask import Blueprint, flash, redirect, render_template, request, url_for

from bec_api_client import BecApiClient, BecApiException
from blueprints.auth import login_requerido
from paginacion import Paginador

bp = Blueprint("usuarios", __name__)

ROL_CIUDADANO = 3
TELEFONO_PATTERN = re.compile(r"^\d{10}$")


@bp.route("/")
@login_requerido
def index():
    api = BecApiClient()
    usuarios = api.get("/usuarios/", {"limit": 200})
    # Los ciudadanos son globales (no pertenecen a un albergue) — el listado del
    # mostrador solo muestra Ciudadanos, no a otro personal de BEC_PAL/BEC_PRF.
    usuarios = [u for u in usuarios if u["rol_id"] == ROL_CIUDADANO]

    busqueda = request.args.get("q", "").strip()
    if busqueda:
        texto = busqueda.lower()
        usuarios = [
            u
            for u in usuarios
            if texto in f"{u['nombre']} {u['apellido_paterno']} {u['apellido_materno']}".lower()
            or texto in (u.get("correo") or "").lower()
            or texto in (u.get("telefono") or "")
        ]

    usuarios = Paginador(usuarios)

    return render_template("usuarios/index.html", usuarios=usuarios, busqueda=busqueda)


def _validar_formulario(form):
    errores = []
    nombre = form.get("nombre", "").strip()
    apellido_paterno = form.get("apellido_paterno", "").strip()
    apellido_materno = form.get("apellido_materno", "").strip()
    telefono = form.get("telefono", "").strip()
    correo = form.get("correo", "").strip()

    if not nombre:
        errores.append("El nombre es obligatorio.")
    if not apellido_paterno:
        errores.append("El apellido paterno es obligatorio.")
    if not apellido_materno:
        errores.append("El apellido materno es obligatorio.")
    if not TELEFONO_PATTERN.match(telefono):
        errores.append("El teléfono debe tener 10 dígitos, sin espacios ni guiones.")

    datos = {
        "nombre": nombre,
        "apellido_paterno": apellido_paterno,
        "apellido_materno": apellido_materno,
        "telefono": telefono,
        "correo": correo or None,
    }
    return datos, errores


@bp.route("/nuevo", methods=["GET", "POST"])
@login_requerido
def crear():
    if request.method == "POST":
        datos, errores = _validar_formulario(request.form)
        if errores:
            for error in errores:
                flash(error, "error")
            return render_template("usuarios/create.html", valores=request.form)

        try:
            BecApiClient().post("/usuarios/", datos)
        except BecApiException as e:
            flash(e.mensaje, "error")
            return render_template("usuarios/create.html", valores=request.form)

        flash("Ciudadano registrado correctamente.", "exito")
        return redirect(url_for("usuarios.index"))

    return render_template("usuarios/create.html", valores={})


@bp.route("/<int:usuario_id>/editar", methods=["GET", "POST"])
@login_requerido
def editar(usuario_id):
    api = BecApiClient()

    if request.method == "POST":
        datos, errores = _validar_formulario(request.form)
        if errores:
            for error in errores:
                flash(error, "error")
            return redirect(url_for("usuarios.editar", usuario_id=usuario_id))

        try:
            api.put(f"/usuarios/{usuario_id}", datos)
        except BecApiException as e:
            flash(e.mensaje, "error")
            return redirect(url_for("usuarios.editar", usuario_id=usuario_id))

        flash("Ciudadano actualizado correctamente.", "exito")
        return redirect(url_for("usuarios.index"))

    try:
        usuario = api.get(f"/usuarios/{usuario_id}")
    except BecApiException as e:
        flash(e.mensaje, "error")
        return redirect(url_for("usuarios.index"))

    if usuario["rol_id"] != ROL_CIUDADANO:
        flash("Solo puedes editar cuentas de Ciudadano.", "error")
        return redirect(url_for("usuarios.index"))

    return render_template("usuarios/edit.html", usuario=usuario)
