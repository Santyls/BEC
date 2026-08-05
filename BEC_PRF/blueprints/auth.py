from functools import wraps

from flask import Blueprint, flash, redirect, render_template, request, session, url_for

from bec_api_client import BecApiClient, BecApiException

bp = Blueprint("auth", __name__)

ROL_RECEPCIONISTA = 2


def login_requerido(vista):
    """Protege una ruta: exige sesión activa con un access_token de BEC_API.
    No hay tabla de usuarios local — 'estar autenticado' es simplemente tener
    ese token guardado (mismo criterio que BecAuthenticated en BEC_PAL)."""

    @wraps(vista)
    def envoltura(*args, **kwargs):
        if not session.get("bec_access_token"):
            return redirect(url_for("auth.mostrar_login"))
        return vista(*args, **kwargs)

    return envoltura


@bp.route("/login", methods=["GET"])
def mostrar_login():
    if session.get("bec_access_token"):
        return redirect(url_for("dashboard.inicio"))
    return render_template("auth/login.html")


@bp.route("/login", methods=["POST"])
def iniciar_sesion():
    correo = request.form.get("correo", "").strip()
    password = request.form.get("password", "")

    api = BecApiClient()
    try:
        tokens = api.post_form("/auth/login?plataforma=web", {"username": correo, "password": password})
    except BecApiException as e:
        flash(e.mensaje, "error")
        return redirect(url_for("auth.mostrar_login"))

    session["bec_access_token"] = tokens["access_token"]
    session["bec_refresh_token"] = tokens["refresh_token"]

    try:
        usuario = api.get("/usuarios/me")
    except BecApiException as e:
        session.clear()
        flash(e.mensaje, "error")
        return redirect(url_for("auth.mostrar_login"))

    if usuario.get("rol_id") != ROL_RECEPCIONISTA:
        session.clear()
        flash("Este portal es exclusivo para recepcionistas.", "error")
        return redirect(url_for("auth.mostrar_login"))

    if not usuario.get("albergue_id"):
        session.clear()
        flash("Tu cuenta no tiene un albergue asignado; contacta a un Administrador.", "error")
        return redirect(url_for("auth.mostrar_login"))

    session["bec_user"] = usuario
    return redirect(url_for("dashboard.inicio"))


@bp.route("/logout", methods=["POST"])
def cerrar_sesion():
    refresh = session.get("bec_refresh_token")
    if refresh:
        try:
            BecApiClient().post("/auth/logout", {"refresh_token": refresh})
        except BecApiException:
            pass  # cerrar sesión debe ser idempotente, igual que en BEC_API
    session.clear()
    return redirect(url_for("auth.mostrar_login"))
