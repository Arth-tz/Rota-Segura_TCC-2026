<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({
    nome: '',
    cpf: '',
    data_nascimento: '',
    obs_medica: '',
})

function maskCpf(event) {
    let v = event.target.value.replace(/\D/g, '').slice(0, 11)
    if (v.length > 9) v = v.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})$/, '$1.$2.$3-$4')
    else if (v.length > 6) v = v.replace(/^(\d{3})(\d{3})(\d+)$/, '$1.$2.$3')
    else if (v.length > 3) v = v.replace(/^(\d{3})(\d+)$/, '$1.$2')
    form.cpf = v
}

function maskDateBr(event, field) {
    let v = event.target.value.replace(/\D/g, '').slice(0, 8)
    if (v.length > 4) v = v.replace(/^(\d{2})(\d{2})(\d{0,4}).*$/, '$1/$2/$3')
    else if (v.length > 2) v = v.replace(/^(\d{2})(\d{0,2}).*$/, '$1/$2')
    form[field] = v
}

function parseDateBrToIso(value) {
    const match = value.match(/^(\d{2})\/(\d{2})\/(\d{4})$/)
    if (!match) return value
    const [, dd, mm, yyyy] = match
    return `${yyyy}-${mm}-${dd}`
}

function submit() {
    form.data_nascimento = parseDateBrToIso(form.data_nascimento)
    form.post(route('responsavel.passageiros.store.essencial'))
}
</script>

<template>
    <Head title="Cadastro de Passageiro - Etapa 1" />

    <div class="min-h-screen bg-slate-50 flex">
        <aside class="hidden lg:flex lg:w-5/12 bg-blue-700 text-white flex-col justify-between p-10 rounded-tr-3xl rounded-br-3xl sticky top-0 h-screen">
            <Link :href="route('home')" class="inline-flex items-center">
                <img src="/rota-segura/public/images/Logo_rota-segura_branco.png" alt="Rota Segura" class="h-12 w-12">
                <span class="text-white font-bold text-lg tracking-tight" style="font-family:'Sora',sans-serif;">Rota Segura</span>
            </Link>
            <div>
                <p class="text-xs uppercase tracking-widest text-blue-200 mb-3">Cadastro de passageiro</p>
                <h2 class="text-3xl font-extrabold leading-tight mb-3">Etapa 1 de 2</h2>
                <p class="text-blue-100 text-sm">Preencha os dados essenciais antes de cadastrar os endereços.</p>
            </div>
            <p class="text-blue-200 text-xs">© 2026 Rota Segura</p>
        </aside>

        <main class="flex-1 flex items-center justify-center px-6 py-10">
            <div class="w-full max-w-2xl bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm">
                <h1 class="text-2xl font-bold text-slate-900 mb-1">Dados essenciais do passageiro</h1>
                <p class="text-sm text-slate-500 mb-6">Após esta etapa, você preencherá endereços e destino.</p>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nome completo</label>
                        <input v-model="form.nome" type="text" class="w-full rounded-lg border-slate-300" placeholder="João Silva" required>
                        <p v-if="form.errors.nome" class="text-red-600 text-xs mt-1">{{ form.errors.nome }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">CPF</label>
                            <input v-model="form.cpf" @input="maskCpf" type="text" maxlength="14" class="w-full rounded-lg border-slate-300" placeholder="000.000.000-00" required>
                            <p v-if="form.errors.cpf" class="text-red-600 text-xs mt-1">{{ form.errors.cpf }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Data de nascimento</label>
                            <input
                                v-model="form.data_nascimento"
                                @input="maskDateBr($event, 'data_nascimento')"
                                type="text"
                                inputmode="numeric"
                                maxlength="10"
                                placeholder="DD/MM/AAAA"
                                class="w-full rounded-lg border-slate-300"
                                required
                            >
                            <p v-if="form.errors.data_nascimento" class="text-red-600 text-xs mt-1">{{ form.errors.data_nascimento }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Observações médicas (opcional)</label>
                        <textarea v-model="form.obs_medica" rows="4" class="w-full rounded-lg border-slate-300" placeholder="Alergias, medicamentos, necessidades especiais..."></textarea>
                        <p v-if="form.errors.obs_medica" class="text-red-600 text-xs mt-1">{{ form.errors.obs_medica }}</p>
                    </div>

                    <p v-if="form.errors.geral" class="text-red-600 text-sm">{{ form.errors.geral }}</p>

                    <div class="flex items-center justify-between gap-3 pt-2">
                        <Link :href="route('responsavel.dashboard')" class="text-sm text-slate-600 hover:text-slate-900">Cancelar</Link>
                        <button type="submit" :disabled="form.processing" class="rounded-lg bg-blue-600 text-white px-5 py-2.5 font-semibold hover:bg-blue-700 disabled:opacity-60">
                            {{ form.processing ? 'Salvando...' : 'Continuar para endereços' }}
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</template>
