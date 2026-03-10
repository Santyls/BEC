from flask import Flask, render_template

app = Flask(__name__)
app.secret_key = 'super_secreta'

# --- MOCKS PARA EL HOME ---
MOCK_CAMPANAS = [
    {"id": 1, "nombre": "Colecta Invernal 2026", "lugar": "Centro Qro.", "fecha": "10 Mar 2026"},
    {"id": 2, "nombre": "Jornada Médica", "lugar": "Albergue San Juan", "fecha": "15 Mar 2026"}
]

# ==========================================
# RUTAS DEL PORTAL CIUDADANO (ESTILO BEC)
# ==========================================

@app.route('/')
def ciudadano_home():
    """Página de inicio pública del portal"""
    return render_template('ciudadano_home.html', campanas=MOCK_CAMPANAS)

@app.route('/albergues')
def ciudadano_albergues():
    """Mapa de localización de albergues en Querétaro"""
    return render_template('ciudadano_albergues.html')

# --- RUTAS PRIVADAS DEL CIUDADANO (Requieren sesión en el futuro) ---

@app.route('/mi-perfil')
def ciudadano_perfil():
    """Menú de perfil: Edición de datos personales"""
    # Aquí consultaríamos los datos del usuario logueado
    return render_template('ciudadano_perfil.html', tab='datos')

@app.route('/mi-perfil/domicilio')
def ciudadano_domicilio():
    """Menú de perfil: Edición de domicilio"""
    return render_template('ciudadano_perfil.html', tab='domicilio')

@app.route('/mis-donaciones')
def ciudadano_donaciones():
    """Consulta del historial de donaciones hechas por el ciudadano"""
    return render_template('ciudadano_donaciones.html')

@app.route('/mis-voluntariados')
def ciudadano_voluntariados():
    """Consulta de los voluntariados a los que se ha apuntado"""
    return render_template('ciudadano_voluntariados.html')

# ==========================================
# RUTAS RECEPCIONISTA (Las que ya hicimos)
# ==========================================
@app.route('/recepcion')
def recepcion_home():
    return render_template('home.html') # Asumiendo que tu recepcionista está en /recepcion

if __name__ == '__main__':
    app.run(debug=True, port=5000)