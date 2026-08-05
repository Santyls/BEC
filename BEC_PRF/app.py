import os

from flask import Flask, redirect, url_for
from flask_wtf import CSRFProtect

from blueprints.auth import bp as auth_bp


def create_app():
    app = Flask(__name__)
    app.secret_key = os.environ["FLASK_SECRET_KEY"]
    # Distinta de BEC_API_URL (usada por bec_api_client.py): esa es el hostname de
    # Docker, solo alcanzable servidor-a-servidor; esta es la que el navegador del
    # recepcionista necesita para pedir archivos estáticos servidos por la API
    # (fotos de perfil, etc.).
    app.config["BEC_API_PUBLIC_URL"] = os.environ.get("BEC_API_PUBLIC_URL", "http://localhost:8000")
    # Límite global de tamaño de cuerpo — sobre todo pensado para la subida de foto de
    # perfil; Flask responde 413 automáticamente si se excede, antes de leer el archivo.
    app.config["MAX_CONTENT_LENGTH"] = 5 * 1024 * 1024

    # Protección CSRF en TODAS las rutas POST/PUT/DELETE — sin esto, cualquier sitio
    # externo podría enviar peticiones a nombre de una sesión de recepcionista activa
    # (ej. inscribir/cancelar/editar) con solo lograr que abra un enlace o imagen maliciosa.
    CSRFProtect(app)

    from blueprints.dashboard import bp as dashboard_bp
    from blueprints.donaciones import bp as donaciones_bp
    from blueprints.perfil import bp as perfil_bp
    from blueprints.usuarios import bp as usuarios_bp
    from blueprints.voluntariados import bp as voluntariados_bp

    app.register_blueprint(auth_bp)
    app.register_blueprint(dashboard_bp)
    app.register_blueprint(usuarios_bp, url_prefix="/usuarios")
    app.register_blueprint(donaciones_bp, url_prefix="/donaciones")
    app.register_blueprint(voluntariados_bp, url_prefix="/voluntariados")
    app.register_blueprint(perfil_bp, url_prefix="/perfil")

    @app.route("/")
    def raiz():
        return redirect(url_for("dashboard.inicio"))

    return app


app = create_app()

if __name__ == "__main__":
    app.run(debug=True, host="0.0.0.0", port=5000)
