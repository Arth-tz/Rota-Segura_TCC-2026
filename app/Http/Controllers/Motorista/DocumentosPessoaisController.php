<?php

namespace App\Http\Controllers\Motorista;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class DocumentosPessoaisController extends Controller
{
    private const DOC_MAP = [
        'cnh'              => 'cnh_foto_url',
        'certidao'         => 'certidao_antecedentes_url',
        'curso_transporte' => 'curso_transporte_url',
    ];

    public function index(): Response|RedirectResponse
    {
        $motorista = auth()->user()->motorista;

        if (!$motorista) {
            return redirect()->route('motorista.dashboard');
        }

        return Inertia::render('Motorista/Documentos', [
            'motorista' => [
                'cnh_numero'                => $motorista->cnh_numero,
                'cnh_categoria'             => $motorista->cnh_categoria,
                'cnh_validade'              => $motorista->cnh_validade?->format('d/m/Y'),
                'cnh_foto_url'              => $motorista->cnh_foto_url,
                'certidao_antecedentes_url' => $motorista->certidao_antecedentes_url,
                'curso_transporte_url'      => $motorista->curso_transporte_url,
                'status_aprovacao'          => $motorista->status_aprovacao,
            ],
        ]);
    }

    public function upload(Request $request, string $tipo): RedirectResponse
    {
        if (!array_key_exists($tipo, self::DOC_MAP)) {
            abort(404);
        }

        $motorista = auth()->user()->motorista;
        if (!$motorista) {
            return redirect()->route('motorista.dashboard');
        }

        $request->validate([
            'arquivo' => ['required', 'file', 'max:5120', 'mimes:jpg,jpeg,png,webp,pdf'],
        ], [
            'arquivo.required' => 'Selecione um arquivo.',
            'arquivo.max'      => 'O arquivo pode ter no máximo 5 MB.',
            'arquivo.mimes'    => 'Formato aceito: JPG, PNG, WebP ou PDF.',
        ]);

        $coluna = self::DOC_MAP[$tipo];

        if ($motorista->$coluna) {
            $old = ltrim(parse_url($motorista->$coluna, PHP_URL_PATH), '/storage/');
            Storage::disk(config('filesystems.upload'))->delete($old);
        }

        $file = $request->file('arquivo');
        $ext  = strtolower($file->getClientOriginalExtension());
        $path = $file->storeAs("motoristas/{$motorista->id_motorista}/documentos", "{$tipo}.{$ext}", config('filesystems.upload'));

        $motorista->update([$coluna => Storage::disk(config('filesystems.upload'))->url($path)]);

        return back()->with('sucesso', 'Documento enviado com sucesso.');
    }
}
