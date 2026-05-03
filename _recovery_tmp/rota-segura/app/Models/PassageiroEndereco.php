<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PassageiroEndereco extends Model
{
    use HasFactory;

    protected $table = 'passageiro_endereco';
    protected $primaryKey = 'id_passageiro_endereco';

    protected $fillable = [
        'id_passageiro',
        'id_endereco',
        'tipo',
        'principal',
    ];

    protected $casts = [
        'principal' => 'boolean',
    ];

    public function passageiro()
    {
        return $this->belongsTo(Passageiro::class, 'id_passageiro', 'id_passageiro');
    }

    public function endereco()
    {
        return $this->belongsTo(Endereco::class, 'id_endereco', 'id_endereco');
    }
}
