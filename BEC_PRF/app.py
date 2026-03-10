from flask import Flask, render_template

app = Flask(__name__)

# --- RUTAS DEL RECEPCIONISTA ---

@app.route('/')
def home():
    # Renderiza el dashboard principal del recepcionista
    return render_template('home.html')

@app.route('/usuarios/nuevo')
def nuevo_usuario():
    # Vista para registrar ciudadano (rol asignado por defecto en BD)
    return render_template('usuarios/index.html') # Placeholder

@app.route('/donaciones/nueva')
def nueva_donacion():
    # Vista para registrar donativo vinculado a un ciudadano
    return render_template('donaciones/index.html') # Placeholder

@app.route('/voluntariados/asignar')
def asignar_voluntariado():
    # Vista para apuntar ciudadano a voluntariado
    return render_template('voluntariados/index.html') # Placeholder

if __name__ == '__main__':
    # Ejecuta el servidor en el puerto 5000
    app.run(debug=True, port=5000)