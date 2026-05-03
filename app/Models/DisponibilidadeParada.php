<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DisponibilidadeParada extends Model
{
    use HasFactory;

    protected $table = 'disponibilidade_parada';
    protected $primaryKey = 'id_disponibilidade_parada';

    protected $fillable = [
        'id_disponibilidade',
        'id_endereco',
        'ordem',
        'tipo',
        'horario_previsto',
        'ativa',
    ];

    protected $casts = [
        'ativa' => 'boolean',
        'ordem' => 'integer',
    ];

    public function disponibilidade()
    {
        return $this->belongsTo(Disponibilidade::class, 'id_disponibilidade', 'id_disponibilidade');
    }

    public function endereco()
    {
        return $this->belongsTo(Endereco::class, 'id_endereco', 'id_endereco');
    }
}
