<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Endereco extends Model
{
    use HasFactory;

    protected $table = 'endereco';
    protected $primaryKey = 'id_endereco';

    protected $fillable = [
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'cep',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude'  => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function passageiros()
    {
        return $this->belongsToMany(
            Passageiro::class,
            'passageiro_endereco',
            'id_endereco',
            'id_passageiro'
        )->withPivot('tipo', 'principal')->withTimestamps();
    }

    public function destino()
    {
        return $this->hasOne(Destino::class, 'id_endereco', 'id_endereco');
    }

    public function getEnderecoCompletoAttribute(): string
    {
        $endereco = $this->logradouro;
        if ($this->numero) $endereco .= ', ' . $this->numero;
        if ($this->complemento) $endereco .= ' - ' . $this->complemento;
        $endereco .= ' - ' . $this->bairro;
        $endereco .= ', ' . $this->cidade . '/' . $this->estado;
        return $endereco;
    }
}
