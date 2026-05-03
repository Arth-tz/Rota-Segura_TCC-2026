<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Parada extends Model
{
    use HasFactory;

    protected $table = 'parada';
    protected $primaryKey = 'id_parada';

    protected $fillable = [
        'id_rota',
        'id_endereco',
        'ordem',
        'tipo',
        'horario_previsto',
        'horario_real',
        'observacoes',
    ];

    public function rota()
    {
        return $this->belongsTo(Rota::class, 'id_rota', 'id_rota');
    }

    public function endereco()
    {
        return $this->belongsTo(Endereco::class, 'id_endereco', 'id_endereco');
    }

    public function passageiros()
    {
        return $this->belongsToMany(
            Passageiro::class,
            'parada_passageiro',
            'id_parada',
            'id_passageiro'
        )->withTimestamps();
    }
}
