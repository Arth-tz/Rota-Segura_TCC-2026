<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Van extends Model
{
    use HasFactory;

    protected $table = 'van';
    protected $primaryKey = 'id_van';

    protected $fillable = [
        'id_motorista',
        'placa',
        'modelo',
        'marca',
        'ano_fabricacao',
        'cor',
        'capacidade_passageiros',
        'status_aprovacao',
        'status_operacional',
        'motivo_rejeicao',
        'id_usuario_avaliador',
        'data_avaliacao',
        'foto_url',
        'crlv_url',
        'crlv_validade',
        'seguro_url',
        'seguro_validade',
        'ipva_comprovante_url',
        'ipva_comprovante_data',
        'documentacao_completa',
        'data_ultima_inspecao',
        'proxima_inspecao_prevista',
    ];

    protected $casts = [
        'crlv_validade'             => 'date',
        'seguro_validade'           => 'date',
        'ipva_comprovante_data'     => 'date',
        'data_ultima_inspecao'      => 'date',
        'proxima_inspecao_prevista' => 'date',
        'data_avaliacao'            => 'datetime',
        'documentacao_completa'     => 'boolean',
    ];

    public function motorista()
    {
        return $this->belongsTo(Motorista::class, 'id_motorista', 'id_motorista');
    }

    public function avaliador()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_avaliador', 'id_usuario');
    }

    public function disponibilidades()
    {
        return $this->hasMany(Disponibilidade::class, 'id_van', 'id_van');
    }

    public function vinculos()
    {
        return $this->hasMany(Vinculo::class, 'id_van', 'id_van');
    }

    public function rotas()
    {
        return $this->hasMany(Rota::class, 'id_van', 'id_van');
    }

    public function solicitacoes()
    {
        return $this->hasMany(Solicitacao::class, 'id_van', 'id_van');
    }
}
