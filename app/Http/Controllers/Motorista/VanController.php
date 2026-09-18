<?php

namespace App\Http\Controllers\Motorista;

use App\Http\Controllers\Controller;
use App\Models\Van;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class VanController extends Controller
{
    private const REGRAS_FOTO = ['nullable', 'image', 'max:2048', 'mimes:jpg,jpeg,png,webp'];

    private const DOC_MAP = [
        'crlv'                  => ['url_col' => 'crlv_url',                  'val_col' => 'crlv_validade'],
        'seguro'                => ['url_col' => 'seguro_url',                 'val_col' => 'seguro_validade'],
        'autorizacao_municipal' => ['url_col' => 'autorizacao_municipal_url',  'val_col' => 'autorizacao_municipal_validade'],
    ];

    // ── CRIAR ────────────────────────────────────────────────────────────────

    public function create(): Response|RedirectResponse
    {
        $motorista = auth()->user()->motorista;

        if ($motorista && $motorista->van) {
            return redirect()->route('motorista.dashboard')
                ->with('aviso', 'Você já possui uma van cadastrada.');
        }

        return Inertia::render('Motorista/Van/Criar');
    }

    public function store(Request $request): RedirectResponse
    {
        $dados = $request->validate([
            'placa'                  => ['required', 'string', 'max:10', 'unique:van,placa', 'regex:/^[A-Z]{3}[0-9][A-Z0-9][0-9]{2}$/'],
            'nome_servico'           => ['nullable', 'string', 'max:150'],
            'marca'                  => ['required', 'string', 'max:100'],
            'modelo'                 => ['required', 'string', 'max:100'],
            'ano_fabricacao'         => ['required', 'integer', 'min:1990', 'max:' . (date('Y') + 1)],
            'cor'                    => ['required', 'string', 'max:50'],
            'capacidade_passageiros' => ['required', 'integer', 'min:1', 'max:30'],
            'foto'                   => self::REGRAS_FOTO,
            'foto_verso'             => self::REGRAS_FOTO,
            'foto_interior'          => self::REGRAS_FOTO,
        ], [
            'placa.required'                  => 'A placa é obrigatória.',
            'placa.unique'                    => 'Esta placa já está cadastrada.',
            'placa.regex'                     => 'Placa inválida. Use o formato ABC1234 ou ABC1D23.',
            'marca.required'                  => 'A marca é obrigatória.',
            'modelo.required'                 => 'O modelo é obrigatório.',
            'ano_fabricacao.required'         => 'O ano de fabricação é obrigatório.',
            'ano_fabricacao.min'              => 'Ano de fabricação inválido (mínimo 1990).',
            'cor.required'                    => 'A cor é obrigatória.',
            'capacidade_passageiros.required' => 'A capacidade é obrigatória.',
            'capacidade_passageiros.min'      => 'Capacidade mínima: 1 passageiro.',
            'capacidade_passageiros.max'      => 'Capacidade máxima: 30 passageiros.',
        ]);

        $motorista = auth()->user()->motorista;

        if (!$motorista) {
            return back()->withErrors(['geral' => 'Perfil de motorista não encontrado.']);
        }

        if ($motorista->van) {
            return redirect()->route('motorista.dashboard')
                ->with('aviso', 'Você já possui uma van cadastrada.');
        }

        $van = DB::transaction(function () use ($dados, $motorista) {
            return Van::create([
                'id_motorista'           => $motorista->id_motorista,
                'placa'                  => strtoupper($dados['placa']),
                'nome_servico'           => $dados['nome_servico'] ?? null,
                'marca'                  => $dados['marca'],
                'modelo'                 => $dados['modelo'],
                'ano_fabricacao'         => $dados['ano_fabricacao'],
                'cor'                    => $dados['cor'],
                'capacidade_passageiros' => $dados['capacidade_passageiros'],
                'status_aprovacao'       => 'pendente',
                'documentacao_completa'  => false,
            ]);
        });

        $this->salvarFotoSeEnviada($request, $van, 'foto',         'foto_url');
        $this->salvarFotoSeEnviada($request, $van, 'foto_verso',   'foto_verso_url',   'verso');
        $this->salvarFotoSeEnviada($request, $van, 'foto_interior', 'foto_interior_url', 'interior');

        return redirect()->route('motorista.van.documentos')
            ->with('sucesso', 'Van cadastrada! Adicione os documentos para iniciar a aprovação.');
    }

    // ── EDITAR ───────────────────────────────────────────────────────────────

    public function edit(): Response|RedirectResponse
    {
        $van = auth()->user()->motorista?->van;

        if (!$van) {
            return redirect()->route('motorista.van.create')
                ->with('aviso', 'Cadastre sua van primeiro.');
        }

        return Inertia::render('Motorista/Van/Editar', [
            'van' => [
                'id_van'                 => $van->id_van,
                'placa'                  => $van->placa,
                'nome_servico'           => $van->nome_servico,
                'marca'                  => $van->marca,
                'modelo'                 => $van->modelo,
                'ano_fabricacao'         => $van->ano_fabricacao,
                'cor'                    => $van->cor,
                'capacidade_passageiros' => $van->capacidade_passageiros,
                'foto_url'               => $van->foto_url,
                'foto_verso_url'         => $van->foto_verso_url,
                'foto_interior_url'      => $van->foto_interior_url,
                'foto_lateral_esq_url'   => $van->foto_lateral_esq_url,
                'foto_lateral_dir_url'   => $van->foto_lateral_dir_url,
                'status_aprovacao'       => $van->status_aprovacao,
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $van = auth()->user()->motorista?->van;

        if (!$van) {
            return redirect()->route('motorista.van.create');
        }

        $dados = $request->validate([
            'nome_servico'           => ['nullable', 'string', 'max:150'],
            'marca'                  => ['required', 'string', 'max:100'],
            'modelo'                 => ['required', 'string', 'max:100'],
            'ano_fabricacao'         => ['required', 'integer', 'min:1990', 'max:' . (date('Y') + 1)],
            'cor'                    => ['required', 'string', 'max:50'],
            'capacidade_passageiros' => ['required', 'integer', 'min:1', 'max:30'],
            'foto'                   => self::REGRAS_FOTO,
            'foto_verso'             => self::REGRAS_FOTO,
            'foto_interior'          => self::REGRAS_FOTO,
            'foto_lateral_esq'       => self::REGRAS_FOTO,
            'foto_lateral_dir'       => self::REGRAS_FOTO,
        ]);

        $van->update([
            'nome_servico'           => $dados['nome_servico'] ?? null,
            'marca'                  => $dados['marca'],
            'modelo'                 => $dados['modelo'],
            'ano_fabricacao'         => $dados['ano_fabricacao'],
            'cor'                    => $dados['cor'],
            'capacidade_passageiros' => $dados['capacidade_passageiros'],
        ]);

        $this->salvarFotoSeEnviada($request, $van, 'foto',           'foto_url');
        $this->salvarFotoSeEnviada($request, $van, 'foto_verso',     'foto_verso_url',     'verso');
        $this->salvarFotoSeEnviada($request, $van, 'foto_interior',  'foto_interior_url',  'interior');
        $this->salvarFotoSeEnviada($request, $van, 'foto_lateral_esq', 'foto_lateral_esq_url', 'lateral-esq');
        $this->salvarFotoSeEnviada($request, $van, 'foto_lateral_dir', 'foto_lateral_dir_url', 'lateral-dir');

        return redirect()->route('motorista.dashboard')
            ->with('sucesso', 'Van atualizada com sucesso!');
    }

    // ── DOCUMENTOS ───────────────────────────────────────────────────────────

    public function documentos(): Response|RedirectResponse
    {
        $van = auth()->user()->motorista?->van;

        if (!$van) {
            return redirect()->route('motorista.van.create')
                ->with('aviso', 'Cadastre sua van primeiro.');
        }

        return Inertia::render('Motorista/Van/Documentos', [
            'van' => [
                'id_van'                           => $van->id_van,
                'placa'                            => $van->placa,
                'modelo'                           => $van->modelo,
                'marca'                            => $van->marca,
                'nome_servico'                     => $van->nome_servico,
                'status_aprovacao'                 => $van->status_aprovacao,
                'documentacao_completa'            => $van->documentacao_completa,
                // Fotos
                'foto_url'                         => $van->foto_url,
                'foto_verso_url'                   => $van->foto_verso_url,
                'foto_interior_url'                => $van->foto_interior_url,
                'foto_lateral_esq_url'             => $van->foto_lateral_esq_url,
                'foto_lateral_dir_url'             => $van->foto_lateral_dir_url,
                // Documentos — datas formatadas como 'Y-m-d' para inputs type=date
                'crlv_url'                         => $van->crlv_url,
                'crlv_validade'                    => $van->crlv_validade?->format('Y-m-d'),
                'seguro_url'                       => $van->seguro_url,
                'seguro_validade'                  => $van->seguro_validade?->format('Y-m-d'),
                'autorizacao_municipal_url'        => $van->autorizacao_municipal_url,
                'autorizacao_municipal_validade'   => $van->autorizacao_municipal_validade?->format('Y-m-d'),
                'prefixo_municipal'                => $van->prefixo_municipal,
            ],
        ]);
    }

    public function uploadDocumento(Request $request, string $tipo): RedirectResponse
    {
        if (!array_key_exists($tipo, self::DOC_MAP)) {
            abort(404);
        }

        $van = auth()->user()->motorista?->van;
        if (!$van) return redirect()->route('motorista.van.create');

        $cfg = self::DOC_MAP[$tipo];

        $rules = [
            'arquivo'  => ['nullable', 'file', 'max:5120', 'mimes:jpg,jpeg,png,webp,pdf'],
            'validade' => ['nullable', 'date'],
        ];
        if ($tipo === 'autorizacao_municipal') {
            $rules['prefixo_municipal'] = ['nullable', 'string', 'max:20'];
        }

        $dados = $request->validate($rules, [
            'arquivo.max'   => 'O arquivo pode ter no máximo 5 MB.',
            'arquivo.mimes' => 'Formato aceito: JPG, PNG, WebP ou PDF.',
            'validade.date' => 'Data de validade inválida.',
        ]);

        $updates = [];

        if ($request->hasFile('arquivo')) {
            $file = $request->file('arquivo');

            // Remove arquivo anterior
            if ($van->{$cfg['url_col']}) {
                $oldRelative = ltrim(str_replace(Storage::disk(config('filesystems.upload'))->url(''), '', $van->{$cfg['url_col']}), '/');
                Storage::disk(config('filesystems.upload'))->delete($oldRelative);
            }

            $ext  = strtolower($file->getClientOriginalExtension());
            $path = $file->storeAs("vans/{$van->id_van}/documentos", "{$tipo}.{$ext}", config('filesystems.upload'));
            $updates[$cfg['url_col']] = Storage::disk(config('filesystems.upload'))->url($path);
        }

        if (array_key_exists('validade', $dados)) {
            $updates[$cfg['val_col']] = $dados['validade'] ?: null;
        }

        if ($tipo === 'autorizacao_municipal' && array_key_exists('prefixo_municipal', $dados)) {
            $updates['prefixo_municipal'] = $dados['prefixo_municipal'] ?: null;
        }

        if (!empty($updates)) {
            $van->update($updates);
            $this->recalcularDocumentacaoCompleta($van);
        }

        return back()->with('sucesso', 'Documento atualizado com sucesso.');
    }

    // ── UPLOADS DE FOTO (slots individuais) ──────────────────────────────────

    public function uploadFoto(Request $request): RedirectResponse
    {
        return $this->uploadFotoSlot($request, 'foto_url', 'foto');
    }

    public function uploadFotoVerso(Request $request): RedirectResponse
    {
        return $this->uploadFotoSlot($request, 'foto_verso_url', 'verso');
    }

    public function uploadFotoInterior(Request $request): RedirectResponse
    {
        return $this->uploadFotoSlot($request, 'foto_interior_url', 'interior');
    }

    public function uploadFotoLateralEsq(Request $request): RedirectResponse
    {
        return $this->uploadFotoSlot($request, 'foto_lateral_esq_url', 'lateral-esq');
    }

    public function uploadFotoLateralDir(Request $request): RedirectResponse
    {
        return $this->uploadFotoSlot($request, 'foto_lateral_dir_url', 'lateral-dir');
    }

    // ── HELPERS ──────────────────────────────────────────────────────────────

    private function uploadFotoSlot(Request $request, string $coluna, string $arquivo): RedirectResponse
    {
        $van = auth()->user()->motorista?->van;
        if (!$van) return redirect()->route('motorista.dashboard');

        $request->validate(
            ['foto' => ['required', 'image', 'max:2048', 'mimes:jpg,jpeg,png,webp']],
            [
                'foto.required' => 'Selecione uma imagem.',
                'foto.image'    => 'O arquivo deve ser uma imagem.',
                'foto.max'      => 'A imagem deve ter no máximo 2 MB.',
                'foto.mimes'    => 'Formato aceito: JPG, PNG ou WebP.',
            ]
        );

        $this->salvarFoto($van, $request->file('foto'), $coluna, $arquivo);
        $this->recalcularDocumentacaoCompleta($van);

        return back()->with('sucesso', 'Foto atualizada com sucesso!');
    }

    private function salvarFotoSeEnviada(Request $request, Van $van, string $campoInput, string $coluna, string $arquivo = 'foto'): void
    {
        if ($request->hasFile($campoInput)) {
            $this->salvarFoto($van, $request->file($campoInput), $coluna, $arquivo);
        }
    }

    private function salvarFoto(Van $van, UploadedFile $file, string $coluna, string $arquivo): void
    {
        foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
            Storage::disk(config('filesystems.upload'))->delete("vans/{$van->id_van}/{$arquivo}.{$ext}");
        }

        $ext  = $file->getClientOriginalExtension();
        $path = $file->storeAs("vans/{$van->id_van}", "{$arquivo}.{$ext}", config('filesystems.upload'));
        $van->update([$coluna => Storage::disk(config('filesystems.upload'))->url($path)]);
    }

    private function recalcularDocumentacaoCompleta(Van $van): void
    {
        $van->refresh();
        $essenciais = ['foto_url', 'crlv_url'];
        $completo   = collect($essenciais)->every(fn($c) => !empty($van->$c));
        $van->update(['documentacao_completa' => $completo]);
    }
}
