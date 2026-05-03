<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vinculo extends Model
{
    use HasFactory;

    protected $table = 'vinculo';
    protected $primaryKey = 'id_vinculo';

    protected $fillable = [
        'id_van',
        'id_passageiro',
        'id_solicitacao',
        'status',
        'data_inicio',
        'data_fim',
        'observacoes',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim'    => 'date',
    ];

    public function van()
    {
        return $this->belongsTo(Van::class, 'id_van', 'id_van');
    }

    public function passageiro()
    {
        return $this->belongsTo(Passageiro::class, 'id_passageiro', 'id_passageiro');
    }

    public function solicitacao()
    {
        return $this->belongsTo(Solicitacao::class, 'id_solicitacao', 'id_solicitacao');
    }

    public function disponibilidades()
    {
        return $this->hasMany(DisponibilidadePassageiro::class, 'id_vinculo', 'id_vinculo');
    }
}
