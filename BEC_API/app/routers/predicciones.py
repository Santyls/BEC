from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from sqlalchemy import func
from datetime import date, timedelta
from typing import Dict, Any

from app.data.database import get_db
from app.models.models import Donacion, Usuario
from app.security.security import get_current_user

# Try importing statsmodels and pandas
try:
    import pandas as pd
    from statsmodels.tsa.holtwinters import ExponentialSmoothing
except ImportError:
    pd = None

router = APIRouter()

@router.get("/albergue/{id_albergue}", status_code=status.HTTP_200_OK)
def predecir_demanda(
    id_albergue: int, 
    db: Session = Depends(get_db), 
    current_user: Usuario = Depends(get_current_user)
) -> Dict[str, Any]:
    """
    Endpoint de Machine Learning (Series de Tiempo)
    Predice la necesidad o el influjo de donaciones de Ropa y Cobijas basándose en histórico.
    """
    if pd is None:
        raise HTTPException(status_code=500, detail="Librerías de análisis de datos no están instaladas (pandas, statsmodels).")

    # 1. Obtener datos históricos de la BD
    resultados = (
        db.query(
            Donacion.Fecha_Donacion, 
            func.sum(Donacion.Cantidad).label("total_cantidad")
        )
        .filter(Donacion.Id_Albergue == id_albergue)
        .filter(Donacion.id_Categoria.in_([1, 3]))
        .group_by(Donacion.Fecha_Donacion)
        .order_by(Donacion.Fecha_Donacion)
        .all()
    )

    if not resultados or len(resultados) < 30:
        raise HTTPException(
            status_code=400, 
            detail="No hay suficientes datos históricos para este albergue (Mínimo 30 días requeridos)."
        )

    # 2. Convertir a DataFrame de Pandas
    fechas = [r[0] for r in resultados]
    cantidades = [float(r[1]) for r in resultados]

    df = pd.DataFrame({"fecha": fechas, "cantidad": cantidades})
    df['fecha'] = pd.to_datetime(df['fecha'])
    df.set_index('fecha', inplace=True)

    # 3. Resamplear (Rellenar días sin donaciones con 0)
    df = df.resample('D').sum().fillna(0)
    ts = df['cantidad']

    # 4. Ajustar modelo
    seasonal_periods = 30 if len(ts) >= 60 else None
    
    try:
        valores = ts.values
        if len(valores) > 10:
            if seasonal_periods:
                modelo = ExponentialSmoothing(
                    valores, trend='add', seasonal='add', 
                    seasonal_periods=seasonal_periods, initialization_method="estimated"
                )
            else:
                modelo = ExponentialSmoothing(
                    valores, trend='add', initialization_method="estimated"
                )
            modelo_ajustado = modelo.fit()
            pred_valores = modelo_ajustado.forecast(30)
        else:
            avg = float(ts.mean())
            pred_valores = [avg] * 30
    except Exception as ex:
        print(f"Fallback de M.L. activado por: {ex}")
        avg = float(ts.mean())
        pred_valores = [avg] * 30

    # 5. Formatear Fechas y Arrays para el Frontend
    last_date = ts.index[-1]
    fechas_futuras = [last_date + timedelta(days=i) for i in range(1, 31)]
    historico_reciente = ts.tail(60)

    respuesta = {
        "historico": {
            "fechas": [d.strftime("%Y-%m-%d") for d in historico_reciente.index],
            "valores": [round(float(val), 2) for val in historico_reciente.tolist()]
        },
        "prediccion": {
            "fechas": [d.strftime("%Y-%m-%d") for d in fechas_futuras],
            "valores": [round(float(val), 2) if val > 0 else 0.0 for val in pred_valores] 
        }
    }
    
    return respuesta
