<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SolicitacaoDisponibilidade extends Pivot
{
    use HasFactory;

    protected $table = 'solicitacao_disponibilidade';
    protected $primaryKey = 'id_solicitacao_disponibilidade';
    public $incrementing = true;

    protected $fillable = [
        'id_solicitacao',
        'id_disponibilidade',
        'preco_mensal',
    ];

    protected $casts = [
        'preco_mensal' => 'decimal:2',
    ];

    public function solicitacao()
    {
        return $this->belongsTo(Solicitacao::class, 'id_solicitacao', 'id_solicitacao');
    }

    public function disponibilidade()
    {
        return $this->belongsTo(Disponibilidade::class, 'id_disponibilidade', 'id_disponibilidade');
    }
}
