<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import FlashMessage from '@/Components/UI/FlashMessage.vue'
import {
    UserIcon, TruckIcon, UsersIcon,
    CheckCircleIcon, XCircleIcon, ClockIcon,
    ShieldCheckIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
    stats:      { type: Object, required: true },
    motoristas: { type: Array,  default: () => [] },
    vans:       { type: Array,  default: () => [] },
})

const secao = ref('motoristas')

// ─── MODAIS ───────────────────────────────────────────────────────────────────
const modalRejeitar = ref(null) // { tipo: 'motorista'|'van', id, nome }
const motivoRejeicao = ref('')

function abrirRejeitar(tipo, id, nome) {
    modalRejeitar.value = { tipo, id, nome }
    motivoRejeicao.value = ''
}

function confirmarRejeicao() {
    const { tipo, id } = modalRejeitar.value
    const routeName = tipo === 'motorista'
        ? 'admin.motoristas.rejeitar'
        : 'admin.vans.rejeitar'

    router.post(route(routeName, id), { motivo: motivoRejeicao.value }, {
        onSuccess: () => { modalRejeitar.value = null },
    })
}

function aprovar(tipo, id) {
    const routeName = tipo === 'motorista'
        ? 'admin.motoristas.aprovar'
        : 'admin.vans.aprovar'
    router.post(route(routeName, id))
}

// ─── HELPERS ──────────────────────────────────────────────────────────────────
const statusConfig = {
    aprovado:  { label: 'Aprovado',  classes: 'bg-emerald-50 text-emerald-700 border-emerald-200' },
    pendente:  { label: 'Pendente',  classes: 'bg-amber-50 text-amber-700 border-amber-200' },
    rejeitado: { label: 'Rejeitado', classes: 'bg-red-50 text-red-600 border-red-200' },
}
</script>

<template>
    <Head title="Dashboard — Admin" />
    <FlashMessage />
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Nunito:wght@400;500;600&display=swap" rel="stylesheet" />

    <div class="min-h-screen bg-slate-100 flex flex-col">

        <!-- Topbar -->
        <header class="bg-gradient-to-b from-blue-800 to-blue-900 text-white px-6 py-4 shadow-lg">
            <div class="max-w-6xl mx-auto flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-white/10 flex items-center justify-center">
                        <ShieldCheckIcon class="w-5 h-5 text-blue-200" />
                    </div>
                    <div>
                        <p class="text-xs text-blue-300 uppercase tracking-widest font-medium">Administração</p>
                        <p class="text-base font-bold leading-none mt-0.5" style="font-family:'Sora',sans-serif;">Rota Segura</p>
                    </div>
                </div>
                <Link :href="route('logout')" method="post" as="button"
                    class="text-xs text-blue-300 hover:text-white transition">
                    Sair
                </Link>
            </div>
        </header>

        <div class="max-w-6xl mx-auto w-full px-4 py-6 space-y-6">

            <!-- Stats -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                <div class="bg-white rounded-2xl border border-slate-200 px-4 py-4 shadow-sm">
                    <p class="text-xs text-slate-400 uppercase tracking-wide">Motoristas</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">{{ stats.motoristas_total }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-amber-200 px-4 py-4 shadow-sm">
                    <p class="text-xs text-amber-500 uppercase tracking-wide">Pendentes</p>
                    <p class="text-2xl font-bold text-amber-600 mt-1">{{ stats.motoristas_pendentes }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-emerald-200 px-4 py-4 shadow-sm">
                    <p class="text-xs text-emerald-500 uppercase tracking-wide">Aprovados</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">{{ stats.motoristas_aprovados }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 px-4 py-4 shadow-sm">
                    <p class="text-xs text-slate-400 uppercase tracking-wide">Vans</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">{{ stats.vans_total }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-amber-200 px-4 py-4 shadow-sm">
                    <p class="text-xs text-amber-500 uppercase tracking-wide">Vans pend.</p>
                    <p class="text-2xl font-bold text-amber-600 mt-1">{{ stats.vans_pendentes }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 px-4 py-4 shadow-sm">
                    <p class="text-xs text-slate-400 uppercase tracking-wide">Responsáveis</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">{{ stats.responsaveis_total }}</p>
                </div>
            </div>

            <!-- Alerta pendentes -->
            <div v-if="stats.motoristas_pendentes > 0 || stats.vans_pendentes > 0"
                class="flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                <div class="w-2 h-2 rounded-full bg-amber-400 animate-pulse shrink-0"></div>
                <span>
                    <template v-if="stats.motoristas_pendentes > 0">
                        {{ stats.motoristas_pendentes }} motorista{{ stats.motoristas_pendentes > 1 ? 's' : '' }} aguardando aprovação.
                    </template>
                    <template v-if="stats.vans_pendentes > 0">
                        {{ stats.vans_pendentes }} van{{ stats.vans_pendentes > 1 ? 's' : '' }} aguardando aprovação.
                    </template>
                </span>
            </div>

            <!-- Tabs -->
            <div class="flex gap-1 bg-white border border-slate-200 rounded-xl p-1 w-fit shadow-sm">
                <button
                    v-for="tab in [{ key:'motoristas', label:'Motoristas' }, { key:'vans', label:'Vans' }]"
                    :key="tab.key"
                    @click="secao = tab.key"
                    class="px-5 py-2 rounded-lg text-sm font-semibold transition-all"
                    :class="secao === tab.key
                        ? 'bg-blue-600 text-white shadow-sm'
                        : 'text-slate-500 hover:text-slate-700'"
                >
                    {{ tab.label }}
                    <span v-if="tab.key === 'motoristas' && stats.motoristas_pendentes > 0"
                        class="ml-1.5 inline-flex items-center justify-center w-4 h-4 rounded-full bg-amber-400 text-white text-[10px] font-bold">
                        {{ stats.motoristas_pendentes }}
                    </span>
                    <span v-if="tab.key === 'vans' && stats.vans_pendentes > 0"
                        class="ml-1.5 inline-flex items-center justify-center w-4 h-4 rounded-full bg-amber-400 text-white text-[10px] font-bold">
                        {{ stats.vans_pendentes }}
                    </span>
                </button>
            </div>

            <!-- ── MOTORISTAS ──────────────────────────────────────────────── -->
            <div v-if="secao === 'motoristas'" class="space-y-3">
                <div v-if="!motoristas.length" class="bg-white rounded-2xl border border-dashed border-slate-200 py-14 text-center text-slate-400 text-sm">
                    Nenhum motorista cadastrado.
                </div>

                <div v-for="m in motoristas" :key="m.id_motorista"
                    class="bg-white rounded-2xl border shadow-sm overflow-hidden transition-all"
                    :class="m.status_aprovacao === 'pendente' ? 'border-amber-200' : 'border-slate-200'">

                    <div class="flex items-center justify-between gap-4 px-5 py-4">
                        <!-- Avatar + dados -->
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                                <UserIcon class="w-5 h-5 text-blue-500" />
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-slate-900 truncate" style="font-family:'Sora',sans-serif;">{{ m.nome }}</p>
                                <p class="text-xs text-slate-400 truncate">{{ m.email }}</p>
                            </div>
                        </div>

                        <!-- Status badge -->
                        <span class="text-xs px-2.5 py-1 rounded-full border font-semibold shrink-0"
                            :class="statusConfig[m.status_aprovacao]?.classes">
                            {{ statusConfig[m.status_aprovacao]?.label }}
                        </span>
                    </div>

                    <!-- Detalhes CNH -->
                    <div class="px-5 pb-4 grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs text-slate-500">
                        <div>
                            <p class="uppercase tracking-wide text-slate-400 mb-0.5">CNH</p>
                            <p class="font-medium text-slate-700">{{ m.cnh_numero ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="uppercase tracking-wide text-slate-400 mb-0.5">Categoria</p>
                            <p class="font-medium text-slate-700">{{ m.cnh_categoria ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="uppercase tracking-wide text-slate-400 mb-0.5">Validade CNH</p>
                            <p class="font-medium text-slate-700">{{ m.cnh_validade ?? '—' }}</p>
                        </div>
                        <div v-if="m.van">
                            <p class="uppercase tracking-wide text-slate-400 mb-0.5">Van</p>
                            <p class="font-medium text-slate-700">{{ m.van.placa }} — {{ m.van.modelo }}</p>
                        </div>
                    </div>

                    <!-- Motivo rejeição -->
                    <div v-if="m.status_aprovacao === 'rejeitado' && m.motivo_rejeicao"
                        class="mx-5 mb-4 px-4 py-2.5 rounded-xl bg-red-50 border border-red-100 text-xs text-red-600">
                        <span class="font-semibold">Motivo:</span> {{ m.motivo_rejeicao }}
                    </div>

                    <!-- Ações -->
                    <div v-if="m.status_aprovacao === 'pendente'"
                        class="flex gap-2 px-5 pb-4">
                        <button @click="aprovar('motorista', m.id_motorista)"
                            class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition">
                            <CheckCircleIcon class="w-4 h-4" /> Aprovar
                        </button>
                        <button @click="abrirRejeitar('motorista', m.id_motorista, m.nome)"
                            class="flex items-center gap-1.5 px-4 py-2 rounded-xl border border-red-200 text-red-600 hover:bg-red-50 text-xs font-semibold transition">
                            <XCircleIcon class="w-4 h-4" /> Rejeitar
                        </button>
                    </div>
                    <div v-else-if="m.status_aprovacao === 'aprovado'"
                        class="flex gap-2 px-5 pb-4">
                        <button @click="abrirRejeitar('motorista', m.id_motorista, m.nome)"
                            class="flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 text-xs font-semibold transition">
                            <XCircleIcon class="w-4 h-4" /> Revogar aprovação
                        </button>
                    </div>
                </div>
            </div>

            <!-- ── VANS ──────────────────────────────────────────────────── -->
            <div v-else class="space-y-3">
                <div v-if="!vans.length" class="bg-white rounded-2xl border border-dashed border-slate-200 py-14 text-center text-slate-400 text-sm">
                    Nenhuma van cadastrada.
                </div>

                <div v-for="v in vans" :key="v.id_van"
                    class="bg-white rounded-2xl border shadow-sm overflow-hidden"
                    :class="v.status_aprovacao === 'pendente' ? 'border-amber-200' : 'border-slate-200'">

                    <div class="flex items-center justify-between gap-4 px-5 py-4">
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                                <TruckIcon class="w-5 h-5 text-blue-500" />
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-slate-900" style="font-family:'Sora',sans-serif;">{{ v.placa }}</p>
                                <p class="text-xs text-slate-400">{{ v.marca }} {{ v.modelo }} · {{ v.ano_fabricacao }}</p>
                            </div>
                        </div>
                        <span class="text-xs px-2.5 py-1 rounded-full border font-semibold shrink-0"
                            :class="statusConfig[v.status_aprovacao]?.classes">
                            {{ statusConfig[v.status_aprovacao]?.label }}
                        </span>
                    </div>

                    <div class="px-5 pb-4 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs text-slate-500">
                        <div>
                            <p class="uppercase tracking-wide text-slate-400 mb-0.5">Motorista</p>
                            <p class="font-medium text-slate-700">{{ v.nome_motorista }}</p>
                        </div>
                        <div>
                            <p class="uppercase tracking-wide text-slate-400 mb-0.5">Cor</p>
                            <p class="font-medium text-slate-700">{{ v.cor }}</p>
                        </div>
                        <div>
                            <p class="uppercase tracking-wide text-slate-400 mb-0.5">Capacidade</p>
                            <p class="font-medium text-slate-700">{{ v.capacidade }} passageiros</p>
                        </div>
                        <div>
                            <p class="uppercase tracking-wide text-slate-400 mb-0.5">Documentação</p>
                            <p class="font-medium" :class="v.documentacao_completa ? 'text-emerald-600' : 'text-amber-600'">
                                {{ v.documentacao_completa ? 'Completa' : 'Incompleta' }}
                            </p>
                        </div>
                    </div>

                    <div v-if="v.status_aprovacao === 'rejeitado' && v.motivo_rejeicao"
                        class="mx-5 mb-4 px-4 py-2.5 rounded-xl bg-red-50 border border-red-100 text-xs text-red-600">
                        <span class="font-semibold">Motivo:</span> {{ v.motivo_rejeicao }}
                    </div>

                    <div v-if="v.status_aprovacao === 'pendente'" class="flex gap-2 px-5 pb-4">
                        <button @click="aprovar('van', v.id_van)"
                            class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition">
                            <CheckCircleIcon class="w-4 h-4" /> Aprovar
                        </button>
                        <button @click="abrirRejeitar('van', v.id_van, v.placa)"
                            class="flex items-center gap-1.5 px-4 py-2 rounded-xl border border-red-200 text-red-600 hover:bg-red-50 text-xs font-semibold transition">
                            <XCircleIcon class="w-4 h-4" /> Rejeitar
                        </button>
                    </div>
                    <div v-else-if="v.status_aprovacao === 'aprovado'" class="flex gap-2 px-5 pb-4">
                        <button @click="abrirRejeitar('van', v.id_van, v.placa)"
                            class="flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 text-xs font-semibold transition">
                            <XCircleIcon class="w-4 h-4" /> Revogar aprovação
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal rejeição -->
    <Teleport to="body">
        <Transition name="fade">
            <div v-if="modalRejeitar"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
                @click.self="modalRejeitar = null">
                <Transition name="pop">
                    <div v-if="modalRejeitar" class="bg-white rounded-2xl p-6 max-w-md w-full shadow-2xl border border-slate-100">
                        <h3 class="text-base font-bold text-slate-900 mb-1" style="font-family:'Sora',sans-serif;">
                            Rejeitar {{ modalRejeitar.tipo === 'motorista' ? 'motorista' : 'van' }}
                        </h3>
                        <p class="text-sm text-slate-500 mb-4">{{ modalRejeitar.nome }}</p>

                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">
                            Motivo <span class="text-slate-400 normal-case font-normal">(opcional)</span>
                        </label>
                        <textarea v-model="motivoRejeicao" rows="3" placeholder="Descreva o motivo da rejeição..."
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-transparent transition resize-none">
                        </textarea>

                        <div class="flex gap-2 mt-4">
                            <button @click="confirmarRejeicao"
                                class="flex-1 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold transition">
                                Confirmar rejeição
                            </button>
                            <button @click="modalRejeitar = null"
                                class="flex-1 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-semibold transition">
                                Cancelar
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.pop-enter-active { transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1); }
.pop-leave-active { transition: all 0.15s ease-in; }
.pop-enter-from { opacity: 0; transform: scale(0.93) translateY(12px); }
.pop-leave-to { opacity: 0; transform: scale(0.96); }
</style>
