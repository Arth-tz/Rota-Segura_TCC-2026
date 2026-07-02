<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ArrowLeftIcon, UserIcon, IdentificationIcon, EnvelopeIcon, PhoneIcon, LockClosedIcon, ShieldCheckIcon } from '@heroicons/vue/24/outline'
import FlashMessage from '@/Components/UI/FlashMessage.vue'

const props = defineProps({
    dados: { type: Object, required: true },
})

const TIPOS = {
    pai:                  'Pai',
    mae:                  'Mãe',
    tutor:                'Tutor(a)',
    representante_legal:  'Representante legal',
    autoresponsavel:      'Autorresponsável',
}

const form = useForm({
    nome:                 props.dados.nome                ?? '',
    email:                props.dados.email               ?? '',
    telefone:             props.dados.telefone            ?? '',
    telefone_emergencia:  props.dados.telefone_emergencia ?? '',
    senha:                '',
    senha_confirmation:   '',
})

function submit() { form.put(route('responsavel.perfil.update')) }

function formatCpf(cpf) {
    if (!cpf) return '—'
    return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4')
}

function formatDate(d) {
    if (!d) return '—'
    return new Date(d + 'T00:00:00').toLocaleDateString('pt-BR')
}
</script>

<template>
    <Head title="Meu Perfil — Responsável" />
    <FlashMessage />

    <div class="min-h-screen bg-slate-50">

        <!-- Topbar -->
        <header class="bg-gradient-to-b from-blue-700 to-blue-800 shadow-sm">
            <div class="max-w-2xl mx-auto px-4 py-4 flex items-center gap-3">
                <Link :href="route('responsavel.dashboard')"
                    class="w-9 h-9 rounded-xl bg-white/20 hover:bg-white/30 flex items-center justify-center transition shrink-0">
                    <ArrowLeftIcon class="w-5 h-5 text-white" />
                </Link>
                <div>
                    <p class="text-xs font-semibold text-blue-200 uppercase tracking-widest">Responsável</p>
                    <h1 class="text-lg font-bold text-white" style="font-family:'Sora',sans-serif;">Meu perfil</h1>
                </div>
            </div>
        </header>

        <main class="max-w-2xl mx-auto px-4 py-6 space-y-4">

            <!-- ── SEÇÃO 1: Dados pessoais (editáveis) ── -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-blue-100 bg-blue-50/60">
                    <div class="w-8 h-8 rounded-xl bg-blue-600 flex items-center justify-center shrink-0">
                        <UserIcon class="w-4 h-4 text-white" />
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800" style="font-family:'Sora',sans-serif;">Dados pessoais</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Informações que você pode alterar</p>
                    </div>
                </div>
                <div class="px-5 py-4 space-y-4">

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Nome completo</label>
                        <input v-model="form.nome" type="text"
                            class="w-full rounded-xl border px-4 py-2.5 text-sm outline-none transition"
                            :class="form.errors.nome ? 'border-red-300 bg-red-50' : 'border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100'" />
                        <p v-if="form.errors.nome" class="mt-1 text-xs text-red-600">{{ form.errors.nome }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">
                                <span class="flex items-center gap-1"><PhoneIcon class="w-3.5 h-3.5" />Telefone / WhatsApp</span>
                            </label>
                            <input v-model="form.telefone" type="tel" placeholder="(51) 99999-9999"
                                class="w-full rounded-xl border px-4 py-2.5 text-sm outline-none transition"
                                :class="form.errors.telefone ? 'border-red-300 bg-red-50' : 'border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100'" />
                            <p v-if="form.errors.telefone" class="mt-1 text-xs text-red-600">{{ form.errors.telefone }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">
                                <span class="flex items-center gap-1"><PhoneIcon class="w-3.5 h-3.5" />Telefone de emergência</span>
                            </label>
                            <input v-model="form.telefone_emergencia" type="tel" placeholder="(51) 99999-9999"
                                class="w-full rounded-xl border px-4 py-2.5 text-sm outline-none transition"
                                :class="form.errors.telefone_emergencia ? 'border-red-300 bg-red-50' : 'border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100'" />
                            <p v-if="form.errors.telefone_emergencia" class="mt-1 text-xs text-red-600">{{ form.errors.telefone_emergencia }}</p>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ── SEÇÃO 2: Dados cadastrais (somente leitura) ── -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100 bg-slate-50/60">
                    <div class="w-8 h-8 rounded-xl bg-slate-400 flex items-center justify-center shrink-0">
                        <IdentificationIcon class="w-4 h-4 text-white" />
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800" style="font-family:'Sora',sans-serif;">Dados de cadastro</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Não podem ser alterados — entre em contato com o administrador se houver erro</p>
                    </div>
                </div>
                <div class="divide-y divide-slate-100">
                    <div class="flex items-center justify-between px-5 py-3 text-sm">
                        <span class="text-slate-500">CPF</span>
                        <span class="font-mono font-medium text-slate-700">{{ formatCpf(dados.cpf) }}</span>
                    </div>
                    <div class="flex items-center justify-between px-5 py-3 text-sm">
                        <span class="text-slate-500">Data de nascimento</span>
                        <span class="font-medium text-slate-700">{{ formatDate(dados.data_nascimento) }}</span>
                    </div>
                    <div class="flex items-center justify-between px-5 py-3 text-sm">
                        <span class="text-slate-500">Tipo de responsável</span>
                        <span class="font-medium text-slate-700">{{ TIPOS[dados.tipo_responsavel] ?? dados.tipo_responsavel ?? '—' }}</span>
                    </div>
                </div>
            </div>

            <!-- ── SEÇÃO 3: Acesso (editável) ── -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-blue-100 bg-blue-50/60">
                    <div class="w-8 h-8 rounded-xl bg-blue-600 flex items-center justify-center shrink-0">
                        <LockClosedIcon class="w-4 h-4 text-white" />
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800" style="font-family:'Sora',sans-serif;">Acesso</h2>
                        <p class="text-xs text-slate-400 mt-0.5">E-mail de login e senha</p>
                    </div>
                </div>
                <div class="px-5 py-4 space-y-4">

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">
                            <span class="flex items-center gap-1"><EnvelopeIcon class="w-3.5 h-3.5" />E-mail</span>
                        </label>
                        <input v-model="form.email" type="email" autocomplete="off"
                            class="w-full rounded-xl border px-4 py-2.5 text-sm outline-none transition"
                            :class="form.errors.email ? 'border-red-300 bg-red-50' : 'border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100'" />
                        <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Nova senha</label>
                            <input v-model="form.senha" type="password" autocomplete="new-password" placeholder="Deixe em branco para manter"
                                class="w-full rounded-xl border px-4 py-2.5 text-sm outline-none transition"
                                :class="form.errors.senha ? 'border-red-300 bg-red-50' : 'border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100'" />
                            <p v-if="form.errors.senha" class="mt-1 text-xs text-red-600">{{ form.errors.senha }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Confirmar senha</label>
                            <input v-model="form.senha_confirmation" type="password" autocomplete="new-password"
                                class="w-full rounded-xl border px-4 py-2.5 text-sm outline-none transition border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100" />
                        </div>
                    </div>

                </div>
            </div>

            <!-- Botões -->
            <div class="flex gap-3 pb-6">
                <Link :href="route('responsavel.dashboard')"
                    class="flex-1 flex items-center justify-center rounded-xl border border-slate-200 bg-white py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                    Cancelar
                </Link>
                <button @click="submit" :disabled="form.processing"
                    class="flex-1 flex items-center justify-center rounded-xl bg-blue-600 hover:bg-blue-700 disabled:opacity-60 py-3 text-sm font-bold text-white transition shadow-sm"
                    style="font-family:'Sora',sans-serif;">
                    {{ form.processing ? 'Salvando…' : 'Salvar alterações' }}
                </button>
            </div>

        </main>
    </div>
</template>
