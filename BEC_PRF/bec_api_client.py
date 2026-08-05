"""Cliente HTTP hacia BEC_API — mismo rol que BecApiClient.php en BEC_PAL.

BEC_PRF no toca Postgres ni Eloquent/ORM directamente: toda la data de negocio
pasa por BEC_API, que ya trae JWT, validaciones y reglas de negocio.
"""

import os

import requests
from flask import session

BEC_API_URL = os.environ.get("BEC_API_URL", "http://bec_api:5000")
TIMEOUT_SEGUNDOS = 10


class BecApiException(Exception):
    def __init__(self, status_code, mensaje, errores=None):
        self.status_code = status_code
        self.mensaje = mensaje
        self.errores = errores or []
        super().__init__(mensaje)


def _mensaje_desde_detail(detail):
    """El detail de FastAPI/Pydantic llega como string (errores de negocio) o como
    lista de {loc, msg} (errores de validación) — se normaliza a un texto legible."""
    if isinstance(detail, str):
        return detail
    if isinstance(detail, list):
        partes = []
        for err in detail:
            ubicacion = err.get("loc", [])
            campo = ".".join(str(p) for p in ubicacion[1:]) if len(ubicacion) > 1 else ""
            mensaje = err.get("msg", "")
            partes.append(f"{campo}: {mensaje}" if campo else mensaje)
        return " | ".join(partes) if partes else "Datos inválidos."
    return "Ocurrió un error inesperado."


class BecApiClient:
    def get(self, path, params=None):
        return self._request("GET", path, params=params)

    def post(self, path, data=None):
        return self._request("POST", path, json_body=data or {})

    def put(self, path, data=None):
        return self._request("PUT", path, json_body=data or {})

    def delete(self, path):
        self._request("DELETE", path)

    def post_form(self, path, data=None):
        return self._request("POST", path, form_body=data or {})

    def post_file(self, path, nombre_campo, archivo, content_type=None):
        """Sube un archivo como multipart/form-data. `archivo` es un objeto tipo
        FileStorage (el que entrega request.files en Flask). Se lee a bytes de una
        vez porque el stream original se agota al enviarse; si hay un reintento por
        token vencido, _request necesita poder reconstruir el mismo cuerpo."""
        contenido = archivo.read()
        files = {nombre_campo: (archivo.filename, contenido, content_type or archivo.content_type)}
        return self._request("POST", path, files=files)

    def _request(self, method, path, params=None, json_body=None, form_body=None, files=None, reintentado=False):
        headers = {}
        token = session.get("bec_access_token")
        if token:
            headers["Authorization"] = f"Bearer {token}"

        try:
            respuesta = requests.request(
                method,
                f"{BEC_API_URL}{path}",
                headers=headers,
                params=params,
                json=json_body,
                data=form_body,
                files=files,
                timeout=TIMEOUT_SEGUNDOS,
            )
        except requests.RequestException:
            raise BecApiException(0, "No se pudo comunicar con el servidor. Intenta de nuevo en unos momentos.")

        if respuesta.status_code == 401 and not reintentado and session.get("bec_refresh_token"):
            if self._refrescar_token():
                return self._request(
                    method, path, params=params, json_body=json_body, form_body=form_body, files=files, reintentado=True
                )

        return self._manejar_respuesta(respuesta)

    def _refrescar_token(self):
        refresh = session.get("bec_refresh_token")
        try:
            respuesta = requests.post(
                f"{BEC_API_URL}/auth/refresh", json={"refresh_token": refresh}, timeout=TIMEOUT_SEGUNDOS
            )
        except requests.RequestException:
            return False

        if respuesta.status_code != 200:
            session.pop("bec_access_token", None)
            session.pop("bec_refresh_token", None)
            return False

        datos = respuesta.json()
        session["bec_access_token"] = datos["access_token"]
        session["bec_refresh_token"] = datos["refresh_token"]
        return True

    def _manejar_respuesta(self, respuesta):
        if not respuesta.ok:
            detail = None
            try:
                detail = respuesta.json().get("detail")
            except ValueError:
                pass
            mensaje = _mensaje_desde_detail(detail) if detail else "Ocurrió un error inesperado."
            raise BecApiException(respuesta.status_code, mensaje, detail if isinstance(detail, list) else [])

        if respuesta.status_code == 204 or not respuesta.content:
            return {}
        return respuesta.json()
