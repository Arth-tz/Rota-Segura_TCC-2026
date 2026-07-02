<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    'id_usuario' => $request->user()->id_usuario,
                    'email'      => $request->user()->email,
                    'role'       => $request->user()->role,
                    'nome'       => $request->user()->pessoa?->nome,
                    'foto_url'   => $request->user()->pessoa?->foto_url,
                    'telefone'   => $request->user()->pessoa?->telefone,
                ] : null,
            ],
            'flash' => [
                'sucesso' => fn () => $request->session()->get('sucesso'),
                'aviso'   => fn () => $request->session()->get('aviso'),
                'erro'    => fn () => $request->session()->get('erro'),
            ],
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
        ];
    }   
}
