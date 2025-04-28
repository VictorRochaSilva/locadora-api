<?php

namespace App\Jobs;

use App\Services\ElasticVehicleService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class DeleteVehicle
 *
 * Job responsável por remover um veículo do índice do Elasticsearch de forma assíncrona.
 */
class DeleteVehicle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * ID do veículo a ser deletado do Elasticsearch.
     *
     * @var int
     */
    protected int $vehicleId;

    /**
     * Cria uma nova instância do Job.
     *
     * @param  int  $vehicleId  ID do veículo a ser deletado.
     */
    public function __construct(int $vehicleId)
    {
        $this->vehicleId = $vehicleId;
    }

    /**
     * Executa o Job para remover o veículo do Elasticsearch.
     *
     * @param  ElasticVehicleService  $elasticService
     * @return void
     */
    public function handle(ElasticVehicleService $elasticService): void
    {
        $elasticService->deleteVehicle($this->vehicleId);
    }
}
