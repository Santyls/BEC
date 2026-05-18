from flask import Flask, render_template, request, redirect, url_for, session, flash
import requests
from functools import wraps
import os

app = Flask(__name__)
app.secret_key = os.getenv('SECRET_KEY', 'super_secreta_prf_2026')

API_URL = os.getenv('BEC_API_URL', 'http://bec_api_app:5000')

# Catálogos locales (seed de la BD — evitan N+1 calls a la API)
CATEGORIAS_MAP = {1: "Ropa", 2: "Alimentos", 3: "Cobijas", 4: "Higiene Personal", 5: "Medicamentos"}
UNIDADES_MAP   = {1: "Piezas", 2: "Kilogramos (kg)", 3: "Litros (L)", 4: "Cajas", 5: "Paquetes"}

# ==========================================
# Decorador de Autenticación
# ==========================================
def login_required(f):
    @wraps(f)
    def decorated_function(*args, **kwargs):
        if 'jwt_token' not in session:
            flash("Debes iniciar turno para acceder.", "error")
            return redirect(url_for('login'))
        return f(*args, **kwargs)
    return decorated_function

# ==========================================
# RUTAS DE LOGIN
# ==========================================
@app.route('/login', methods=['GET', 'POST'])
def login():
    if 'jwt_token' in session:
        return redirect(url_for('home'))

    if request.method == 'POST':
        correo = request.form.get('correo')
        password = request.form.get('password')

        # Hacemos POST a BEC_API enviando como formulario (OAuth2 expected config)
        try:
            r = requests.post(f"{API_URL}/login", data={
                'username': correo,
                'password': password
            })
            
            if r.status_code == 200:
                data = r.json()
                session['jwt_token'] = data.get('access_token')
                session['correo'] = correo
                return redirect(url_for('home'))
            else:
                flash("Credenciales inválidas. Verifica tu información.", "error")
        except requests.exceptions.ConnectionError:
            flash("Error de conexión crítica con el Servidor Central (BEC API).", "error")

    return render_template('login.html')

@app.route('/logout', methods=['POST'])
def logout():
    session.clear()
    return redirect(url_for('login'))

# ==========================================
# RUTAS DEL RECEPCIONISTA (Protegidas)
# ==========================================
@app.route('/')
@app.route('/home')
@login_required
def home():
    # Renderiza al dashboard
    return render_template('home.html')

@app.route('/donaciones')
@login_required
def donaciones_list():
    headers = {'Authorization': f"Bearer {session.get('jwt_token')}"}
    donaciones = []

    try:
        # 1. Mapa id → nombre completo de todos los usuarios
        r_u = requests.get(f"{API_URL}/usuarios/", headers=headers, timeout=5)
        usuarios_map = {}
        if r_u.status_code == 200:
            for u in r_u.json():
                uid = u.get('id_Usuario')
                nombre = f"{u.get('Nombre','')} {u.get('Apellido_P','')} {(u.get('Apellido_M') or '')}".strip()
                usuarios_map[uid] = nombre
        elif r_u.status_code == 401:
            session.clear()
            return redirect(url_for('login'))

        # 2. Obtener todas las donaciones y enriquecer
        r_d = requests.get(f"{API_URL}/donaciones/", headers=headers, timeout=5)
        if r_d.status_code == 200:
            for d in r_d.json():
                uid = d.get('Id_Usuario')
                cat_id = d.get('id_Categoria')
                uni_id = d.get('Id_Unidad')
                fecha  = str(d.get('Fecha_Donacion', ''))
                donaciones.append({
                    "nombre_donante":   usuarios_map.get(uid, f"Ciudadano #{uid}"),
                    "nombre_categoria": CATEGORIAS_MAP.get(cat_id, f"Categoría {cat_id}"),
                    "Id_Condicion":     d.get('Id_Condicion', '—'),
                    "Cantidad":         d.get('Cantidad', 0),
                    "nombre_unidad":    UNIDADES_MAP.get(uni_id, ''),
                    "Marca":            d.get('Marca') or '',
                    # Fecha como DD/MM/AAAA (la API devuelve YYYY-MM-DD)
                    "Fecha_Donacion":   f"{fecha[8:10]}/{fecha[5:7]}/{fecha[0:4]}" if len(fecha) == 10 else fecha,
                })
        elif r_d.status_code == 401:
            session.clear()
            return redirect(url_for('login'))
        else:
            flash("No se pudo cargar el historial de donaciones.", "error")

    except Exception as e:
        flash(f"Error de conexión con la API: {str(e)}", "error")

    return render_template('donaciones/index.html', donaciones=donaciones)


@app.route('/donaciones/nueva', methods=['GET', 'POST'])
@login_required
def nueva_donacion():
    headers = {'Authorization': f"Bearer {session.get('jwt_token')}"}

    if request.method == 'POST':
        id_usuario_str   = request.form.get('id_usuario', '').strip()
        id_categoria_str = request.form.get('id_categoria', '').strip()
        condicion        = request.form.get('condicion', '').strip()
        cantidad_str     = request.form.get('cantidad', '').strip()
        id_unidad_str    = request.form.get('id_unidad', '').strip()
        marca            = request.form.get('marca', '').strip() or None

        if not id_usuario_str or not id_usuario_str.lstrip('-').isdigit():
            flash("Debes seleccionar un ciudadano válido de la lista.", "error")
            return redirect(url_for('nueva_donacion'))

        try:
            payload = {
                "Id_Usuario":   int(id_usuario_str),
                "id_Categoria": int(id_categoria_str),
                "Id_Condicion": condicion,
                "Cantidad":     float(cantidad_str),
                "Id_Unidad":    int(id_unidad_str),
                "Marca":        marca,
            }
            r = requests.post(f"{API_URL}/donaciones/", json=payload, headers=headers, timeout=5)

            if r.status_code == 201:
                flash("¡Donativo registrado exitosamente!", "success")
                return redirect(url_for('donaciones_list'))
            else:
                try:
                    detail = r.json().get('detail', r.text)
                except Exception:
                    detail = f"Error {r.status_code} al registrar."
                flash(f"Error al registrar: {detail}", "error")

        except (ValueError, TypeError) as e:
            flash(f"Datos inválidos en el formulario: {str(e)}", "error")
        except Exception as e:
            flash(f"Error de conexión con la API: {str(e)}", "error")

        return redirect(url_for('nueva_donacion'))

    # GET: cargar ciudadanos para el selector
    usuarios = []
    try:
        r_u = requests.get(f"{API_URL}/usuarios/", headers=headers, timeout=5)
        if r_u.status_code == 200:
            usuarios = [u for u in r_u.json() if u.get('Id_Rol') == 3]
        elif r_u.status_code == 401:
            session.clear()
            return redirect(url_for('login'))
        else:
            flash("No se pudo cargar la lista de ciudadanos.", "error")
    except Exception as e:
        flash(f"Error al obtener ciudadanos: {str(e)}", "error")

    return render_template('donaciones/form.html', usuarios=usuarios)

@app.route('/usuarios/nuevo', methods=['GET', 'POST'])
@login_required
def nuevo_usuario():
    if request.method == 'POST':
        nombre_full = request.form.get('nombre_completo', '').strip()
        correo = request.form.get('correo')
        password = request.form.get('password')

        # Lógica simple para dividir el nombre completo en Nombre, Apellido_P, Apellido_M
        partes = nombre_full.split()
        nombre = partes[0] if len(partes) > 0 else "Sin Nombre"
        apellido_p = partes[1] if len(partes) > 1 else " "
        apellido_m = " ".join(partes[2:]) if len(partes) > 2 else " "

        payload = {
            "Nombre": nombre,
            "Apellido_P": apellido_p,
            "Apellido_M": apellido_m,
            "Correo": correo,
            "Password": password,
            "Edad": 18,              # Default
            "Id_Direccion": 1,       # Default (Debe existir en la BD)
            "Tel": 0,                # Default
            "Id_Genero": 1,          # Default (1 = Masculino generalmente)
            "Id_Rol": 3              # Ciudadano
        }

        headers = {'Authorization': f"Bearer {session.get('jwt_token')}"}
        
        try:
            r = requests.post(f"{API_URL}/usuarios/", json=payload, headers=headers)
            if r.status_code == 201:
                flash(f"¡Ciudadano {nombre} registrado con éxito!", "success")
                return redirect(url_for('home'))
            else:
                error_detail = r.json().get('detail', r.text)
                flash(f"Error al registrar: {error_detail}", "error")
        except Exception as e:
            flash(f"Error de comunicación con la API: {str(e)}", "error")

    return render_template('usuarios/index.html')

@app.route('/voluntariados/asignar', methods=['GET', 'POST'])
@login_required
def asignar_voluntariado():
    headers = {'Authorization': f"Bearer {session.get('jwt_token')}"}
    
    if request.method == 'POST':
        # Próximo paso: Implementar lógica de asignación
        pass

    # Para el GET, necesitamos cargar los usuarios (ciudadanos) y las campañas
    usuarios = []
    campanas = []
    try:
        r_u = requests.get(f"{API_URL}/usuarios/", headers=headers)
        if r_u.status_code == 200:
            # Filtramos solo por rol Ciudadano (Id_Rol == 3)
            usuarios = [u for u in r_u.json() if u.get('Id_Rol') == 3]
            
        r_c = requests.get(f"{API_URL}/campanas/", headers=headers)
        if r_c.status_code == 200:
            campanas = r_c.json()
    except Exception:
        pass

    return render_template('voluntariados/index.html', usuarios=usuarios, voluntariados=campanas)

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)