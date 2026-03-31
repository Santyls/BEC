from flask import Flask, render_template, request, session, redirect, url_for, flash
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

    if request.method == 'POST':
        correo = request.form.get('correo')
        password = request.form.get('password')

        try:
            r = requests.post(f"{API_URL}/login", data={
                'username': correo,
                'password': password
            })
            
            if r.status_code == 200:
                data = r.json()
                session['jwt_token'] = data.get('access_token')
                session['correo'] = correo
                return redirect(url_for('ciudadano_home'))
            else:
                flash("El correo o contraseña no son correctos.", "error")
        except Exception:
            flash("Error conectando con la plataforma BEC.", "error")

    return render_template('login.html')  # El form de login lo haré parte del UI o en un archivo aparte

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
        # Hacemos la petición sin token, asumiendo que el Endpoint de campañas permita GET publico 
        # (Si la API las protege todas, le pasaremos el token en el header solo si hay sesión, 
        # o el endpoint de FastAPI debería estar sin dependency de current_user).
        # Ya que TODO está protegido en las Prácticas previas por get_current_user,
        # Necesitamos mandar token. Si no hay sesión, simulamos un payload vacío para que la vista no rompa.
        
        headers = {}
        if 'jwt_token' in session:
            headers['Authorization'] = f"Bearer {session.get('jwt_token')}"
            
        r = requests.get(f"{API_URL}/campanas/", headers=headers, timeout=5)
        
        if r.status_code == 200:
            campanas_reales = r.json()
        elif r.status_code == 401:
            flash("Debes inciar sesión para ver la información en tiempo real.", "warning")
            
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
@app.route('/mi-perfil')
def ciudadano_perfil():
    if 'jwt_token' not in session: return redirect(url_for('login'))
    return render_template('ciudadano_perfil.html', tab='datos')

@app.route('/mis-donaciones')
def ciudadano_donaciones():
    if 'jwt_token' not in session: return redirect(url_for('login'))
    return render_template('ciudadano_donaciones.html')

@app.route('/mis-voluntariados')
def ciudadano_voluntariados():
    if 'jwt_token' not in session: return redirect(url_for('login'))
    return render_template('ciudadano_voluntariados.html')

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)