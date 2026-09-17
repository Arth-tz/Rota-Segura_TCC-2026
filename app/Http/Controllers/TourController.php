<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class TourController extends Controller
{
    public function concluir(): RedirectResponse
    {
        auth()->user()->update(['tour_visto' => true]);
        return back();
    }
}
