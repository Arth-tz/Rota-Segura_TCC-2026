<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin      = 'admin';
    case Motorista  = 'motorista';
    case Responsavel = 'responsavel';
    case Passageiro = 'passageiro';
}
