<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use illuminate\Database\Eloquent\Factories\HasFactory;


class Van extends Model
{
    use HasFactory;
    protected $fillable = [
        'motorista_id',
        'placa',
        'modelo',
        'marca',
        'ano_fabricacao',
        'cor',
        'capacidade',
        'crlv_num',
        'crlv_val',
        'seguro_num',
        "seguro_val",
        'ult_rev',
        "prox_rev",
        'foto_van',
        'disponivel',
    ];

    protected $casts = [
        'crlv_val'   => 'date',
        'seguro_val' => 'date',
        'ult_rev'    => 'date',
        'prox_rev'   => 'date',
        'disponivel' => 'boolean',
    ];

    public function motorista(){
        return $this->belongsTo(Motorista::class);
    }

}
