<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VinculoDisponibilidade extends Pivot
{
    use HasFactory;

    protected $table = 'vinculo_disponibilidade';
    protected $primaryKey = 'id_vinculo_disponibilidade';
    public $incrementing = true;

    protected $fillable = [
        'id_vinculo',
        'id_disponibilidade',
        'preco_mensal',
    ];

    protected $casts = [
        'preco_mensal' => 'decimal:2',
    ];

    public function vinculo()
    {
        return $this->belongsTo(Vinculo::class, 'id_vinculo', 'id_vinculo');
    }

    public function disponibilidade()
    {
        return $this->belongsTo(Disponibilidade::class, 'id_disponibilidade', 'id_disponibilidade');
    }
}
