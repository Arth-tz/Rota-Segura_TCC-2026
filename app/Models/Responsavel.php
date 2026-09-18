<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Responsavel extends Model
{
    use HasFactory;

    protected $table = 'responsavel';
    protected $primaryKey = 'id_responsavel';

    protected $fillable = [
        'id_usuario',
        'tipo_responsavel',
        'telefone_emergencia',
    ];

    protected $casts = [];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function passageiros()
    {
        return $this->belongsToMany(
            Passageiro::class,
            'responsavel_passageiro',
            'id_responsavel',
            'id_passageiro'
        )->withPivot('data_inicio', 'data_fim')->withTimestamps();
    }

    public function solicitacoes()
    {
        return $this->hasMany(Solicitacao::class, 'id_responsavel', 'id_responsavel');
    }
}
