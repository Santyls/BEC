import re

from flask import Blueprint, flash, redirect, render_template, request, session, url_for

from bec_api_client import BecApiClient, BecApiException
from blueprints.auth import login_requerido

bp = Blueprint("perfil", __name__)

TELEFONO_PATTERN = re.compile(r"^\d{10}$")
EXTENSIONES_PERMITIDAS = {"image/jpeg", "image/png", "image/webp"}


@bp.route("/")
@login_requerido
def index():
    api = BecApiClient()
    usuario = api.get("/usuarios/me")
    roles = {r["id"]: r for r in api.get("/catalogos/roles")}
    return render_template("perfil/index.html", usuario=usuario, roles=roles)


@bp.route("/telefono", methods=["POST"])
@login_requerido
def actualizar_telefono():
    telefono = request.form.get("telefono", "").strip()
    if not TELEFONO_PATTERN.match(telefono):
        flash("El teléfono debe tener 10 dígitos, sin espacios ni guiones.", "error")
        return redirect(url_for("perfil.index"))

    try:
        usuario = BecApiClient().put("/usuarios/me", {"telefono": telefono})
    except BecApiException as e:
        flash(e.mensaje, "error")
        return redirect(url_for("perfil.index"))

    session["bec_user"] = usuario
    flash("Teléfono actualizado correctamente.", "exito")
    return redirect(url_for("perfil.index"))


@bp.route("/password", methods=["POST"])
@login_requerido
def actualizar_password():
    password_actual = request.form.get("password_actual", "")
    password_nueva = request.form.get("password_nueva", "")
    password_confirmacion = request.form.get("password_nueva_confirmation", "")

    if len(password_nueva) < 6:
        flash("La nueva contraseña debe tener al menos 6 caracteres.", "error")
        return redirect(url_for("perfil.index"))
    if password_nueva != password_confirmacion:
        flash("La confirmación no coincide con la nueva contraseña.", "error")
        return redirect(url_for("perfil.index"))

    try:
        BecApiClient().put(
            "/usuarios/me/password",
            {"password_actual": password_actual, "password_nueva": password_nueva},
        )
    except BecApiException as e:
        flash(e.mensaje, "error")
        return redirect(url_for("perfil.index"))

    flash("Contraseña actualizada correctamente.", "exito")
    return redirect(url_for("perfil.index"))


@bp.route("/foto", methods=["POST"])
@login_requerido
def actualizar_foto():
    archivo = request.files.get("foto")
    if not archivo or not archivo.filename:
        flash("Selecciona una imagen.", "error")
        return redirect(url_for("perfil.index"))
    if archivo.content_type not in EXTENSIONES_PERMITIDAS:
        flash("Formato no soportado. Usa JPEG, PNG o WEBP.", "error")
        return redirect(url_for("perfil.index"))

    try:
        usuario = BecApiClient().post_file("/usuarios/me/foto", "archivo", archivo)
    except BecApiException as e:
        flash(e.mensaje, "error")
        return redirect(url_for("perfil.index"))

    session["bec_user"] = usuario
    flash("Foto de perfil actualizada correctamente.", "exito")
    return redirect(url_for("perfil.index"))
