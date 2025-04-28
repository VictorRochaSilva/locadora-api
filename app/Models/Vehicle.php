<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de veículo (Vehicle).
 *
 * Esta classe representa um veículo na locadora, armazenando informações como
 * placa, marca, modelo e a tarifa diária de aluguel.
 */
class Vehicle extends Model
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
        'plate',      // Placa do veículo
        'make',       // Marca do veículo
        'model',      // Modelo do veículo
        'daily_rate'  // Tarifa diária de aluguel
    ];
}
