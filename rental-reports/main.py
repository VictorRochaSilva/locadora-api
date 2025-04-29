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

# Modelo de relatório
class Report(BaseModel):
    plate: str
    make: str
    model: str
    total_rentals: int
    total_revenue: float

# Modelo de relatório com totais gerais
class RevenueSummary(BaseModel):
    total_rentals: int
    total_revenue: float
    vehicles: List[Report]

@app.get("/reports/revenue", response_model=RevenueSummary)
def get_revenue_report(start: str, end: str):
    try:
        # Convertendo as strings para o formato de data
        start_date = datetime.strptime(start, '%Y-%m-%d')
        end_date = datetime.strptime(end, '%Y-%m-%d')

        with engine.connect() as conn:
            # Consulta para dados dos veículos
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

            logging.info(f'Report generated for start: {start_date}, end: {end_date}')

            # Gerar a lista de veículos com os dados de aluguéis
            vehicles = [Report(**row._mapping) for row in result.fetchall()]

            # Calcular totais gerais
            total_rentals = sum(vehicle.total_rentals for vehicle in vehicles)
            total_revenue = sum(vehicle.total_revenue for vehicle in vehicles)

            # Retornar o resumo completo
            return RevenueSummary(
                total_rentals=total_rentals,
                total_revenue=total_revenue,
                vehicles=vehicles
            )

    except Exception as e:
        logging.error(f"Erro ao gerar relatório: {str(e)}")
        raise HTTPException(status_code=500, detail="Erro ao gerar o relatório.")
