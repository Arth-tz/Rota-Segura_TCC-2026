<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({
    nome:            '',
    cpf:             '',
    data_nascimento: '',
    obs_medica:      '',
})

function maskCpf(event) {
    let v = event.target.value.replace(/\D/g, '').slice(0, 11)
    if (v.length > 9)      v = v.replace(/^(\d{3})(\d{3})(\d{3})(\d+)$/, '$1.$2.$3-$4')
    else if (v.length > 6) v = v.replace(/^(\d{3})(\d{3})(\d+)$/, '$1.$2.$3')
    else if (v.length > 3) v = v.replace(/^(\d{3})(\d+)$/, '$1.$2')
    form.cpf = v
}

function submit() {
    form.post(route('responsavel.passageiros.store.essencial'))
}
</script>

<template>
    <Head title="Cadastrar passageiro" />

    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Nunito:wght@300;400;500;600&display=swap" rel="stylesheet" />

    <div class="min-h-screen bg-slate-50 flex">

        <!-- Lado esquerdo -->
        <aside class="hidden lg:flex lg:w-5/12 bg-blue-700 flex-col justify-between p-12 rounded-tr-3xl rounded-br-3xl sticky top-0 h-screen overflow-y-auto bg-[radial-gradient(circle_at_top_left,_#3b82f6,_#1d4ed8_60%)] z-10 shadow-[10px_0_50px_-15px_rgba(0,0,0,0.3)]">

            <Link :href="route('home')" class="flex items-center gap-2 hover:opacity-80 transition-all duration-300">
                <img src="/rota-segura/public/images/Logo_rota-segura_branco.png" alt="Rota Segura" class="h-12 w-12">
                <span class="text-white font-bold text-lg tracking-tight" style="font-family:'Sora',sans-serif;">Rota Segura</span>
            </Link>

            <div class="relative z-10">
                <!-- Indicador de progresso -->
                <div class="flex items-center gap-3 mb-8">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center flex-shrink-0">
                            <span class="text-blue-700 text-sm font-bold">1</span>
                        </div>
                        <span class="text-white text-sm font-semibold">Dados</span>
                    </div>
                    <div class="h-px w-8 bg-blue-400"></div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-white/30 flex items-center justify-center flex-shrink-0">
                            <span class="text-blue-200 text-sm font-bold">2</span>
                        </div>
                        <span class="text-blue-200 text-sm">Endereços</span>
                    </div>
                </div>

                <h2 class="text-4xl font-extrabold text-white leading-tight mb-4" style="font-family:'Sora',sans-serif;">
                    Cadastre o passageiro
                </h2>
                <p class="text-blue-100 text-sm leading-relaxed">
                    Comece pelos dados principais. Depois você informa os endereços e já pode buscar uma van.
                </p>

                <div class="mt-8 rounded-2xl border border-white/15 bg-white/10 p-4">
                    <p class="text-sm font-semibold text-white">Próximo passo</p>
                    <p class="mt-1 text-sm text-blue-100/85">Endereços de embarque e destino.</p>
                </div>
            </div>

            <p class="text-blue-200 text-xs">© 2026 Rota Segura.</p>
        </aside>

        <!-- Lado direito -->
        <main class="flex-1 flex items-center justify-center px-6 py-10">
            <div class="w-full max-w-lg">

                <div class="lg:hidden flex items-center gap-2 mb-8">
                    <img src="/rota-segura/public/images/Logo_rota_segura-azul.png" alt="Rota Segura" class="h-10 w-10">
                    <span class="text-slate-900 font-bold" style="font-family:'Sora',sans-serif;">Rota Segura</span>
                </div>

                <h1 class="text-3xl font-bold text-slate-900 mb-1" style="font-family:'Sora',sans-serif;">
                    Cadastrar passageiro
                </h1>
                <p class="text-slate-400 text-sm mb-8">
                    Preencha os dados de quem usará o transporte.
                </p>

                <form @submit.prevent="submit" class="space-y-4">

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Nome completo</label>
                        <input
                            v-model="form.nome"
                            type="text"
                            placeholder="João da Silva"
                            required
                            autofocus
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-900 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            :class="{ 'border-red-400 focus:ring-red-400': form.errors.nome }"
                        />
                        <p v-if="form.errors.nome" class="text-red-500 text-xs mt-1">{{ form.errors.nome }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">CPF</label>
                            <input
                                v-model="form.cpf"
                                @input="maskCpf"
                                type="text"
                                placeholder="000.000.000-00"
                                maxlength="14"
                                required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-900 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                :class="{ 'border-red-400 focus:ring-red-400': form.errors.cpf }"
                            />
                            <p v-if="form.errors.cpf" class="text-red-500 text-xs mt-1">{{ form.errors.cpf }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Data de nascimento</label>
                            <input
                                v-model="form.data_nascimento"
                                type="date"
                                required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                :class="{ 'border-red-400 focus:ring-red-400': form.errors.data_nascimento }"
                            />
                            <p v-if="form.errors.data_nascimento" class="text-red-500 text-xs mt-1">{{ form.errors.data_nascimento }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">
                            Observações médicas
                            <span class="text-slate-400 normal-case font-normal">(opcional)</span>
                        </label>
                        <textarea
                            v-model="form.obs_medica"
                            rows="3"
                            placeholder="Alergias, medicamentos, necessidades especiais..."
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-900 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"
                        ></textarea>
                    </div>

                    <div v-if="form.errors.geral" class="px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
                        {{ form.errors.geral }}
                    </div>

                    <div class="flex items-center justify-between gap-3 pt-2">
                        <Link
                            :href="route('responsavel.dashboard')"
                            class="text-sm text-slate-400 hover:text-slate-600 transition"
                        >
                            Fazer depois
                        </Link>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="bg-blue-600 hover:bg-blue-700 disabled:opacity-60 disabled:cursor-not-allowed text-white font-semibold px-6 py-3 rounded-xl transition shadow-lg shadow-blue-200 text-sm"
                            style="font-family:'Sora',sans-serif;"
                        >
                            {{ form.processing ? 'Salvando...' : 'Continuar' }}
                        </button>
                    </div>
                </form>

                <p class="text-center text-xs text-slate-400 mt-6" style="font-family:'Nunito',sans-serif;">
                    Você pode cadastrar passageiros a qualquer momento pelo seu dashboard.
                </p>

            </div>
        </main>
    </div>
</template>
