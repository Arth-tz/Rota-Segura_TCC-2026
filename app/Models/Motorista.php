<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Motorista extends Model
{
    use HasFactory;
    Protected $fillable = [
        'user_id',
        'cnh_numero',
        'cnh_categoria',
        'cnh_validade',
        'cnh_foto',
        'crlv',
        'ant_crim',
        'status_aprov',
        'data_aprov',
    ];

    protected $casts = [
        'cnh_validade' => 'date',
        'data_aprov'   => 'date',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function van(){
        return $this->hasOne(Van::class);
    }

    public function alunos(){
        return $this->hasMany(Aluno::class);
    }
}
