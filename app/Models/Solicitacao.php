<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Solicitacao extends Model
{
    use HasFactory;

    protected $table = 'solicitacao';
    protected $primaryKey = 'id_solicitacao';

    protected $fillable = [
        'id_van',
        'id_passageiro',
        'id_responsavel',
        'id_usuario_solicitante',
        'tipo_solicitante',
        'status',
        'mensagem',
        'motivo_recusa',
        'data_solicitacao',
        'data_resposta',
        'cancelado_em',
        'cancelado_por',
    ];

    protected $casts = [
        'data_solicitacao' => 'datetime',
        'data_resposta'    => 'datetime',
        'cancelado_em'     => 'datetime',
    ];

    public function van()
    {
        return $this->belongsTo(Van::class, 'id_van', 'id_van');
    }

    public function passageiro()
    {
        return $this->belongsTo(Passageiro::class, 'id_passageiro', 'id_passageiro');
    }

    public function responsavel()
    {
        return $this->belongsTo(Responsavel::class, 'id_responsavel', 'id_responsavel');
    }

    public function solicitante()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_solicitante', 'id_usuario');
    }

    public function vinculo()
    {
        return $this->hasOne(Vinculo::class, 'id_solicitacao', 'id_solicitacao');
    }

    public function disponibilidades()
    {
        return $this->belongsToMany(
            Disponibilidade::class,
            'solicitacao_disponibilidade',
            'id_solicitacao',
            'id_disponibilidade'
        )->withPivot('preco_mensal', 'dias_contratados')->withTimestamps();
    }
}
