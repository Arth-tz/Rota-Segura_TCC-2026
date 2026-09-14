<script setup>
import { computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({
    nome:                    '',
    cpf:                     '',
    data_nascimento:         '',
    obs_medica:              '',
    foto_consentimento_lgpd: false,
})

function maskCpf(event) {
    let v = event.target.value.replace(/\D/g, '').slice(0, 11)
    if (v.length > 9)      v = v.replace(/^(\d{3})(\d{3})(\d{3})(\d+)$/, '$1.$2.$3-$4')
    else if (v.length > 6) v = v.replace(/^(\d{3})(\d{3})(\d+)$/, '$1.$2.$3')
    else if (v.length > 3) v = v.replace(/^(\d{3})(\d+)$/, '$1.$2')
    event.target.value = v
    form.cpf = v
}

function maskDateBr(e, field) {
    let v = e.target.value.replace(/\D/g, '').slice(0, 8)
    if (v.length > 4)      v = v.replace(/^(\d{2})(\d{2})(\d{0,4}).*$/, '$1/$2/$3')
    else if (v.length > 2) v = v.replace(/^(\d{2})(\d{0,2}).*$/, '$1/$2')
    e.target.value = v
    form[field] = v
}

function parseDateBrToIso(value) {
    const match = value.match(/^(\d{2})\/(\d{2})\/(\d{4})$/)
    if (!match) return value
    const [, dd, mm, yyyy] = match
    return `${yyyy}-${mm}-${dd}`
}

function submit() {
    form.transform(data => ({
        ...data,
        data_nascimento: parseDateBrToIso(data.data_nascimento),
    })).post(route('responsavel.passageiros.store.essencial'))
}

const inputClass = computed(() => (err) =>
    `w-full rounded-xl border px-4 py-3 text-sm bg-white text-slate-900 placeholder-slate-400 outline-none transition focus:ring-2 focus:border-transparent ${
        err ? 'border-red-300 bg-red-50 focus:ring-red-200' : 'border-slate-200 focus:border-blue-400 focus:ring-blue-100'
    }`
)
</script>

<template>
    <Head title="Cadastrar passageiro" />

    <div class="min-h-screen bg-slate-50 flex">

        <!-- ── ASIDE ─────────────────────────────────────────────────────── -->
        <aside class="hidden lg:flex lg:w-5/12 flex-col justify-between p-12 sticky top-0 h-screen overflow-y-auto z-10
                       bg-[radial-gradient(circle_at_top_left,_#3b82f6,_#1d4ed8_60%)]
                       shadow-[10px_0_50px_-15px_rgba(0,0,0,0.3)]">

            <Link :href="route('home')" class="flex items-center gap-2 hover:opacity-80 transition">
                <img src="/rota-segura/public/images/Logo_rota-segura_branco.png" alt="Rota Segura" class="h-12 w-12">
                <span class="text-white font-bold text-lg tracking-tight">Rota Segura</span>
            </Link>

            <div>
                <!-- Stepper -->
                <div class="flex items-center gap-3 mb-8">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shrink-0">
                            <span class="text-blue-700 text-sm font-bold">1</span>
                        </div>
                        <span class="text-white text-sm font-semibold">Dados</span>
                    </div>
                    <div class="h-px w-8 bg-blue-400"></div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-white/30 flex items-center justify-center shrink-0">
                            <span class="text-blue-200 text-sm font-bold">2</span>
                        </div>
                        <span class="text-blue-200 text-sm">Endereços</span>
                    </div>
                </div>

                <h2 class="text-4xl font-extrabold text-white leading-tight mb-4">
                    Cadastre o passageiro
                </h2>
                <p class="text-blue-100 text-sm leading-relaxed">
                    Comece pelos dados principais. No próximo passo você informa os endereços e pode adicionar uma foto.
                </p>

                <div class="mt-8 rounded-2xl border border-white/15 bg-white/10 p-4">
                    <p class="text-sm font-semibold text-white">Próximo passo</p>
                    <p class="mt-1 text-sm text-blue-100/85">Endereços de embarque, destino e foto do passageiro.</p>
                </div>
            </div>

            <p class="text-blue-200 text-xs">© 2026 Rota Segura</p>
        </aside>

        <!-- ── FORMULÁRIO ─────────────────────────────────────────────────── -->
        <main class="flex-1 flex items-center justify-center px-6 py-10">
            <div class="w-full max-w-lg">

                <!-- Logo mobile -->
                <div class="lg:hidden flex items-center gap-2 mb-8">
                    <img src="/rota-segura/public/images/Logo_rota_segura-azul.png" alt="Rota Segura" class="h-10 w-10">
                    <span class="text-slate-900 font-bold">Rota Segura</span>
                </div>

                <h1 class="text-3xl font-bold text-slate-900 mb-1">Cadastrar passageiro</h1>
                <p class="text-slate-500 text-sm mb-8">Preencha os dados de quem usará o transporte.</p>

                <form @submit.prevent="submit" class="space-y-5">

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Nome completo</label>
                        <input v-model="form.nome" type="text" placeholder="João da Silva" required autofocus
                            :class="inputClass(form.errors.nome)" />
                        <p v-if="form.errors.nome" class="text-red-500 text-xs mt-1">{{ form.errors.nome }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">CPF</label>
                            <input :value="form.cpf" @input="maskCpf" type="text"
                                placeholder="000.000.000-00" maxlength="14" required
                                :class="inputClass(form.errors.cpf)" />
                            <p v-if="form.errors.cpf" class="text-red-500 text-xs mt-1">{{ form.errors.cpf }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Data de nascimento</label>
                            <input :value="form.data_nascimento" type="text"
                                inputmode="numeric" maxlength="10" placeholder="DD/MM/AAAA"
                                required @input="maskDateBr($event, 'data_nascimento')"
                                :class="inputClass(form.errors.data_nascimento)" />
                            <p v-if="form.errors.data_nascimento" class="text-red-500 text-xs mt-1">{{ form.errors.data_nascimento }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            Observações médicas
                            <span class="text-slate-400 font-normal">(opcional)</span>
                        </label>
                        <textarea v-model="form.obs_medica" rows="3"
                            placeholder="Alergias, medicamentos, necessidades especiais…"
                            :class="inputClass(null) + ' resize-none'"></textarea>
                    </div>

                    <!-- Consentimento LGPD — foto do passageiro (Art. 14) -->
                    <div class="rounded-xl border border-blue-100 bg-blue-50 px-4 py-3.5">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" v-model="form.foto_consentimento_lgpd" required
                                class="mt-0.5 h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 shrink-0" />
                            <span class="text-sm text-slate-700 leading-relaxed">
                                Autorizo o uso de foto do passageiro para identificação no embarque e desembarque,
                                conforme o <strong class="text-slate-900">Art. 14 da LGPD</strong>.
                                A foto fica restrita ao motorista e à equipe da Rota Segura.
                            </span>
                        </label>
                        <p v-if="form.errors.foto_consentimento_lgpd" class="text-red-500 text-xs mt-1.5 pl-7">
                            {{ form.errors.foto_consentimento_lgpd }}
                        </p>
                    </div>

                    <div v-if="form.errors.geral"
                        class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                        {{ form.errors.geral }}
                    </div>

                    <div class="flex items-center justify-between gap-3 pt-1">
                        <Link :href="route('responsavel.dashboard')"
                            class="text-sm text-slate-400 hover:text-slate-600 transition">
                            Fazer depois
                        </Link>
                        <button type="submit" :disabled="form.processing"
                            class="bg-blue-600 hover:bg-blue-700 disabled:opacity-60 disabled:cursor-not-allowed
                                   text-white font-semibold px-6 py-3 rounded-xl transition shadow-sm shadow-blue-200 text-sm">
                            {{ form.processing ? 'Salvando…' : 'Continuar →' }}
                        </button>
                    </div>

                </form>

                <p class="text-center text-xs text-slate-400 mt-6">
                    Você pode cadastrar passageiros a qualquer momento pelo painel.
                </p>

            </div>
        </main>
    </div>
</template>
