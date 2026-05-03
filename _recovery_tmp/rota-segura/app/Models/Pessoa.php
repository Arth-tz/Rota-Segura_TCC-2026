<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pessoa extends Model
{
    use HasFactory;

    protected $table = 'pessoa';
    protected $primaryKey = 'id_pessoa';

    protected $fillable = [
        'nome',
        'cpf',
        'data_nascimento',
        'telefone',
        'foto_url',
        'foto_upload_data',
        'ativo',
    ];

    protected $casts = [
        'data_nascimento'  => 'date',
        'foto_upload_data' => 'datetime',
        'ativo'            => 'boolean',
    ];

    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'id_pessoa', 'id_pessoa');
    }

    public function passageiro()
    {
        return $this->hasOne(Passageiro::class, 'id_pessoa', 'id_pessoa');
    }

    public function getCpfFormatadoAttribute(): string
    {
        $cpf = $this->cpf;
        return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' .
               substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
    }

    public function getIdadeAttribute(): int
    {
        return \Carbon\Carbon::parse($this->data_nascimento)->age;
    }
}
