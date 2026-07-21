<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin      = 'admin';
    case Motorista  = 'motorista';
    case Responsavel = 'responsavel';
    case Passageiro = 'passageiro';

    public function dashboardRouteName(): ?string
    {
        return match ($this) {
            self::Admin       => 'admin.dashboard',
            self::Motorista   => 'motorista.dashboard',
            self::Responsavel => 'responsavel.dashboard',
            default           => null,
        };
    }
}
