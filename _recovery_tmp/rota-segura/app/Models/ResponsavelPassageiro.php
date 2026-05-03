<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ResponsavelPassageiro extends Pivot
{
    use HasFactory;

    protected $table = 'responsavel_passageiro';
    protected $primaryKey = 'id_responsavel_passageiro';
    public $incrementing = true;

    protected $fillable = [
        'id_responsavel',
        'id_passageiro',
        'data_inicio',
        'data_fim',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim'    => 'date',
    ];

    public function responsavel()
    {
        return $this->belongsTo(Responsavel::class, 'id_responsavel', 'id_responsavel');
    }

    public function passageiro()
    {
        return $this->belongsTo(Passageiro::class, 'id_passageiro', 'id_passageiro');
    }
}
