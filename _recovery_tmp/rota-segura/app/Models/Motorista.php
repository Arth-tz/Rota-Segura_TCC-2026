<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Motorista extends Model
{
    use HasFactory;

    protected $table = 'motorista';
    protected $primaryKey = 'id_motorista';

    protected $fillable = [
        'id_usuario',
        'cnh_numero',
        'cnh_categoria',
        'cnh_validade',
        'cnh_foto_url',
        'status_aprovacao',
        'motivo_rejeicao',
        'data_avaliacao_documento',
        'id_usuario_avaliador',
    ];

    protected $casts = [
        'cnh_validade'             => 'date',
        'data_avaliacao_documento' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function avaliador()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_avaliador', 'id_usuario');
    }

    public function van()
    {
        return $this->hasOne(Van::class, 'id_motorista', 'id_motorista');
    }
}
