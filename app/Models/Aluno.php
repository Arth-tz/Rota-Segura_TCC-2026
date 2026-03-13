<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use illuminate\Database\Eloquent\Factories\HasFactory;

class Aluno extends Model
{   
    use HasFactory;
    protected $fillable = [
        'responsavel_id',
        'first_name',
        'last_name',
        'data_nascimento',
        'escola_nome',
        'escola_endereco',
        'turno',
        'serie_ano',
        'endereco_embarque',
        'endereco_desembarque',
        'obs_medica',
        'ctt_emergencia',
        'tel_emergencia',
        'foto',
        'ativo',
    ];

    
    protected $casts = [
        'data_nascimento' => 'date',
        'ativo' => 'boolean',
    ];

    public function responsavel(){
        return $this->belongsTo(Responsavel::class);
    }

    public function motorista(){
        return $this->belongsTo(Motorista::class);
    }
}
