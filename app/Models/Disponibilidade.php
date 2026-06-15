<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Disponibilidade extends Model
{
    use HasFactory;

    protected $table = 'disponibilidade';
    protected $primaryKey = 'id_disponibilidade';

    protected $fillable = [
        'id_van',
        'nome',
        'turno',
        'preco_mensal',
        'capacidade_total',
        'ativa',
    ];

    protected $casts = [
        'ativa'        => 'boolean',
        'preco_mensal' => 'decimal:2',
    ];

    public function van()
    {
        return $this->belongsTo(Van::class, 'id_van', 'id_van');
    }

    public function dias()
    {
        return $this->hasMany(DisponibilidadeDia::class, 'id_disponibilidade', 'id_disponibilidade');
    }

    public function paradas()
    {
        return $this->hasMany(DisponibilidadeParada::class, 'id_disponibilidade', 'id_disponibilidade')
                    ->orderBy('ordem');
    }

    public function solicitacoes()
    {
        return $this->belongsToMany(
            Solicitacao::class,
            'solicitacao_disponibilidade',
            'id_disponibilidade',
            'id_solicitacao'
        )->withPivot('preco_mensal')->withTimestamps();
    }

    public function vinculos()
    {
        return $this->belongsToMany(
            Vinculo::class,
            'vinculo_disponibilidade',
            'id_disponibilidade',
            'id_vinculo'
        )->withPivot('preco_mensal')->withTimestamps();
    }
}
