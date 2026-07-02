<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ArrowLeftIcon, UserIcon, IdentificationIcon, EnvelopeIcon, PhoneIcon, LockClosedIcon, ShieldCheckIcon } from '@heroicons/vue/24/outline'
import FlashMessage from '@/Components/UI/FlashMessage.vue'

const props = defineProps({
    dados: { type: Object, required: true },
})

const form = useForm({
    nome:               props.dados.nome     ?? '',
    email:              props.dados.email    ?? '',
    telefone:           props.dados.telefone ?? '',
    senha:              '',
    senha_confirmation: '',
})

function submit() { form.put(route('motorista.perfil.update')) }

function formatCpf(cpf) {
    if (!cpf) return '—'
    return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4')
}

function formatDate(d) {
    if (!d) return '—'
    return new Date(d + 'T00:00:00').toLocaleDateString('pt-BR')
}

function formatTelefone(t) {
    if (!t) return '—'
    const n = t.replace(/\D/g, '')
    if (n.length === 11) return n.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3')
    if (n.length === 10) return n.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3')
    return t
}

const statusConfig = {
    pendente:  { label: 'Aguardando aprovação', classes: 'bg-amber-100 text-amber-700 border-amber-200' },
    aprovado:  { label: 'Aprovado',             classes: 'bg-emerald-100 text-emerald-700 border-emerald-200' },
    rejeitado: { label: 'Rejeitado',            classes: 'bg-red-100 text-red-700 border-red-200' },
}
</script>

<template>
    <Head title="Meu Perfil — Motorista" />
    <FlashMessage />

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
                    <h1 class="text-lg font-bold text-white" style="font-family:'Sora',sans-serif;">Meu perfil</h1>
                </div>
            </div>
        </header>

        <main class="max-w-2xl mx-auto px-4 py-6 space-y-4">

            <!-- ── SEÇÃO 1: Dados pessoais (editáveis) ── -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-amber-100 bg-amber-50/60">
                    <div class="w-8 h-8 rounded-xl bg-amber-500 flex items-center justify-center shrink-0">
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
                            :class="form.errors.nome ? 'border-red-300 bg-red-50' : 'border-slate-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-100'" />
                        <p v-if="form.errors.nome" class="mt-1 text-xs text-red-600">{{ form.errors.nome }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">
                            <span class="flex items-center gap-1"><PhoneIcon class="w-3.5 h-3.5" />Telefone / WhatsApp</span>
                        </label>
                        <input v-model="form.telefone" type="tel" placeholder="(51) 99999-9999"
                            class="w-full rounded-xl border px-4 py-2.5 text-sm outline-none transition"
                            :class="form.errors.telefone ? 'border-red-300 bg-red-50' : 'border-slate-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-100'" />
                        <p class="mt-1 text-xs text-slate-400">Usado para contato pelos responsáveis no marketplace</p>
                        <p v-if="form.errors.telefone" class="mt-1 text-xs text-red-600">{{ form.errors.telefone }}</p>
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
                </div>
            </div>

            <!-- ── SEÇÃO 3: CNH (somente leitura) ── -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100 bg-slate-50/60">
                    <div class="w-8 h-8 rounded-xl bg-slate-400 flex items-center justify-center shrink-0">
                        <ShieldCheckIcon class="w-4 h-4 text-white" />
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800" style="font-family:'Sora',sans-serif;">CNH e aprovação</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Gerenciado pelo administrador do sistema</p>
                    </div>
                </div>
                <div class="divide-y divide-slate-100">
                    <div class="flex items-center justify-between px-5 py-3 text-sm">
                        <span class="text-slate-500">Número da CNH</span>
                        <span class="font-mono font-medium text-slate-700">{{ dados.cnh_numero ?? '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between px-5 py-3 text-sm">
                        <span class="text-slate-500">Categoria</span>
                        <span class="font-medium text-slate-700">{{ dados.cnh_categoria ?? '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between px-5 py-3 text-sm">
                        <span class="text-slate-500">Validade</span>
                        <span class="font-medium text-slate-700">{{ formatDate(dados.cnh_validade) }}</span>
                    </div>
                    <div class="flex items-center justify-between px-5 py-3 text-sm">
                        <span class="text-slate-500">Status</span>
                        <span class="text-xs px-2.5 py-1 rounded-full border font-semibold"
                            :class="statusConfig[dados.status_aprovacao]?.classes ?? 'bg-slate-100 text-slate-600 border-slate-200'">
                            {{ statusConfig[dados.status_aprovacao]?.label ?? '—' }}
                        </span>
                    </div>
                    <div v-if="dados.motivo_rejeicao" class="px-5 py-3 text-sm">
                        <p class="text-slate-500 mb-1">Motivo da rejeição</p>
                        <p class="text-red-600 text-xs bg-red-50 rounded-xl px-3 py-2">{{ dados.motivo_rejeicao }}</p>
                    </div>
                </div>
            </div>

            <!-- ── SEÇÃO 4: Acesso (editável) ── -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-amber-100 bg-amber-50/60">
                    <div class="w-8 h-8 rounded-xl bg-amber-500 flex items-center justify-center shrink-0">
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
                            :class="form.errors.email ? 'border-red-300 bg-red-50' : 'border-slate-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-100'" />
                        <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Nova senha</label>
                            <input v-model="form.senha" type="password" autocomplete="new-password" placeholder="Deixe em branco para manter"
                                class="w-full rounded-xl border px-4 py-2.5 text-sm outline-none transition"
                                :class="form.errors.senha ? 'border-red-300 bg-red-50' : 'border-slate-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-100'" />
                            <p v-if="form.errors.senha" class="mt-1 text-xs text-red-600">{{ form.errors.senha }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Confirmar senha</label>
                            <input v-model="form.senha_confirmation" type="password" autocomplete="new-password"
                                class="w-full rounded-xl border px-4 py-2.5 text-sm outline-none transition border-slate-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-100" />
                        </div>
                    </div>

                </div>
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
                    {{ form.processing ? 'Salvando…' : 'Salvar alterações' }}
                </button>
            </div>

        </main>
    </div>
</template>
