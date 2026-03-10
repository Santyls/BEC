from fastapi import FastAPI

app = FastAPI(title="BEC API")

@app.get("/api/status")
def check_status():
    return {
        "status": "success", 
        "message": "¡La API BEC_API (FastAPI) está funcionando correctamente!"
    }