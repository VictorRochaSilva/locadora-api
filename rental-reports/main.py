from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
from sqlalchemy import create_engine, text
from typing import List
from datetime import datetime
import logging
import os

app = FastAPI()

# Configurações de Banco (pegar do ambiente ou definir aqui direto)
DB_USER = os.getenv('DB_USERNAME', 'laravel')
DB_PASSWORD = os.getenv('DB_PASSWORD', 'secret')
DB_HOST = os.getenv('DB_HOST', 'localhost')
DB_PORT = os.getenv('DB_PORT', '5433')
DB_NAME = os.getenv('DB_DATABASE', 'locadora')

DATABASE_URL = f"postgresql://{DB_USER}:{DB_PASSWORD}@{DB_HOST}:{DB_PORT}/{DB_NAME}"

engine = create_engine(DATABASE_URL)

class Report(BaseModel):
    plate: str
    make: str
    model: str
    total_rentals: int
    total_revenue: float

from datetime import datetime

@app.get("/reports/revenue", response_model=List[Report])
def get_revenue_report(start: str, end: str):
    try:
      # Convertendo as strings para o formato de data
        start_date = datetime.strptime(start, '%Y-%m-%d')
        end_date = datetime.strptime(end, '%Y-%m-%d')


        with engine.connect() as conn:
            query = text("""
                SELECT
                    v.plate,
                    v.make,
                    v.model,
                    COUNT(r.id) AS total_rentals,
                    COALESCE(SUM(r.total_amount), 0) AS total_revenue
                FROM rentals r
                JOIN vehicles v ON v.id = r.vehicle_id
                WHERE r.start_date >= :start AND r.end_date <= :end
                GROUP BY v.id
            """)

            # Passando as variáveis de data convertidas para a consulta
            result = conn.execute(query, {"start": start_date, "end": end_date})

            logging.info(f'Start: {start_date}, End: {end_date}')
            reports = [Report(**row._mapping) for row in result.fetchall()]
            return reports

    except Exception as e:
        logging.error(f"Erro ao gerar relatório: {str(e)}")
        raise HTTPException(status_code=500, detail="Erro ao gerar o relatório.")

