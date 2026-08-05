from datetime import date

from flask import Blueprint, flash, redirect, render_template, request, session, url_for

from bec_api_client import BecApiClient, BecApiException
from blueprints.auth import login_requerido
from paginacion import Paginador

bp = Blueprint("voluntariados", __name__)

ROL_CIUDADANO = 3
ROL_RECEPCIONISTA = 2
ESTADO_PROGRAMADO = 1
ESTADO_ACTIVO = 2


@bp.route("/")
@login_requerido
def index():
    api = BecApiClient()
    voluntariados = api.get("/voluntariados/")  # ya viene filtrado a propio albergue + independientes

    busqueda = request.args.get("q", "").strip()
    if busqueda:
        texto = busqueda.lower()
        voluntariados = [v for v in voluntariados if texto in v["nombre_programa"].lower()]

    estados = {e["id"]: e for e in api.get("/catalogos/estados-voluntariado")}
    voluntariados = Paginador(voluntariados)
    return render_template("voluntariados/index.html", voluntariados=voluntariados, estados=estados, busqueda=busqueda)


def _validar_formulario(form):
    errores = []
    nombre_programa = form.get("nombre_programa", "").strip()
    fecha_programada = form.get("fecha_programada", "").strip()
    hora_inicio = form.get("hora_inicio", "").strip()
    hora_fin = form.get("hora_fin", "").strip()
    descripcion_requisitos = form.get("descripcion_requisitos", "").strip()
    cupo_maximo = form.get("cupo_maximo", "").strip()
    ubicacion = form.get("ubicacion", "").strip()
    campana_id = form.get("campana_id", "").strip()

    if len(nombre_programa) < 3:
        errores.append("El nombre de la actividad debe tener al menos 3 caracteres.")
    if not fecha_programada:
        errores.append("La fecha programada es obligatoria.")
    if not hora_inicio or not hora_fin:
        errores.append("La hora de inicio y fin son obligatorias.")
    elif hora_fin <= hora_inicio:
        errores.append("La hora de fin debe ser posterior a la de inicio.")
    if not descripcion_requisitos:
        errores.append("La descripción y requisitos son obligatorios.")

    datos = {
        "nombre_programa": nombre_programa,
        "fecha_programada": fecha_programada,
        "hora_inicio": hora_inicio,
        "hora_fin": hora_fin,
        "descripcion_requisitos": descripcion_requisitos,
        "cupo_maximo": int(cupo_maximo) if cupo_maximo else None,
        "ubicacion": ubicacion or None,
        "campana_id": int(campana_id) if campana_id else None,
    }
    return datos, errores


@bp.route("/nuevo", methods=["GET", "POST"])
@login_requerido
def crear():
    api = BecApiClient()

    if request.method == "POST":
        datos, errores = _validar_formulario(request.form)
        if errores:
            for error in errores:
                flash(error, "error")
            return redirect(url_for("voluntariados.crear"))

        try:
            # albergue_id NO se manda: la API lo fuerza al propio del Recepcionista.
            api.post("/voluntariados/", datos)
        except BecApiException as e:
            flash(e.mensaje, "error")
            return redirect(url_for("voluntariados.crear"))

        flash("Voluntariado registrado correctamente.", "exito")
        return redirect(url_for("voluntariados.index"))

    campanas = api.get("/campanas/")
    # `hoy` limita el selector de fecha: no se puede programar en el pasado
    # (la API lo rechaza igual, esto solo evita que el usuario lo intente).
    return render_template("voluntariados/create.html", campanas=campanas, hoy=date.today().isoformat())


@bp.route("/asignar", methods=["GET", "POST"])
@login_requerido
def asignar():
    api = BecApiClient()

    if request.method == "POST":
        usuario_id = request.form.get("usuario_id")
        voluntariado_id = request.form.get("voluntariado_id")
        if not usuario_id or not voluntariado_id:
            flash("Selecciona un ciudadano y un voluntariado.", "error")
            return redirect(url_for("voluntariados.asignar"))

        try:
            api.post("/inscripciones/", {"voluntariado_id": int(voluntariado_id), "usuario_id": int(usuario_id)})
        except BecApiException as e:
            flash(e.mensaje, "error")
            return redirect(url_for("voluntariados.asignar"))

        flash("Ciudadano inscrito correctamente.", "exito")
        return redirect(url_for("voluntariados.index"))

    usuarios = [u for u in api.get("/usuarios/", {"limit": 200}) if u["rol_id"] == ROL_CIUDADANO]
    voluntariados = [v for v in api.get("/voluntariados/") if v["estado_id"] in (ESTADO_PROGRAMADO, ESTADO_ACTIVO)]
    return render_template("voluntariados/asignar.html", usuarios=usuarios, voluntariados=voluntariados)


@bp.route("/<int:voluntariado_id>")
@login_requerido
def mostrar(voluntariado_id):
    api = BecApiClient()
    try:
        voluntariado = api.get(f"/voluntariados/{voluntariado_id}")
        inscritos = api.get(f"/voluntariados/{voluntariado_id}/inscritos")
        encargados = api.get(f"/voluntariados/{voluntariado_id}/encargados")
    except BecApiException as e:
        flash(e.mensaje, "error")
        return redirect(url_for("voluntariados.index"))

    estados = {e["id"]: e for e in api.get("/catalogos/estados-voluntariado")}
    estados_inscripcion = {e["id"]: e for e in api.get("/catalogos/estados-inscripcion")}
    campana = api.get(f"/campanas/{voluntariado['campana_id']}") if voluntariado.get("campana_id") else None
    usuarios = [u for u in api.get("/usuarios/", {"limit": 200}) if u["rol_id"] == ROL_CIUDADANO]

    yo_id = session["bec_user"]["id"]
    ids_encargados = {e["usuario"]["id"] for e in encargados}
    es_encargado = yo_id in ids_encargados
    # Mismo criterio de respaldo que _puede_gestionar_asistencia en la API: si el
    # voluntariado todavía no tiene ningún encargado, cualquier recepcionista con
    # acceso puede pasar lista/finalizar.
    puede_pasar_lista = es_encargado or not encargados

    recepcionistas_disponibles = [
        u for u in api.get("/usuarios/", {"limit": 200})
        if u["rol_id"] == ROL_RECEPCIONISTA
        and u.get("albergue_id") == session["bec_user"]["albergue_id"]
        and u["id"] not in ids_encargados
    ]

    return render_template(
        "voluntariados/show.html",
        voluntariado=voluntariado,
        inscritos=inscritos,
        estados=estados,
        estados_inscripcion=estados_inscripcion,
        campana=campana,
        usuarios=usuarios,
        encargados=encargados,
        recepcionistas_disponibles=recepcionistas_disponibles,
        puede_pasar_lista=puede_pasar_lista,
    )


@bp.route("/<int:voluntariado_id>/editar", methods=["GET", "POST"])
@login_requerido
def editar(voluntariado_id):
    api = BecApiClient()

    if request.method == "POST":
        datos, errores = _validar_formulario(request.form)
        estado_id = request.form.get("estado_id")
        if estado_id:
            datos["estado_id"] = int(estado_id)

        if errores:
            for error in errores:
                flash(error, "error")
            return redirect(url_for("voluntariados.editar", voluntariado_id=voluntariado_id))

        try:
            api.put(f"/voluntariados/{voluntariado_id}", datos)
        except BecApiException as e:
            flash(e.mensaje, "error")
            return redirect(url_for("voluntariados.editar", voluntariado_id=voluntariado_id))

        flash("Voluntariado actualizado correctamente.", "exito")
        return redirect(url_for("voluntariados.index"))

    try:
        voluntariado = api.get(f"/voluntariados/{voluntariado_id}")
    except BecApiException as e:
        flash(e.mensaje, "error")
        return redirect(url_for("voluntariados.index"))

    campanas = api.get("/campanas/")
    estados = api.get("/catalogos/estados-voluntariado")
    return render_template("voluntariados/edit.html", voluntariado=voluntariado, campanas=campanas, estados=estados)


@bp.route("/<int:voluntariado_id>/cancelar", methods=["POST"])
@login_requerido
def cancelar(voluntariado_id):
    try:
        BecApiClient().delete(f"/voluntariados/{voluntariado_id}")
    except BecApiException as e:
        flash(e.mensaje, "error")
        return redirect(url_for("voluntariados.index"))

    flash("Voluntariado cancelado correctamente.", "exito")
    return redirect(url_for("voluntariados.index"))


@bp.route("/<int:voluntariado_id>/finalizar", methods=["POST"])
@login_requerido
def finalizar(voluntariado_id):
    try:
        BecApiClient().post(f"/voluntariados/{voluntariado_id}/finalizar")
    except BecApiException as e:
        flash(e.mensaje, "error")
        return redirect(request.referrer or url_for("voluntariados.index"))

    flash("Voluntariado finalizado. Los inscritos que seguían activos quedaron como completados.", "exito")
    return redirect(request.referrer or url_for("voluntariados.index"))


@bp.route("/<int:voluntariado_id>/inscribir", methods=["POST"])
@login_requerido
def inscribir_desde_detalle(voluntariado_id):
    usuario_id = request.form.get("usuario_id")
    if not usuario_id:
        flash("Selecciona un ciudadano.", "error")
        return redirect(url_for("voluntariados.mostrar", voluntariado_id=voluntariado_id))

    try:
        BecApiClient().post("/inscripciones/", {"voluntariado_id": voluntariado_id, "usuario_id": int(usuario_id)})
    except BecApiException as e:
        flash(e.mensaje, "error")
        return redirect(url_for("voluntariados.mostrar", voluntariado_id=voluntariado_id))

    flash("Ciudadano inscrito correctamente.", "exito")
    return redirect(url_for("voluntariados.mostrar", voluntariado_id=voluntariado_id))


@bp.route("/<int:voluntariado_id>/inscripciones/<int:inscripcion_id>/cancelar", methods=["POST"])
@login_requerido
def cancelar_inscripcion(voluntariado_id, inscripcion_id):
    try:
        BecApiClient().put(f"/inscripciones/{inscripcion_id}/cancelar")
    except BecApiException as e:
        flash(e.mensaje, "error")
        return redirect(url_for("voluntariados.mostrar", voluntariado_id=voluntariado_id))

    flash("Inscripción cancelada correctamente.", "exito")
    return redirect(url_for("voluntariados.mostrar", voluntariado_id=voluntariado_id))


@bp.route("/<int:voluntariado_id>/inscripciones/<int:inscripcion_id>/asistencia", methods=["POST"])
@login_requerido
def marcar_asistencia(voluntariado_id, inscripcion_id):
    asistio = request.form.get("asistio") == "1"
    try:
        BecApiClient().put(f"/inscripciones/{inscripcion_id}/asistencia", {"asistio": asistio})
    except BecApiException as e:
        flash(e.mensaje, "error")
        return redirect(url_for("voluntariados.mostrar", voluntariado_id=voluntariado_id))

    flash("Asistencia registrada." if asistio else "Se registró como no asistió.", "exito")
    return redirect(url_for("voluntariados.mostrar", voluntariado_id=voluntariado_id))


@bp.route("/<int:voluntariado_id>/encargados", methods=["POST"])
@login_requerido
def asignar_encargado(voluntariado_id):
    usuario_id = request.form.get("usuario_id")
    if not usuario_id:
        flash("Selecciona un recepcionista para asignar como encargado.", "error")
        return redirect(url_for("voluntariados.mostrar", voluntariado_id=voluntariado_id))

    try:
        BecApiClient().post(f"/voluntariados/{voluntariado_id}/encargados", {"usuario_id": int(usuario_id)})
    except BecApiException as e:
        flash(e.mensaje, "error")
        return redirect(url_for("voluntariados.mostrar", voluntariado_id=voluntariado_id))

    flash("Encargado asignado correctamente.", "exito")
    return redirect(url_for("voluntariados.mostrar", voluntariado_id=voluntariado_id))


@bp.route("/<int:voluntariado_id>/encargados/<int:usuario_id>/quitar", methods=["POST"])
@login_requerido
def quitar_encargado(voluntariado_id, usuario_id):
    try:
        BecApiClient().delete(f"/voluntariados/{voluntariado_id}/encargados/{usuario_id}")
    except BecApiException as e:
        flash(e.mensaje, "error")
        return redirect(url_for("voluntariados.mostrar", voluntariado_id=voluntariado_id))

    flash("Encargado removido correctamente.", "exito")
    return redirect(url_for("voluntariados.mostrar", voluntariado_id=voluntariado_id))


@bp.route("/mis-encargos")
@login_requerido
def mis_encargos():
    try:
        voluntariados = BecApiClient().get("/voluntariados/mis-encargos")
    except BecApiException as e:
        flash(e.mensaje, "error")
        voluntariados = []

    estados = {e["id"]: e for e in BecApiClient().get("/catalogos/estados-voluntariado")}
    return render_template("voluntariados/mis_encargos.html", voluntariados=voluntariados, estados=estados)
