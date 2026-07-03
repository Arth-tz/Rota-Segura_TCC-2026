<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ParadaPassageiro extends Pivot
{
    use HasFactory;

    protected $table = 'parada_passageiro';
    protected $primaryKey = 'id_parada_passageiro';
    public $incrementing = true;

    protected $fillable = [
        'id_parada',
        'id_passageiro',
        'embarque_em',
        'desembarque_em',
        'marcado_por',
        'metodo_confirmacao',
    ];

    public function parada()
    {
        return $this->belongsTo(Parada::class, 'id_parada', 'id_parada');
    }

    public function passageiro()
    {
        return $this->belongsTo(Passageiro::class, 'id_passageiro', 'id_passageiro');
    }
}
