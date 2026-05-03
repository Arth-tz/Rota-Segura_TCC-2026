<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DisponibilidadePassageiro extends Model
{
    use HasFactory;

    protected $table = 'disponibilidade_passageiro';
    protected $primaryKey = 'id_disponibilidade_passageiro';

    protected $fillable = [
        'id_passageiro',
        'id_vinculo',
        'data',
        'vai',
        'motivo_falta',
        'observacoes',
        'marcado_por',
    ];

    protected $casts = [
        'data' => 'date',
        'vai'  => 'boolean',
    ];

    public function passageiro()
    {
        return $this->belongsTo(Passageiro::class, 'id_passageiro', 'id_passageiro');
    }

    public function vinculo()
    {
        return $this->belongsTo(Vinculo::class, 'id_vinculo', 'id_vinculo');
    }

    public function quemMarcou()
    {
        return $this->belongsTo(Usuario::class, 'marcado_por', 'id_usuario');
    }
}
