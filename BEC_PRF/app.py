from flask import Flask, render_template, request, redirect, url_for, session, flash
import requests
from functools import wraps
import os

app = Flask(__name__)
# Usamos una clave secreta para firmar la cookie de la session de Flask
app.secret_key = os.getenv('SECRET_KEY', 'super_secreta_prf_2026')

API_URL = os.getenv('BEC_API_URL', 'http://bec_api_app:5000')

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

@app.route('/donaciones/nueva', methods=['GET', 'POST'])
@login_required
def nueva_donacion():
    # Obtener catálogos desde la API para llenar los Selects del formulario
    headers = {'Authorization': f"Bearer {session.get('jwt_token')}"}
    
    # En la vida real harías gets a /categorias y /unidades si existieran los endpoints, 
    # por ahora simulamos los IDs con un template estático y mandamos directo a donaciones.
    
    if request.method == 'POST':
        # Nota: El Id_Albergue no se manda. La API BEC lo saca del token JWT del recepcionista.
        payload = {
            "Id_Usuario": int(request.form.get('id_usuario')), # El ID del donante público
            "id_Categoria": int(request.form.get('id_categoria')),
            "Id_Condicion": request.form.get('condicion'),
            "Cantidad": float(request.form.get('cantidad')),
            "Id_Unidad": int(request.form.get('id_unidad')),
            "Marca": request.form.get('marca') or None
        }

        r = requests.post(f"{API_URL}/donaciones/", json=payload, headers=headers)
        
        if r.status_code == 201:
            flash("¡Donativo registrado exitosamente!", "success")
            return redirect(url_for('home'))
        else:
            flash(f"Error al registrar: {r.text}", "error")

    # Si es GET, mostramos formulario
    return render_template('donaciones/form.html')

@app.route('/usuarios/nuevo')
@login_required
def nuevo_usuario():
    return render_template('home.html') # Placeholder

@app.route('/voluntariados/asignar')
@login_required
def asignar_voluntariado():
    return render_template('home.html') # Placeholder

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)