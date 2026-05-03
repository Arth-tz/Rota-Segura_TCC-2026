<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Enums\UserRole;
use App\Http\Requests\Auth\RegisterResponsavelRequest;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;

class RegisterResponsavelController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/RegisterResponsavel');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterResponsavelRequest $request): RedirectResponse
    {
        try {
        DB::transaction(function () use ($request) {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
                'email'      => $request->email,
                'password'   => Hash::make($request->password),
                'cpf'        => $request->cpf,
                'phone'      => $request->phone,
                'role'       => UserRole::Responsavel,
            ]);

            $user->responsavel()->create();

            Auth::login($user);
        });

        return redirect()->route('responsavel.alunos.create'); //revisar se mandará direto ao dashboard ou para o cadastro de filho...amanhã pq to com sono

    } catch (\Exception $e) {
        dd($e->getMessage());
        return back()->withErrors(['geral' => 'Erro ao criar conta. Tente novamente.']);
    }
    }
}
