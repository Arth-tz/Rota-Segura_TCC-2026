<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Destino extends Model
{
    use HasFactory;

    protected $table = 'destino';
    protected $primaryKey = 'id_destino';

    protected $fillable = ['id_endereco', 'nome', 'tipo', 'ativo'];

    protected $casts = ['ativo' => 'boolean'];

    public function endereco()
    {
        return $this->belongsTo(Endereco::class, 'id_endereco', 'id_endereco');
    }
}
