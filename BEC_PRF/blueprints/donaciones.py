from datetime import date, timedelta

from flask import Blueprint, flash, redirect, render_template, request, url_for

from bec_api_client import BecApiClient, BecApiException
from blueprints.auth import login_requerido
from paginacion import Paginador

bp = Blueprint("donaciones", __name__)

ROL_CIUDADANO = 3
PERIODOS_VALIDOS = {"semana", "mes", "rango"}


def _rango_periodo(periodo, desde_str, hasta_str):
    """Traduce el filtro elegido a un (inicio, fin) de fechas, o (None, None) si no
    aplica ninguno. 'semana'/'mes' se calculan sobre la fecha de hoy; 'rango' usa lo
    que haya escrito el usuario (ignora valores que no sean una fecha válida)."""
    hoy = date.today()
    if periodo == "semana":
        inicio = hoy - timedelta(days=hoy.weekday())
        return inicio, inicio + timedelta(days=6)
    if periodo == "mes":
        inicio = hoy.replace(day=1)
        fin = (inicio.replace(day=28) + timedelta(days=4)).replace(day=1) - timedelta(days=1)
        return inicio, fin
    if periodo == "rango":
        try:
            inicio = date.fromisoformat(desde_str) if desde_str else None
        except ValueError:
            inicio = None
        try:
            fin = date.fromisoformat(hasta_str) if hasta_str else None
        except ValueError:
            fin = None
        return inicio, fin
    return None, None


@bp.route("/")
@login_requerido
def index():
    api = BecApiClient()
    # Ya llega filtrado al propio albergue — lo fuerza la API según el rol.
    filtros = {}
    categoria_id = request.args.get("categoria_id")
    if categoria_id:
        filtros["categoria_id"] = categoria_id
    donaciones = api.get("/donaciones/", filtros)
    categorias = api.get("/catalogos/categorias")

    busqueda = request.args.get("q", "").strip()
    if busqueda:
        texto = busqueda.lower()
        donaciones = [
            d
            for d in donaciones
            if texto in (d.get("marca") or "").lower()
            or texto in (d["categoria"]["nombre"].lower() if d.get("categoria") else "")
            or (d.get("usuario") and texto in f"{d['usuario']['nombre']} {d['usuario']['apellido_paterno']}".lower())
        ]

    periodo = request.args.get("periodo", "").strip()
    desde = request.args.get("desde", "").strip()
    hasta = request.args.get("hasta", "").strip()
    if periodo in PERIODOS_VALIDOS:
        inicio, fin = _rango_periodo(periodo, desde, hasta)
        if inicio or fin:
            donaciones = [
                d
                for d in donaciones
                if (not inicio or date.fromisoformat(d["fecha_donacion"]) >= inicio)
                and (not fin or date.fromisoformat(d["fecha_donacion"]) <= fin)
            ]
    else:
        periodo = ""

    donaciones = Paginador(donaciones)

    return render_template(
        "donaciones/index.html",
        donaciones=donaciones,
        categorias=categorias,
        busqueda=busqueda,
        categoria_id=categoria_id,
        periodo=periodo,
        desde=desde,
        hasta=hasta,
        # Tope de los selectores de fecha: no tiene sentido filtrar a futuro.
        hoy=date.today().isoformat(),
    )


@bp.route("/nueva", methods=["GET", "POST"])
@login_requerido
def crear():
    api = BecApiClient()

    if request.method == "POST":
        datos = {
            "usuario_id": request.form.get("usuario_id") or None,
            "categoria_id": request.form.get("categoria_id"),
            "condicion_id": request.form.get("condicion_id"),
            "cantidad": request.form.get("cantidad"),
            "unidad_id": request.form.get("unidad_id"),
            "marca": request.form.get("marca") or None,
        }
        try:
            api.post("/donaciones/", datos)
        except BecApiException as e:
            flash(e.mensaje, "error")
            return redirect(url_for("donaciones.crear"))

        flash("Donación registrada correctamente.", "exito")
        return redirect(url_for("donaciones.index"))

    usuarios = [u for u in api.get("/usuarios/", {"limit": 200}) if u["rol_id"] == ROL_CIUDADANO]
    categorias = api.get("/catalogos/categorias")
    condiciones = api.get("/catalogos/condiciones")
    unidades = api.get("/catalogos/unidades")

    return render_template(
        "donaciones/create.html", usuarios=usuarios, categorias=categorias, condiciones=condiciones, unidades=unidades
    )
