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
        $usuario = $request->user()?->loadMissing('pessoa');

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $usuario ? [
                    'id_usuario' => $usuario->id_usuario,
                    'email' => $usuario->email,
                    'role' => $usuario->role,
                    'nome' => $usuario->pessoa?->nome,
                    'id_pessoa' => $usuario->id_pessoa,
                ] : null,
            ],
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
        ];
    }
}
