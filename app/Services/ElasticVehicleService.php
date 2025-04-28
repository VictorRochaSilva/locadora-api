<?php

namespace App\Services;

use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Illuminate\Support\Facades\Log;

/**
 * Serviço para interação com o Elasticsearch para veículos.
 *
 * Esta classe gerencia as operações de indexação, exclusão e busca de veículos
 * no Elasticsearch, bem como a criação do índice necessário se ele não existir.
 */
class ElasticVehicleService
{
    /**
     * Cliente do Elasticsearch.
     *
     * @var \Elastic\Elasticsearch\Client
     */
    protected $client;

    /**
     * Construtor do serviço.
     *
     * Inicializa o cliente do Elasticsearch com as configurações fornecidas
     * nas variáveis de ambiente.
     */
    public function __construct()
    {
        $this->client = ClientBuilder::create()
            ->setHosts([env('ELASTICSEARCH_HOST', 'elasticsearch:9200')])
            ->build();
    }

    /**
     * Garante que o índice de veículos existe.
     *
     * Verifica se o índice 'vehicles' já existe no Elasticsearch e, caso contrário,
     * chama o método para criar o índice.
     *
     * @return void
     */
    public function ensureIndexExists(): void
    {
        try {
            if (!$this->client->indices()->exists(['index' => 'vehicles'])->asBool()) {
                $this->createIndex();
            }
        } catch (ClientResponseException $e) {
            Log::error('Erro ao verificar/criar índice: ' . $e->getMessage());
            throw $e;
        }
    }


    /**
     * Cria o índice de veículos no Elasticsearch.
     *
     * Este índice contém mapeamentos para os campos de veículo e configurações
     * para a criação de shards e réplicas.
     *
     * @return \Elastic\Elasticsearch\Response\ElasticsearchResponse
     */
    protected function createIndex()
    {
        $params = [
            'index' => 'vehicles',
            'body' => [
                'mappings' => [
                    'properties' => [
                        'plate' => ['type' => 'keyword'],
                        'make' => ['type' => 'text'],
                        'model' => ['type' => 'text'],
                        'daily_rate' => ['type' => 'scaled_float', 'scaling_factor' => 100],
                        'created_at' => ['type' => 'date'],
                        'updated_at' => ['type' => 'date']
                    ]
                ],
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 1
                ]
            ]
        ];

        return $this->client->indices()->create($params);
    }

    /**
     * Indexa um veículo no Elasticsearch.
     *
     * Este método cria ou atualiza o documento do veículo no índice 'vehicles',
     * com os dados do veículo fornecido.
     *
     * @param  \App\Models\Vehicle  $vehicle  O veículo a ser indexado.
     * @return \Elastic\Elasticsearch\Response\ElasticsearchResponse
     */
    public function indexVehicle($vehicle)
    {
        $this->ensureIndexExists();

        $params = [
            'index' => 'vehicles',
            'id' => $vehicle->id,
            'body' => [
                'plate' => $vehicle->plate,
                'make' => $vehicle->make,
                'model' => $vehicle->model,
                'daily_rate' => $vehicle->daily_rate,
                'created_at' => $vehicle->created_at->toIso8601String(),
                'updated_at' => $vehicle->updated_at->toIso8601String()
            ],
            'refresh' => true
        ];

        return $this->client->index($params);
    }

    /**
     * Exclui um veículo do índice do Elasticsearch.
     *
     * Este método tenta excluir o veículo pelo ID. Se o documento não existir (erro 404),
     * ele apenas retorna true, caso contrário, relança o erro.
     *
     * @param  int  $vehicleId  ID do veículo a ser excluído.
     * @return bool|\Elastic\Elasticsearch\Response\ElasticsearchResponse
     * @throws \Elastic\Elasticsearch\Exception\ClientResponseException
     */
    public function deleteVehicle($vehicleId)
    {
        try {
            return $this->client->delete([
                'index' => 'vehicles',
                'id' => $vehicleId
            ]);
        } catch (ClientResponseException $e) {
            if ($e->getCode() === 404) {
                return true; // Documento já não existe
            }
            throw $e;
        }
    }

    /**
     * Realiza uma busca de veículos no Elasticsearch.
     *
     * Este método executa uma busca por um termo na placa, marca, modelo ou
     * na placa usando o operador "wildcard" (coringa).
     *
     * @param  string  $query  O termo a ser pesquisado.
     * @return array  Lista de veículos que correspondem à busca.
     */
    public function searchVehicle($query)
    {
        $params = [
            'index' => 'vehicles',
            'body' => [
                'query' => [
                    'bool' => [
                        'should' => [
                            ['match' => ['plate' => $query]],
                            ['match' => ['make' => $query]],
                            ['match' => ['model' => $query]],
                            ['wildcard' => ['plate' => "*$query*"]]
                        ]
                    ]
                ],
                'size' => 100
            ]
        ];

        $response = $this->client->search($params);
        return $response['hits']['hits'];
    }
}
