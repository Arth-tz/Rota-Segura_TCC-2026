<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterMotoristaRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\TryCatch;

class RegisterMotoristaController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/RegisterMotorista');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterMotoristaRequest $request): RedirectResponse
    {
        try {
            DB::transaction( function () use ($request){
                $user = User::create([
                    'first_name' => $request->first_name,
                    'last_name'  => $request->last_name,
                    'email'      => $request->email,
                    'password'   => Hash::make($request->password),
                    'cpf'        => $request->cpf,
                    'phone'      => $request->phone,
                    'role'       => UserRole::Motorista,
                ]);

                $user->motorista()->create([
                    'cnh_numero' => $request->cnh_numero,
                    'cnh_categoria' => $request->cnh_categoria,
                    'cnh_validade' => $request->cnh_validade,
                ]);

                Auth::login($user);
            });

            return redirect()->route('motorista.dashboard');
            
        } catch (\Exception $e) {
            dd($e->getMessage());
            return back()->withErrors(['geral' => 'Erro ao criar conta. Tente novamente.']);

        }
    }
}
