from flask import Flask, render_template, request, session, redirect, url_for, flash, jsonify
import requests
import os

app = Flask(__name__)
app.secret_key = os.getenv('SECRET_KEY', 'super_secreta_pcf_2026')
API_URL = os.getenv('BEC_API_URL', 'http://bec_api_app:5000')

# ==========================================
# RUTAS DE LOGIN Y AUTENTICACIÓN
# ==========================================
@app.route('/login', methods=['GET', 'POST'])
def login():
    if 'jwt_token' in session:
        return redirect(url_for('ciudadano_home'))

    form_data = None
    if request.method == 'POST':
        correo   = request.form.get('correo', '').strip()
        password = request.form.get('password', '')
        form_data = {'correo': correo}

        try:
            r = requests.post(f"{API_URL}/login", data={
                'username': correo,
                'password': password
            }, timeout=5)

            if r.status_code == 200:
                data = r.json()
                session['jwt_token'] = data.get('access_token')
                session['correo'] = correo
                return redirect(url_for('ciudadano_home'))
            else:
                flash("El correo o contraseña no son correctos.", "error")
        except Exception:
            flash("Error conectando con la plataforma BEC.", "error")

    return render_template('login.html', form_data=form_data)


# ==========================================
# REGISTRO DE NUEVO CIUDADANO
# ==========================================
@app.route('/registro', methods=['GET', 'POST'])
def register():
    """Permite crear una cuenta nueva de ciudadano a través de la API."""
    if 'jwt_token' in session:
        return redirect(url_for('ciudadano_home'))

    if request.method == 'GET':
        return render_template('register.html')

    # ── POST: procesar el formulario de registro ──
    nombre      = request.form.get('Nombre', '').strip()
    apellido_p  = request.form.get('Apellido_P', '').strip()
    apellido_m  = request.form.get('Apellido_M', '').strip()
    correo      = request.form.get('Correo', '').strip()
    password    = request.form.get('password', '')
    confirm     = request.form.get('password_confirmation', '')

    # Datos del form para re-llenar el formulario en caso de error
    form_data = {
        'Nombre':     nombre,
        'Apellido_P': apellido_p,
        'Apellido_M': apellido_m,
        'Correo':     correo,
    }
    errors = {}

    # ── Validaciones locales ──
    if not nombre:
        errors['Nombre'] = 'El nombre es obligatorio.'
    if not apellido_p:
        errors['Apellido_P'] = 'El apellido paterno es obligatorio.'
    if not correo:
        errors['Correo'] = 'El correo electrónico es obligatorio.'
    if len(password) < 6:
        errors['password'] = 'La contraseña debe tener al menos 6 caracteres.'
    elif password != confirm:
        errors['password'] = 'Las contraseñas no coinciden.'

    if errors:
        return render_template('register.html', form_data=form_data, errors=errors)

    # ── Llamada a la API: POST /usuarios/ ──
    # El endpoint de crear usuario requiere un Id_Direccion obligatorio.
    # Como ciudadano nuevo no tiene dirección, primero creamos una dirección
    # "vacía" de placeholder con una colonia conocida, o pedimos a la API
    # una dirección por defecto. Lo más limpio es pedir Id_Direccion=1 si existe.
    # NOTA: Adaptarlo si la API permite Id_Direccion nullable en creación.
    payload = {
        "Nombre":       nombre,
        "Apellido_P":   apellido_p,
        "Apellido_M":   apellido_m if apellido_m else nombre,   # fallback
        "Correo":       correo,
        "Password":     password,
        "Edad":         18,          # valor por defecto; el usuario lo edita en perfil
        "Id_Direccion": 1,           # dirección provisional (1 = registro inicial)
        "Id_Albergue":  None,
        "Id_Rol":       3,           # 3 = Ciudadano (forzado en portal ciudadano)
        "Tel":          0,           # teléfono provisional; el usuario lo edita en perfil
        "Id_Genero":    1,           # género provisional; el usuario lo edita en perfil
    }

    try:
        # El endpoint POST /usuarios/ requiere autenticación de recepcionista o admin.
        # Usamos el endpoint de registro público si existe, si no, se necesita
        # un token de servicio configurado como variable de entorno.
        SERVICE_TOKEN = os.getenv('SERVICE_TOKEN', '')
        headers_reg = {}
        if SERVICE_TOKEN:
            headers_reg['Authorization'] = f"Bearer {SERVICE_TOKEN}"

        r = requests.post(
            f"{API_URL}/usuarios/",
            json=payload,
            headers=headers_reg,
            timeout=5
        )

        if r.status_code == 201:
            flash("¡Cuenta creada exitosamente! Por favor, inicia sesión.", "success")
            return redirect(url_for('login'))
        else:
            # Capturar mensaje de error de la API
            try:
                api_msg = r.json()
                detail = api_msg.get('detail', str(api_msg))
            except Exception:
                detail = f"Error {r.status_code} al registrarte. Intenta de nuevo."
            return render_template('register.html', form_data=form_data, api_error=detail)

    except Exception as e:
        return render_template('register.html', form_data=form_data,
                               api_error=f"No se pudo conectar con el servidor: {str(e)}")

@app.route('/logout', methods=['POST'])
def logout():
    session.clear()
    return redirect(url_for('ciudadano_home'))

# ==========================================
# CONSUMO DE API: RUTAS PÚBLICAS / INFORMATIVAS
# ==========================================
@app.route('/')
def ciudadano_home():
    """Consume /campanas de la API y las muestra en la vista pública"""
    campanas_reales = []
    
    try:
        headers = {}
        if 'jwt_token' in session:
            headers['Authorization'] = f"Bearer {session.get('jwt_token')}"
            
        r = requests.get(f"{API_URL}/campanas/", headers=headers, timeout=5)
        
        if r.status_code == 200:
            campanas_reales = r.json()
            
    except Exception as e:
        print(f"Error fetching campanas: {e}")

    return render_template('ciudadano_home.html', campanas=campanas_reales)

@app.route('/albergues')
def ciudadano_albergues():
    """Directorio de Albergues consumiendo de la API"""
    albergues = []
    try:
        headers = {}
        if 'jwt_token' in session:
            headers['Authorization'] = f"Bearer {session.get('jwt_token')}"
            
        r = requests.get(f"{API_URL}/albergues/", headers=headers, timeout=5)
        
        if r.status_code == 200:
            albergues = r.json()
    except Exception:
        pass

    return render_template('ciudadano_albergues.html', albergues=albergues)

# ==========================================
# RUTAS PRIVADAS (Requieren sesión)
# ==========================================

@app.route('/mi-perfil', methods=['GET', 'POST'])
def ciudadano_perfil():
    """
    GET:  Carga el perfil del usuario logueado + catálogo de estados.
          Si el usuario tiene dirección, hace una llamada adicional para
          resolver Id_Colonia → Id_Municipio → Id_Estado (precarga de selects).
    POST: Actualiza datos personales, dirección y/o contraseña.
    """
    if 'jwt_token' not in session:
        return redirect(url_for('login'))

    headers = {'Authorization': f"Bearer {session.get('jwt_token')}"}

    # ─────────────────────────────────────────
    # POST: Procesar el formulario de edición
    # ─────────────────────────────────────────
    if request.method == 'POST':
        perfil_payload = {}

        # 1. Datos personales
        nombre     = request.form.get('Nombre', '').strip()
        apellido_p = request.form.get('Apellido_P', '').strip()
        apellido_m = request.form.get('Apellido_M', '').strip()
        tel_str    = request.form.get('Tel', '').strip()
        edad_str   = request.form.get('Edad', '').strip()

        if nombre:     perfil_payload['Nombre']     = nombre
        if apellido_p: perfil_payload['Apellido_P'] = apellido_p
        if apellido_m: perfil_payload['Apellido_M'] = apellido_m
        if tel_str and tel_str.isdigit():
            perfil_payload['Tel'] = int(tel_str)
        if edad_str and edad_str.isdigit():
            perfil_payload['Edad'] = int(edad_str)

        # 2. Contraseña (opcional: solo si el user escribió algo)
        nueva_pass    = request.form.get('new_password', '').strip()
        confirm_pass  = request.form.get('confirm_password', '').strip()

        if nueva_pass:
            if nueva_pass != confirm_pass:
                flash("Las contraseñas no coinciden. Los demás cambios fueron descartados.", "error")
                return redirect(url_for('ciudadano_perfil'))
            if len(nueva_pass) < 6:
                flash("La contraseña debe tener al menos 6 caracteres.", "error")
                return redirect(url_for('ciudadano_perfil'))
            perfil_payload['Password'] = nueva_pass

        # 3. Dirección
        id_colonia  = request.form.get('Id_Colonia', '').strip()
        calle       = request.form.get('Calle', '').strip()
        no_exterior = request.form.get('No_exterior', '').strip()

        try:
            if id_colonia and calle and no_exterior:
                # 3a. Crear la nueva dirección en la API
                dir_res = requests.post(
                    f"{API_URL}/catalogos/direcciones",
                    json={
                        "Id_Colonia":  int(id_colonia),
                        "Calle":       calle,
                        "No_exterior": no_exterior
                    },
                    headers=headers,
                    timeout=5
                )

                if dir_res.status_code == 201:
                    dir_data = dir_res.json().get('data', {})
                    id_dir = dir_data.get('Id_Direccion')
                    if id_dir:
                        perfil_payload['Id_Direccion'] = id_dir
                else:
                    # Intentar parsear mensaje de error de la API
                    try:
                        err_msg = dir_res.json().get('detail', 'Error al guardar la dirección.')
                    except Exception:
                        err_msg = 'Error al guardar la dirección.'
                    flash(f"Dirección: {err_msg}", "error")

            # 4. Actualizar perfil en la API (solo si hay algo que actualizar)
            if perfil_payload:
                p_res = requests.patch(
                    f"{API_URL}/usuarios/me",
                    json=perfil_payload,
                    headers=headers,
                    timeout=5
                )
                if p_res.status_code == 200:
                    flash("¡Perfil actualizado exitosamente!", "success")
                else:
                    try:
                        err_msg = p_res.json().get('detail', 'Error al actualizar el perfil.')
                    except Exception:
                        err_msg = 'Error al actualizar el perfil.'
                    flash(f"Error: {err_msg}", "error")
            else:
                flash("No se detectaron cambios para guardar.", "warning")

        except Exception as e:
            flash(f"Error de conexión con la API: {str(e)}", "error")

        return redirect(url_for('ciudadano_perfil'))

    # ─────────────────────────────────────────
    # GET: Precargar datos del perfil
    # ─────────────────────────────────────────
    usuario_data   = {}
    estados        = []
    municipios_pre = []   # municipios pre-cargados si ya hay dirección
    colonias_pre   = []   # colonias pre-cargadas si ya hay dirección
    id_estado_pre  = None
    id_municipio_pre = None

    try:
        # 1. Obtener perfil del usuario autenticado
        r = requests.get(f"{API_URL}/usuarios/me", headers=headers, timeout=5)
        if r.status_code == 200:
            usuario_data = r.json()
        elif r.status_code == 401:
            session.clear()
            return redirect(url_for('login'))
        else:
            flash("No se pudo obtener tu información de perfil.", "warning")

        # 2. Catálogo de estados (siempre necesario)
        st_res = requests.get(f"{API_URL}/catalogos/estados", headers=headers, timeout=5)
        if st_res.status_code == 200:
            estados = st_res.json()

        # 3. Si el usuario tiene dirección, resolvemos el árbol geográfico completo
        #    para pre-seleccionar los <select> de Estado → Municipio → Colonia
        dir_obj = usuario_data.get('direccion')
        if dir_obj and dir_obj.get('Id_Colonia'):
            id_colonia = dir_obj['Id_Colonia']

            # 3a. Obtener la colonia para saber el municipio
            col_res = requests.get(
                f"{API_URL}/catalogos/colonias/{id_colonia}",
                headers=headers, timeout=5
            )
            if col_res.status_code == 200:
                colonia_info = col_res.json()
                id_municipio_pre = colonia_info.get('Id_Municipio')

                if id_municipio_pre:
                    # 3b. Obtener el municipio para saber el estado
                    mun_res = requests.get(
                        f"{API_URL}/catalogos/municipios/{id_municipio_pre}",
                        headers=headers, timeout=5
                    )
                    if mun_res.status_code == 200:
                        municipio_info = mun_res.json()
                        id_estado_pre = municipio_info.get('Id_Estado')

                        # 3c. Cargar todos los municipios del estado para poblar el select
                        if id_estado_pre:
                            muns_res = requests.get(
                                f"{API_URL}/catalogos/estados/{id_estado_pre}/municipios",
                                headers=headers, timeout=5
                            )
                            if muns_res.status_code == 200:
                                municipios_pre = muns_res.json()

                    # 3d. Cargar todas las colonias del municipio para poblar el select
                    cols_res = requests.get(
                        f"{API_URL}/catalogos/municipios/{id_municipio_pre}/colonias",
                        headers=headers, timeout=5
                    )
                    if cols_res.status_code == 200:
                        colonias_pre = cols_res.json()

    except Exception as e:
        flash(f"No se pudo cargar la información del perfil: {str(e)}", "error")

    return render_template(
        'ciudadano_perfil.html',
        usuario=usuario_data,
        estados=estados,
        municipios_pre=municipios_pre,
        colonias_pre=colonias_pre,
        id_estado_pre=id_estado_pre,
        id_municipio_pre=id_municipio_pre,
    )


# ==========================================
# PROXY ROUTES PARA AJAX (Selects en Cascada)
# ==========================================

@app.route('/api/municipios/<int:estado_id>')
def api_municipios(estado_id):
    headers = {'Authorization': f"Bearer {session.get('jwt_token')}"} if 'jwt_token' in session else {}
    try:
        r = requests.get(f"{API_URL}/catalogos/estados/{estado_id}/municipios", headers=headers, timeout=5)
        return jsonify(r.json()) if r.status_code == 200 else jsonify([])
    except Exception:
        return jsonify([])

@app.route('/api/colonias/<int:municipio_id>')
def api_colonias(municipio_id):
    headers = {'Authorization': f"Bearer {session.get('jwt_token')}"} if 'jwt_token' in session else {}
    try:
        r = requests.get(f"{API_URL}/catalogos/municipios/{municipio_id}/colonias", headers=headers, timeout=5)
        return jsonify(r.json()) if r.status_code == 200 else jsonify([])
    except Exception:
        return jsonify([])


# ==========================================
# OTRAS RUTAS PRIVADAS
# ==========================================

@app.route('/mis-donaciones')
def ciudadano_donaciones():
    if 'jwt_token' not in session: return redirect(url_for('login'))
    return render_template('ciudadano_donaciones.html')

@app.route('/mis-voluntariados')
def ciudadano_voluntariados():
    if 'jwt_token' not in session:
        return redirect(url_for('login'))

    headers = {'Authorization': f"Bearer {session.get('jwt_token')}"}
    todos_voluntariados = []
    mis_voluntariados   = []

    try:
        r_todos = requests.get(f"{API_URL}/voluntariados/", headers=headers, timeout=5)
        if r_todos.status_code == 200:
            todos_voluntariados = r_todos.json()
        elif r_todos.status_code == 401:
            session.clear()
            return redirect(url_for('login'))

        r_mis = requests.get(f"{API_URL}/voluntariados/inscripciones/me", headers=headers, timeout=5)
        if r_mis.status_code == 200:
            mis_voluntariados = r_mis.json()
    except Exception as e:
        flash(f"Error al cargar voluntariados: {str(e)}", "error")

    ids_inscritos = {v['Id_Voluntariado'] for v in mis_voluntariados}
    voluntariados_disponibles = [
        v for v in todos_voluntariados
        if v['Id_Voluntariado'] not in ids_inscritos
        and v.get('Estado_Voluntariado') == 'Activo'
    ]

    return render_template('ciudadano_voluntariados.html',
                           mis_voluntariados=mis_voluntariados,
                           voluntariados_disponibles=voluntariados_disponibles)


@app.route('/inscribirse', methods=['POST'])
def inscribirse_voluntariado():
    if 'jwt_token' not in session:
        return redirect(url_for('login'))

    headers = {'Authorization': f"Bearer {session.get('jwt_token')}"}
    id_voluntariado = request.form.get('Id_Voluntariado')

    try:
        r = requests.post(
            f"{API_URL}/voluntariados/{id_voluntariado}/inscribirse",
            headers=headers,
            timeout=5
        )
        if r.status_code == 201:
            flash("¡Te has inscrito exitosamente al voluntariado!", "success")
        else:
            try:
                detail = r.json().get('detail', 'Error al inscribirse.')
            except Exception:
                detail = f"Error {r.status_code} al inscribirse."
            flash(f"Error: {detail}", "error")
    except Exception as e:
        flash(f"Error de conexión: {str(e)}", "error")

    return redirect(url_for('ciudadano_voluntariados'))


@app.route('/cancelar-inscripcion', methods=['POST'])
def cancelar_inscripcion():
    if 'jwt_token' not in session:
        return redirect(url_for('login'))

    headers = {'Authorization': f"Bearer {session.get('jwt_token')}"}
    id_voluntariado = request.form.get('Id_Voluntariado')

    try:
        r = requests.delete(
            f"{API_URL}/voluntariados/{id_voluntariado}/cancelar-inscripcion",
            headers=headers,
            timeout=5
        )
        if r.status_code == 200:
            flash("Inscripción cancelada exitosamente.", "success")
        else:
            try:
                detail = r.json().get('detail', 'Error al cancelar.')
            except Exception:
                detail = f"Error {r.status_code} al cancelar."
            flash(f"Error: {detail}", "error")
    except Exception as e:
        flash(f"Error de conexión: {str(e)}", "error")

    return redirect(url_for('ciudadano_voluntariados'))


# En tu archivo de Flask
import os

if __name__ == '__main__':
    # Esto lee el puerto de Railway (8080) o usa 5000 si estás en tu PC
    port = int(os.environ.get("PORT", 5000))
    app.run(host='0.0.0.0', port=port)