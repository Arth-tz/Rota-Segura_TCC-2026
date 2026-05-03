<?php

namespace App\Http\Controllers\Motorista;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(){
        return Inertia::render('Motorista/Dashboard');
    }
}
