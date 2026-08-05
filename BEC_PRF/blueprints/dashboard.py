from datetime import date

from flask import Blueprint, render_template

from bec_api_client import BecApiClient
from blueprints.auth import login_requerido

bp = Blueprint("dashboard", __name__)

ESTADO_PROGRAMADO = 1
ESTADO_ACTIVO = 2


@bp.route("/")
@login_requerido
def inicio():
    api = BecApiClient()

    # Ambas listas ya llegan filtradas al propio albergue — lo fuerza la API
    # según el rol (ver donaciones.py / voluntariados.py, verificar_acceso_albergue).
    donaciones = api.get("/donaciones/")
    voluntariados = api.get("/voluntariados/")

    inicio_mes = date.today().replace(day=1).isoformat()
    donaciones_mes = [d for d in donaciones if d["fecha_donacion"] >= inicio_mes]
    voluntariados_activos = [v for v in voluntariados if v["estado_id"] in (ESTADO_PROGRAMADO, ESTADO_ACTIVO)]
    total_inscritos = sum(v["inscritos"] for v in voluntariados_activos)

    recientes = sorted(
        [
            {
                "texto": f"Donación registrada" + (f" ({d['categoria']['nombre']})" if d.get("categoria") else ""),
                "fecha": d["fecha_donacion"],
                "color": "bg-emerald-500",
            }
            for d in donaciones[:3]
        ]
        + [
            {
                "texto": f"Voluntariado \"{v['nombre_programa']}\" programado.",
                "fecha": v["fecha_programada"],
                "color": "bg-blue-500",
            }
            for v in voluntariados[:3]
        ],
        key=lambda item: item["fecha"],
        reverse=True,
    )[:5]

    return render_template(
        "home.html",
        total_donaciones_mes=len(donaciones_mes),
        voluntariados_activos=len(voluntariados_activos),
        total_inscritos=total_inscritos,
        recientes=recientes,
    )
