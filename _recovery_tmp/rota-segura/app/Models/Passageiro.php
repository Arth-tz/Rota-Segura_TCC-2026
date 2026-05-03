<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Passageiro extends Model
{
    use HasFactory;

    protected $table = 'passageiro';
    protected $primaryKey = 'id_passageiro';

    protected $fillable = [
        'id_pessoa',
        'id_usuario',
        'observacoes_medicas',
        'ativo',
        'data_inscricao',
    ];

    protected $casts = [
        'ativo'          => 'boolean',
        'data_inscricao' => 'date',
    ];

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'id_pessoa', 'id_pessoa');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function responsaveis()
    {
        return $this->belongsToMany(
            Responsavel::class,
            'responsavel_passageiro',
            'id_passageiro',
            'id_responsavel'
        )->withPivot('data_inicio', 'data_fim')->withTimestamps();
    }

    public function enderecos()
    {
        return $this->hasMany(PassageiroEndereco::class, 'id_passageiro', 'id_passageiro');
    }

    public function vinculoAtivo()
    {
        return $this->hasOne(Vinculo::class, 'id_passageiro', 'id_passageiro')
                    ->where('status', 'ativo');
    }

    public function vinculos()
    {
        return $this->hasMany(Vinculo::class, 'id_passageiro', 'id_passageiro');
    }
}
