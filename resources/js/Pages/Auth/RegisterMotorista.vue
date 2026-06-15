<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({
    nome: '',
    email: '',
    password: '',
    password_confirmation: '',
    cpf: '',
    telefone: '',
    data_nascimento: '',
    cnh_numero: '',
    cnh_categoria: '',
    cnh_validade: '',
})

function maskCpf(event) {
    let v = event.target.value.replace(/\D/g, '').slice(0, 11)
    if (v.length > 9) v = v.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})$/, '$1.$2.$3-$4')
    else if (v.length > 6) v = v.replace(/^(\d{3})(\d{3})(\d+)$/, '$1.$2.$3')
    else if (v.length > 3) v = v.replace(/^(\d{3})(\d+)$/, '$1.$2')
    form.cpf = v
}

function maskTelefone(event) {
    let v = event.target.value.replace(/\D/g, '').slice(0, 11)
    if (v.length > 10) v = v.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3')
    else if (v.length > 6) v = v.replace(/^(\d{2})(\d{4})(\d+)$/, '($1) $2-$3')
    else if (v.length > 2) v = v.replace(/^(\d{2})(\d+)$/, '($1) $2')
    form.telefone = v
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
    form.cnh_validade = parseDateBrToIso(form.cnh_validade)
    form.post(route('register.motorista.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    })
}
</script>

<template>
    <Head title="Cadastro de Motorista" />

    <div class="min-h-screen bg-slate-50 flex">
        <aside class="hidden lg:flex lg:w-5/12 bg-blue-700 text-white flex-col justify-between p-10 rounded-tr-3xl rounded-br-3xl">
            <Link :href="route('home')" class="inline-flex items-center">
                <img src="/rota-segura/public/images/Logo_rota-segura_branco.png" alt="Rota Segura" class="h-10 w-auto">
            </Link>
            <div>
                <p class="text-xs uppercase tracking-widest text-blue-200 mb-3">Para motoristas</p>
                <h2 class="text-3xl font-extrabold leading-tight mb-3">Cadastre-se e expanda sua rota</h2>
                <p class="text-blue-100 text-sm">Seu perfil profissional em um fluxo rápido e padronizado.</p>
            </div>
            <p class="text-blue-200 text-xs">© 2026 Rota Segura</p>
        </aside>

        <main class="flex-1 flex items-center justify-center px-6 py-10">
            <div class="w-full max-w-2xl bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm">
                <h1 class="text-2xl font-bold text-slate-900 mb-1">Cadastro de motorista</h1>
                <p class="text-sm text-slate-500 mb-6">Preencha os dados pessoais e da CNH.</p>
                <div class="mb-4">
                    <Link :href="route('home')" class="text-sm text-slate-600 hover:text-slate-900">Voltar</Link>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nome completo</label>
                        <input v-model="form.nome" type="text" class="w-full rounded-lg border-slate-300" required>
                        <p v-if="form.errors.nome" class="text-red-600 text-xs mt-1">{{ form.errors.nome }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">E-mail</label>
                        <input v-model="form.email" type="email" class="w-full rounded-lg border-slate-300" required>
                        <p v-if="form.errors.email" class="text-red-600 text-xs mt-1">{{ form.errors.email }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">CPF</label>
                            <input v-model="form.cpf" @input="maskCpf" type="text" maxlength="14" class="w-full rounded-lg border-slate-300" required>
                            <p v-if="form.errors.cpf" class="text-red-600 text-xs mt-1">{{ form.errors.cpf }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Telefone</label>
                            <input v-model="form.telefone" @input="maskTelefone" type="text" class="w-full rounded-lg border-slate-300" required>
                            <p v-if="form.errors.telefone" class="text-red-600 text-xs mt-1">{{ form.errors.telefone }}</p>
                        </div>
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

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Número da CNH</label>
                            <input v-model="form.cnh_numero" type="text" maxlength="11" class="w-full rounded-lg border-slate-300" required>
                            <p v-if="form.errors.cnh_numero" class="text-red-600 text-xs mt-1">{{ form.errors.cnh_numero }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Categoria</label>
                            <select v-model="form.cnh_categoria" class="w-full rounded-lg border-slate-300" required>
                                <option value="" disabled>Selecione</option>
                                <option value="D">D</option>
                                <option value="E">E</option>
                            </select>
                            <p v-if="form.errors.cnh_categoria" class="text-red-600 text-xs mt-1">{{ form.errors.cnh_categoria }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Validade CNH</label>
                            <input
                                v-model="form.cnh_validade"
                                @input="maskDateBr($event, 'cnh_validade')"
                                type="text"
                                inputmode="numeric"
                                maxlength="10"
                                placeholder="DD/MM/AAAA"
                                class="w-full rounded-lg border-slate-300"
                                required
                            >
                            <p v-if="form.errors.cnh_validade" class="text-red-600 text-xs mt-1">{{ form.errors.cnh_validade }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Senha</label>
                            <input v-model="form.password" type="password" class="w-full rounded-lg border-slate-300" required>
                            <p v-if="form.errors.password" class="text-red-600 text-xs mt-1">{{ form.errors.password }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Confirmar senha</label>
                            <input v-model="form.password_confirmation" type="password" class="w-full rounded-lg border-slate-300" required>
                        </div>
                    </div>

                    <p v-if="form.errors.geral" class="text-red-600 text-sm">{{ form.errors.geral }}</p>

                    <button type="submit" :disabled="form.processing" class="w-full rounded-lg bg-blue-600 text-white py-2.5 font-semibold hover:bg-blue-700 disabled:opacity-60">
                        {{ form.processing ? 'Criando...' : 'Criar conta' }}
                    </button>
                </form>

                <p class="text-sm text-slate-600 mt-5 text-center">
                    Já tem conta?
                    <Link :href="route('login')" class="text-blue-600 font-semibold">Entrar</Link>
                </p>
            </div>
        </main>
    </div>
</template>
