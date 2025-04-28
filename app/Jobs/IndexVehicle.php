<?php

namespace App\Jobs;

use App\Models\Vehicle;
use App\Services\ElasticVehicleService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Class IndexVehicle
 *
 * Job responsável por indexar um veículo no Elasticsearch de forma assíncrona.
 */
class IndexVehicle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Dados do veículo a ser indexado.
     *
     * @var Vehicle
     */
    protected Vehicle $vehicle;

    /**
     * Cria uma nova instância do job.
     *
     * @param  Vehicle  $vehicle  Instância do veículo para indexação.
     */
    public function __construct(Vehicle $vehicle)
    {
        $this->vehicle = $vehicle;
    }

    /**
     * Executa o job para indexar o veículo.
     *
     * @param  ElasticVehicleService  $elasticService
     * @return void
     */
    public function handle(ElasticVehicleService $elasticService): void
    {
        try {
            if (!$this->vehicleExists()) {
                return;
            }

            $elasticService->indexVehicle($this->vehicle);
        } catch (Throwable $e) {
            Log::error('Falha ao indexar veículo.', [
                'vehicle_id' => $this->vehicle->id,
                'error' => $e->getMessage(),
            ]);

            // Re-throw para o sistema de filas do Laravel controlar tentativas/retries
            throw $e;
        }
    }

    /**
     * Verifica se o veículo ainda existe no banco de dados.
     *
     * @return bool
     */
    protected function vehicleExists(): bool
    {
        return Vehicle::where('id', $this->vehicle->id)->exists();
    }
}
