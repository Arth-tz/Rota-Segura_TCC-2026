<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';

    public $rememberTokenName = 'remember_token';

    protected $fillable = [
        'id_pessoa',
        'email',
        'senha_hash',
        'role',
        'ativo',
        'ultimo_login',
        'tentativas_falhas',
        'bloqueado_ate',
    ];

    protected $hidden = [
        'senha_hash',
        'remember_token',
    ];

    protected $casts = [
        'ativo'         => 'boolean',
        'ultimo_login'  => 'datetime',
        'bloqueado_ate' => 'datetime',
        'role'          => \App\Enums\UserRole::class,
    ];

    public function getAuthPassword()
    {
        return $this->senha_hash;
    }

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'id_pessoa', 'id_pessoa');
    }

    public function responsavel()
    {
        return $this->hasOne(Responsavel::class, 'id_usuario', 'id_usuario');
    }

    public function motorista()
    {
        return $this->hasOne(Motorista::class, 'id_usuario', 'id_usuario');
    }
}
