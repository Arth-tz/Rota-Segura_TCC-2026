<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Localizacao extends Model
{
    use HasFactory;

    protected $table = 'localizacao';
    protected $primaryKey = 'id_localizacao';

    const UPDATED_AT = null;

    protected $fillable = [
        'id_rota',
        'id_van',
        'latitude',
        'longitude',
        'altitude',
        'precisao_metros',
        'fonte_localizacao',
        'numero_satelites',
        'timestamp_captura',
    ];

    protected $casts = [
        'latitude'          => 'decimal:7',
        'longitude'         => 'decimal:7',
        'altitude'          => 'decimal:2',
        'precisao_metros'   => 'decimal:2',
        'timestamp_captura' => 'datetime',
    ];

    public function rota()
    {
        return $this->belongsTo(Rota::class, 'id_rota', 'id_rota');
    }

    public function van()
    {
        return $this->belongsTo(Van::class, 'id_van', 'id_van');
    }
}
