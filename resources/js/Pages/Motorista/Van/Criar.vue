<script setup>
import { computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ArrowLeftIcon, CameraIcon } from '@heroicons/vue/24/outline'
import FlashMessage from '@/Components/UI/FlashMessage.vue'
import FotoSlotInput from '@/Components/UI/FotoSlotInput.vue'

const form = useForm({
    placa:                   '',
    nome_servico:            '',
    marca:                   '',
    modelo:                  '',
    ano_fabricacao:          '',
    cor:                     '',
    capacidade_passageiros:  '',
    foto:                    null,
    foto_verso:              null,
    foto_interior:           null,
})

const temAlgumaFoto = computed(() => form.foto || form.foto_verso || form.foto_interior)

// ─── SUBMIT ──────────────────────────────────────────────────────────────────
function submit() {
    form.post(route('motorista.van.store'), { forceFormData: true })
}

// ─── HELPERS ─────────────────────────────────────────────────────────────────
const marcasComuns = ['Mercedes-Benz', 'Volkswagen', 'Fiat', 'Iveco', 'Renault', 'Toyota', 'Outro']
const coresComuns  = ['Branco', 'Prata', 'Preto', 'Cinza', 'Azul', 'Vermelho', 'Amarelo', 'Outro']

const inputClass = computed(() => (err) =>
    `w-full rounded-xl border px-4 py-3 text-sm bg-white text-slate-900 placeholder-slate-400 outline-none transition focus:ring-2 focus:border-transparent ${
        err ? 'border-red-300 bg-red-50 focus:ring-red-200' : 'border-slate-200 focus:border-amber-600 focus:ring-amber-100'
    }`
)
const selectClass = computed(() => (err) =>
    `w-full rounded-xl border px-4 py-3 text-sm bg-white text-slate-900 outline-none transition focus:ring-2 focus:border-transparent ${
        err ? 'border-red-300 bg-red-50 focus:ring-red-200' : 'border-slate-200 focus:border-amber-600 focus:ring-amber-100'
    }`
)
</script>

<template>
    <Head title="Cadastrar Van — Motorista" />
    <FlashMessage />

    <div class="min-h-screen bg-slate-50">

        <!-- ── TOPBAR ─────────────────────────────────────────────────────── -->
        <header class="bg-gradient-to-b from-amber-700 to-amber-800 shadow-sm">
            <div class="max-w-2xl mx-auto px-4 py-4 flex items-center gap-3">
                <Link :href="route('motorista.dashboard')"
                    class="w-9 h-9 rounded-xl bg-white/20 hover:bg-white/30 flex items-center justify-center transition shrink-0">
                    <ArrowLeftIcon class="w-5 h-5 text-white" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-white">Cadastrar van</h1>
                    <p class="text-xs text-amber-200">Preencha os dados do veículo</p>
                </div>
            </div>
        </header>

        <main class="max-w-2xl mx-auto px-4 py-6 space-y-4">

            <!-- ── ERRO GERAL ─────────────────────────────────────────────── -->
            <div v-if="form.errors.geral"
                class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ form.errors.geral }}
            </div>

            <!-- ── FOTOS DA VAN ────────────────────────────────────────────── -->
            <div class="rounded-2xl border bg-white shadow-sm overflow-hidden"
                :class="temAlgumaFoto ? 'border-amber-300' : 'border-slate-200'">

                <!-- Banner de importância -->
                <div class="px-5 py-4 border-b"
                    :class="temAlgumaFoto ? 'border-amber-200 bg-amber-50' : 'border-slate-100 bg-slate-50'">
                    <div class="flex items-center gap-2.5">
                        <CameraIcon class="w-5 h-5 text-amber-500 shrink-0" />
                        <div>
                            <p class="text-base font-semibold text-slate-800">Fotos da van</p>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Frente, verso e interior — quanto mais fotos, mais confiança pros responsáveis.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Grid de 3 slots -->
                <div class="px-5 py-5">
                    <div class="grid grid-cols-3 gap-3 max-w-md">
                        <FotoSlotInput v-model="form.foto" label="Frente" :error="form.errors.foto" />
                        <FotoSlotInput v-model="form.foto_verso" label="Verso" :error="form.errors.foto_verso" />
                        <FotoSlotInput v-model="form.foto_interior" label="Interior" :error="form.errors.foto_interior" />
                    </div>
                    <p class="text-xs text-slate-400 mt-3">JPG, PNG ou WebP · máx. 2 MB cada · todas opcionais, mas recomendadas</p>
                </div>
            </div>

            <!-- ── IDENTIFICAÇÃO ───────────────────────────────────────────── -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h2 class="text-base font-semibold text-slate-800">Identificação</h2>
                </div>
                <div class="px-5 py-4 space-y-4">

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            Placa <span class="text-red-400">*</span>
                        </label>
                        <input v-model="form.placa" type="text" maxlength="7"
                            placeholder="ABC1234 ou ABC1D23"
                            @input="form.placa = form.placa.toUpperCase()"
                            :class="inputClass(form.errors.placa) + ' font-mono uppercase tracking-widest'" />
                        <p v-if="form.errors.placa" class="mt-1 text-xs text-red-600">{{ form.errors.placa }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            Nome do serviço
                            <span class="text-slate-400 font-normal">(opcional)</span>
                        </label>
                        <input v-model="form.nome_servico" type="text" maxlength="150"
                            placeholder="Ex: Van do Tio Marquinhos"
                            :class="inputClass(form.errors.nome_servico)" />
                        <p class="mt-1 text-xs text-slate-400">Como os responsáveis verão sua van na busca de motoristas.</p>
                        <p v-if="form.errors.nome_servico" class="mt-1 text-xs text-red-600">{{ form.errors.nome_servico }}</p>
                    </div>

                </div>
            </div>

            <!-- ── DADOS DO VEÍCULO ────────────────────────────────────────── -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h2 class="text-base font-semibold text-slate-800">Dados do veículo</h2>
                </div>
                <div class="px-5 py-4 space-y-4">

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                Marca <span class="text-red-400">*</span>
                            </label>
                            <select v-model="form.marca" :class="selectClass(form.errors.marca)">
                                <option value="">Selecione…</option>
                                <option v-for="m in marcasComuns" :key="m" :value="m">{{ m }}</option>
                            </select>
                            <p v-if="form.errors.marca" class="mt-1 text-xs text-red-600">{{ form.errors.marca }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                Modelo <span class="text-red-400">*</span>
                            </label>
                            <input v-model="form.modelo" type="text" placeholder="Ex: Sprinter 415"
                                :class="inputClass(form.errors.modelo)" />
                            <p v-if="form.errors.modelo" class="mt-1 text-xs text-red-600">{{ form.errors.modelo }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                Ano de fabricação <span class="text-red-400">*</span>
                            </label>
                            <input v-model="form.ano_fabricacao" type="number" placeholder="2020"
                                min="1990" :max="new Date().getFullYear() + 1"
                                :class="inputClass(form.errors.ano_fabricacao)" />
                            <p v-if="form.errors.ano_fabricacao" class="mt-1 text-xs text-red-600">{{ form.errors.ano_fabricacao }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                Cor <span class="text-red-400">*</span>
                            </label>
                            <select v-model="form.cor" :class="selectClass(form.errors.cor)">
                                <option value="">Selecione…</option>
                                <option v-for="c in coresComuns" :key="c" :value="c">{{ c }}</option>
                            </select>
                            <p v-if="form.errors.cor" class="mt-1 text-xs text-red-600">{{ form.errors.cor }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            Capacidade de passageiros <span class="text-red-400">*</span>
                        </label>
                        <input v-model="form.capacidade_passageiros" type="number"
                            placeholder="Ex: 15" min="1" max="30"
                            :class="inputClass(form.errors.capacidade_passageiros)" />
                        <p v-if="form.errors.capacidade_passageiros" class="mt-1 text-xs text-red-600">{{ form.errors.capacidade_passageiros }}</p>
                    </div>

                </div>
            </div>

            <!-- ── NOTA DE APROVAÇÃO ───────────────────────────────────────── -->
            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-800 leading-relaxed">
                Após o cadastro, sua van passará por aprovação antes de aparecer na busca de motoristas. Documentos como CRLV, seguro e IPVA poderão ser enviados posteriormente.
            </div>

            <!-- ── AÇÕES ───────────────────────────────────────────────────── -->
            <div class="flex gap-3 pb-6">
                <Link :href="route('motorista.dashboard')"
                    class="flex-1 flex items-center justify-center rounded-xl border border-slate-200 bg-white py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                    Cancelar
                </Link>
                <button @click="submit" :disabled="form.processing"
                    class="flex-1 flex items-center justify-center rounded-xl bg-amber-700 hover:bg-amber-800 disabled:opacity-60 disabled:cursor-not-allowed py-3 text-sm font-bold text-white transition shadow-sm shadow-amber-900/20">
                    {{ form.processing ? 'Salvando…' : 'Cadastrar van' }}
                </button>
            </div>

        </main>
    </div>
</template>
