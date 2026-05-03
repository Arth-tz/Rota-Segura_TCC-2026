<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rota extends Model
{
    use HasFactory;

    protected $table = 'rota';
    protected $primaryKey = 'id_rota';

    protected $fillable = [
        'id_van',
        'data',
        'status',
        'horario_inicio_previsto',
        'horario_inicio_real',
        'horario_fim_previsto',
        'horario_fim_real',
        'distancia_km',
        'tempo_decorrido_minutos',
        'observacoes',
    ];

    protected $casts = [
        'data'                    => 'date',
        'horario_inicio_previsto' => 'datetime',
        'horario_inicio_real'     => 'datetime',
        'horario_fim_previsto'    => 'datetime',
        'horario_fim_real'        => 'datetime',
        'distancia_km'            => 'decimal:2',
    ];

    public function van()
    {
        return $this->belongsTo(Van::class, 'id_van', 'id_van');
    }

    public function paradas()
    {
        return $this->hasMany(Parada::class, 'id_rota', 'id_rota')->orderBy('ordem');
    }

    public function localizacoes()
    {
        return $this->hasMany(Localizacao::class, 'id_rota', 'id_rota')->orderBy('timestamp_captura');
    }

    public function ultimaLocalizacao()
    {
        return $this->hasOne(Localizacao::class, 'id_rota', 'id_rota')->latestOfMany('timestamp_captura');
    }
}
