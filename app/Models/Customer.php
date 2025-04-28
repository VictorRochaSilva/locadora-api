<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de cliente (Customer).
 *
 * Esta classe representa um cliente da locadora de veículos, armazenando
 * informações como nome, e-mail, telefone e CNH (Carteira Nacional de Habilitação).
 */
class Customer extends Model
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
        'name',   // Nome do cliente
        'email',  // E-mail do cliente
        'phone',  // Telefone do cliente
        'cnh'     // Número da CNH (Carteira Nacional de Habilitação)
    ];
}
