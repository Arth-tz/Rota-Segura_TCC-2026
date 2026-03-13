<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin       = 'admin';
    case Responsavel = 'responsavel';
    case Motorista   = 'motorista';
}