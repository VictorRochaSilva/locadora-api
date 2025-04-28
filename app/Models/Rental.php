<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de aluguel (Rental).
 *
 * Esta classe representa um aluguel de veículo, armazenando informações sobre
 * o veículo alugado, o cliente, as datas de início e término, e o valor total
 * do aluguel. Ela também define os relacionamentos com os modelos `Vehicle` 
 * e `Customer`.
 */
class Rental extends Model
{
    use HasFactory;

    /**
     * Atributos que são atribuíveis em massa.
     *
     * Esses campos podem ser preenchidos em massa via criação ou atualização de
     * registros no banco de dados. O uso do `fillable` ajuda a evitar
     * vulnerabilidades de atribuição em massa.
     *
     * @var array
     */
    protected $fillable = [
        'vehicle_id',  // ID do veículo alugado
        'customer_id', // ID do cliente que está alugando
        'start_date',  // Data de início do aluguel
        'end_date',    // Data de término do aluguel
        'total_amount', // Valor total do aluguel
    ];

    /**
     * Atributos que devem ser convertidos para instâncias de data.
     *
     * Especifica quais atributos devem ser tratados como instâncias de Carbon,
     * permitindo fácil manipulação de datas e horários.
     *
     * @var array
     */
    protected $dates = [
        'start_date',  // Data de início
        'end_date',    // Data de término
    ];

    /**
     * Relacionamento com o modelo `Vehicle`.
     *
     * Um aluguel pertence a um veículo específico. Esta função define o
     * relacionamento de "pertence a" entre `Rental` e `Vehicle`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Relacionamento com o modelo `Customer`.
     *
     * Um aluguel pertence a um cliente específico. Esta função define o
     * relacionamento de "pertence a" entre `Rental` e `Customer`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
