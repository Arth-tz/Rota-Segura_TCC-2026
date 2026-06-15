<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DisponibilidadeDia extends Model
{
    use HasFactory;

    protected $table = 'disponibilidade_dia';
    protected $primaryKey = 'id_disponibilidade_dia';

    protected $fillable = [
        'id_disponibilidade',
        'dia_semana',
    ];

    public function disponibilidade()
    {
        return $this->belongsTo(Disponibilidade::class, 'id_disponibilidade', 'id_disponibilidade');
    }
}
