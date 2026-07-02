<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { TruckIcon, ArrowLeftIcon } from '@heroicons/vue/24/outline'

const form = useForm({
    placa:                   '',
    marca:                   '',
    modelo:                  '',
    ano_fabricacao:          '',
    cor:                     '',
    capacidade_passageiros:  '',
})

function submit() { form.post(route('motorista.van.store')) }

const marcasComuns = ['Mercedes-Benz', 'Volkswagen', 'Fiat', 'Iveco', 'Renault', 'Toyota', 'Outro']
const coresComuns  = ['Branco', 'Prata', 'Preto', 'Cinza', 'Azul', 'Vermelho', 'Amarelo', 'Outro']
</script>

<template>
    <Head title="Cadastrar Van — Motorista" />

    <div class="min-h-screen bg-slate-50">

        <!-- Topbar -->
        <header class="bg-gradient-to-b from-amber-500 to-amber-600 shadow-sm">
            <div class="max-w-2xl mx-auto px-4 py-4 flex items-center gap-3">
                <Link :href="route('motorista.dashboard')"
                    class="w-9 h-9 rounded-xl bg-white/20 hover:bg-white/30 flex items-center justify-center transition shrink-0">
                    <ArrowLeftIcon class="w-5 h-5 text-white" />
                </Link>
                <div>
                    <p class="text-xs font-semibold text-amber-200 uppercase tracking-widest">Motorista</p>
                    <h1 class="text-lg font-bold text-white" style="font-family:'Sora',sans-serif;">Cadastrar van</h1>
                </div>
            </div>
        </header>

        <main class="max-w-2xl mx-auto px-4 py-6 space-y-4">

            <!-- Erro geral -->
            <div v-if="form.errors.geral"
                class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ form.errors.geral }}
            </div>

            <!-- Dados do veículo -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-amber-100 bg-amber-50/60">
                    <div class="w-8 h-8 rounded-xl bg-amber-500 flex items-center justify-center shrink-0">
                        <TruckIcon class="w-4 h-4 text-white" />
                    </div>
                    <h2 class="text-sm font-bold text-slate-800" style="font-family:'Sora',sans-serif;">Dados do veículo</h2>
                </div>
                <div class="px-5 py-4 space-y-4">

                    <!-- Placa -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">
                            Placa <span class="text-red-400">*</span>
                        </label>
                        <input v-model="form.placa" type="text" maxlength="7"
                            placeholder="ABC1234 ou ABC1D23"
                            @input="form.placa = form.placa.toUpperCase()"
                            class="w-full rounded-xl border px-4 py-2.5 text-sm font-mono uppercase tracking-widest outline-none transition"
                            :class="form.errors.placa ? 'border-red-300 bg-red-50' : 'border-slate-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-100'" />
                        <p v-if="form.errors.placa" class="mt-1 text-xs text-red-600">{{ form.errors.placa }}</p>
                    </div>

                    <!-- Marca + Modelo -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">
                                Marca <span class="text-red-400">*</span>
                            </label>
                            <select v-model="form.marca"
                                class="w-full rounded-xl border px-4 py-2.5 text-sm bg-white outline-none transition"
                                :class="form.errors.marca ? 'border-red-300 bg-red-50' : 'border-slate-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-100'">
                                <option value="">Selecione…</option>
                                <option v-for="m in marcasComuns" :key="m" :value="m">{{ m }}</option>
                            </select>
                            <p v-if="form.errors.marca" class="mt-1 text-xs text-red-600">{{ form.errors.marca }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">
                                Modelo <span class="text-red-400">*</span>
                            </label>
                            <input v-model="form.modelo" type="text" placeholder="Ex: Sprinter 415"
                                class="w-full rounded-xl border px-4 py-2.5 text-sm outline-none transition"
                                :class="form.errors.modelo ? 'border-red-300 bg-red-50' : 'border-slate-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-100'" />
                            <p v-if="form.errors.modelo" class="mt-1 text-xs text-red-600">{{ form.errors.modelo }}</p>
                        </div>
                    </div>

                    <!-- Ano + Cor -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">
                                Ano de fabricação <span class="text-red-400">*</span>
                            </label>
                            <input v-model="form.ano_fabricacao" type="number" placeholder="2020"
                                min="1990" :max="new Date().getFullYear() + 1"
                                class="w-full rounded-xl border px-4 py-2.5 text-sm outline-none transition"
                                :class="form.errors.ano_fabricacao ? 'border-red-300 bg-red-50' : 'border-slate-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-100'" />
                            <p v-if="form.errors.ano_fabricacao" class="mt-1 text-xs text-red-600">{{ form.errors.ano_fabricacao }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">
                                Cor <span class="text-red-400">*</span>
                            </label>
                            <select v-model="form.cor"
                                class="w-full rounded-xl border px-4 py-2.5 text-sm bg-white outline-none transition"
                                :class="form.errors.cor ? 'border-red-300 bg-red-50' : 'border-slate-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-100'">
                                <option value="">Selecione…</option>
                                <option v-for="c in coresComuns" :key="c" :value="c">{{ c }}</option>
                            </select>
                            <p v-if="form.errors.cor" class="mt-1 text-xs text-red-600">{{ form.errors.cor }}</p>
                        </div>
                    </div>

                    <!-- Capacidade -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">
                            Capacidade de passageiros <span class="text-red-400">*</span>
                        </label>
                        <input v-model="form.capacidade_passageiros" type="number"
                            placeholder="Ex: 15" min="1" max="30"
                            class="w-full rounded-xl border px-4 py-2.5 text-sm outline-none transition"
                            :class="form.errors.capacidade_passageiros ? 'border-red-300 bg-red-50' : 'border-slate-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-100'" />
                        <p v-if="form.errors.capacidade_passageiros" class="mt-1 text-xs text-red-600">{{ form.errors.capacidade_passageiros }}</p>
                    </div>
                </div>
            </div>

            <!-- Aviso -->
            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-700">
                Após o cadastro, a van precisa ser aprovada pelo administrador antes de aparecer no marketplace. Documentos (CRLV, seguro, IPVA) poderão ser enviados depois.
            </div>

            <!-- Botões -->
            <div class="flex gap-3 pb-6">
                <Link :href="route('motorista.dashboard')"
                    class="flex-1 flex items-center justify-center rounded-xl border border-slate-200 bg-white py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                    Cancelar
                </Link>
                <button @click="submit" :disabled="form.processing"
                    class="flex-1 flex items-center justify-center rounded-xl bg-amber-500 hover:bg-amber-600 disabled:opacity-60 py-3 text-sm font-bold text-white transition shadow-sm"
                    style="font-family:'Sora',sans-serif;">
                    {{ form.processing ? 'Salvando…' : 'Cadastrar van' }}
                </button>
            </div>

        </main>
    </div>
</template>
