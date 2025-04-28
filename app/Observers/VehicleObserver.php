<?php

namespace App\Observers;

use App\Models\Vehicle;
use App\Jobs\IndexVehicle;
use App\Jobs\DeleteVehicle;

/**
 * Classe Observer para o modelo Vehicle.
 *
 * Responsável por disparar Jobs de indexação no Elasticsearch quando um veículo
 * for criado ou atualizado, e por disparar um Job de remoção do índice quando
 * um veículo for deletado.
 */
class VehicleObserver
{
    /**
     * Disparado após um veículo ser criado.
     *
     * Este método enfileira o Job para indexar o veículo no Elasticsearch.
     *
     * @param  Vehicle  $vehicle  Instância do veículo criado.
     * @return void
     */
    public function created(Vehicle $vehicle): void
    {
        IndexVehicle::dispatch($vehicle);
    }

    /**
     * Disparado após um veículo ser atualizado.
     *
     * Este método enfileira o Job para reindexar o veículo no Elasticsearch.
     *
     * @param  Vehicle  $vehicle  Instância do veículo atualizado.
     * @return void
     */
    public function updated(Vehicle $vehicle): void
    {
        IndexVehicle::dispatch($vehicle);
    }

    /**
     * Disparado após um veículo ser deletado.
     *
     * Este método enfileira o Job para remover o veículo do índice do Elasticsearch.
     *
     * @param  Vehicle  $vehicle  Instância do veículo deletado.
     * @return void
     */
    public function deleted(Vehicle $vehicle): void
    {
        DeleteVehicle::dispatch($vehicle->id);
    }
}
