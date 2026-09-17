<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import FlashMessage from '@/Components/UI/FlashMessage.vue'
import {
    UserIcon, TruckIcon, UsersIcon,
    CheckCircleIcon, XCircleIcon, ClockIcon,
    ShieldCheckIcon, DocumentTextIcon, ArrowTopRightOnSquareIcon, PhotoIcon,
    BeakerIcon, ChevronDownIcon, ChevronUpIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
    stats:        { type: Object, required: true },
    motoristas:   { type: Array,  default: () => [] },
    vans:         { type: Array,  default: () => [] },
    responsaveis: { type: Array,  default: () => [] },
})

const secao = ref('motoristas')

// ─── RESPONSÁVEIS: expandir passageiros ───────────────────────────────────────
const expandidos = ref(new Set())
function toggleExpandir(id) {
    if (expandidos.value.has(id)) expandidos.value.delete(id)
    else expandidos.value.add(id)
}

// ─── MODAIS ───────────────────────────────────────────────────────────────────
const modalRejeitar    = ref(null)
const motivoRejeicao   = ref('')
const modalDocumentos  = ref(null)

function abrirDocumentos(van) { modalDocumentos.value = van }

function aprovarEFechar(tipo, id) {
    aprovar(tipo, id)
    modalDocumentos.value = null
}

function rejeitarEFechar(tipo, id, nome) {
    modalDocumentos.value = null
    abrirRejeitar(tipo, id, nome)
}

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

const TIPO_RESPONSAVEL = { pai: 'Pai', mae: 'Mãe', avo: 'Avô/Avó', responsavel_legal: 'Resp. Legal' }

const tabs = computed(() => [
    { key: 'motoristas',   label: 'Motoristas',   badge: props.stats.motoristas_pendentes },
    { key: 'vans',         label: 'Vans',         badge: props.stats.vans_pendentes },
    { key: 'responsaveis', label: 'Responsáveis', badge: 0 },
])
</script>

<template>
    <Head title="Painel — Admin" />
    <FlashMessage />

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
                        <p class="text-base font-bold leading-none mt-0.5">Rota Segura</p>
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
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3">
                <div class="bg-white rounded-2xl border border-slate-200 px-4 py-4 shadow-sm col-span-1">
                    <p class="text-xs text-slate-400 uppercase tracking-wide">Motoristas</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">{{ stats.motoristas_total }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-amber-200 px-4 py-4 shadow-sm col-span-1">
                    <p class="text-xs text-amber-500 uppercase tracking-wide">Pendentes</p>
                    <p class="text-2xl font-bold text-amber-600 mt-1">{{ stats.motoristas_pendentes }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-emerald-200 px-4 py-4 shadow-sm col-span-1">
                    <p class="text-xs text-emerald-500 uppercase tracking-wide">Aprovados</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">{{ stats.motoristas_aprovados }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 px-4 py-4 shadow-sm col-span-1">
                    <p class="text-xs text-slate-400 uppercase tracking-wide">Vans</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">{{ stats.vans_total }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-amber-200 px-4 py-4 shadow-sm col-span-1">
                    <p class="text-xs text-amber-500 uppercase tracking-wide">Vans pend.</p>
                    <p class="text-2xl font-bold text-amber-600 mt-1">{{ stats.vans_pendentes }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 px-4 py-4 shadow-sm col-span-1">
                    <p class="text-xs text-slate-400 uppercase tracking-wide">Responsáveis</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">{{ stats.responsaveis_total }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 px-4 py-4 shadow-sm col-span-1">
                    <p class="text-xs text-slate-400 uppercase tracking-wide">Passageiros</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">{{ stats.passageiros_total }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-blue-200 px-4 py-4 shadow-sm col-span-1">
                    <p class="text-xs text-blue-500 uppercase tracking-wide">Vínculos ativos</p>
                    <p class="text-2xl font-bold text-blue-700 mt-1">{{ stats.vinculos_ativos }}</p>
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
                    v-for="tab in tabs"
                    :key="tab.key"
                    @click="secao = tab.key"
                    class="px-5 py-2 rounded-lg text-sm font-semibold transition-all"
                    :class="secao === tab.key
                        ? 'bg-blue-600 text-white shadow-sm'
                        : 'text-slate-500 hover:text-slate-700'"
                >
                    {{ tab.label }}
                    <span v-if="tab.badge > 0"
                        class="ml-1.5 inline-flex items-center justify-center w-4 h-4 rounded-full bg-amber-400 text-white text-[10px] font-bold">
                        {{ tab.badge }}
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
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                                <UserIcon class="w-5 h-5 text-blue-500" />
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="font-semibold text-slate-900 truncate">{{ m.nome }}</p>
                                    <span v-if="m.is_teste"
                                        class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-violet-100 text-violet-700 border border-violet-200 shrink-0">
                                        <BeakerIcon class="w-3 h-3" /> Usuário de teste
                                    </span>
                                </div>
                                <p class="text-xs text-slate-400 truncate">{{ m.email }}</p>
                            </div>
                        </div>

                        <span class="text-xs px-2.5 py-1 rounded-full border font-semibold shrink-0"
                            :class="statusConfig[m.status_aprovacao]?.classes">
                            {{ statusConfig[m.status_aprovacao]?.label }}
                        </span>
                    </div>

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
                        <div v-if="m.cnh_foto_url">
                            <p class="uppercase tracking-wide text-slate-400 mb-0.5">Foto CNH</p>
                            <a :href="m.cnh_foto_url" target="_blank" rel="noopener noreferrer"
                                class="flex items-center gap-1 font-medium text-blue-600 hover:underline">
                                <ArrowTopRightOnSquareIcon class="w-3 h-3" />
                                Ver arquivo
                            </a>
                        </div>
                        <div v-if="m.certidao_antecedentes_url">
                            <p class="uppercase tracking-wide text-slate-400 mb-0.5">Certidão</p>
                            <a :href="m.certidao_antecedentes_url" target="_blank" rel="noopener noreferrer"
                                class="flex items-center gap-1 font-medium text-blue-600 hover:underline">
                                <ArrowTopRightOnSquareIcon class="w-3 h-3" />
                                Ver arquivo
                            </a>
                        </div>
                        <div>
                            <p class="uppercase tracking-wide text-slate-400 mb-0.5">SEST SENAT</p>
                            <a v-if="m.curso_transporte_url" :href="m.curso_transporte_url" target="_blank" rel="noopener noreferrer"
                                class="flex items-center gap-1 font-medium text-blue-600 hover:underline">
                                <ArrowTopRightOnSquareIcon class="w-3 h-3" />
                                Ver certificado
                            </a>
                            <span v-else class="text-slate-400 text-xs">Não enviado</span>
                        </div>
                        <div>
                            <p class="uppercase tracking-wide text-slate-400 mb-0.5">RENACH</p>
                            <a v-if="m.renach_url" :href="m.renach_url" target="_blank" rel="noopener noreferrer"
                                class="flex items-center gap-1 font-medium text-blue-600 hover:underline">
                                <ArrowTopRightOnSquareIcon class="w-3 h-3" />
                                Ver extrato
                            </a>
                            <span v-else class="text-slate-400 text-xs">Não enviado</span>
                        </div>
                        <div v-if="m.van">
                            <p class="uppercase tracking-wide text-slate-400 mb-0.5">Van</p>
                            <p class="font-medium text-slate-700">{{ m.van.placa }} — {{ m.van.modelo }}</p>
                        </div>
                    </div>

                    <div v-if="m.status_aprovacao === 'rejeitado' && m.motivo_rejeicao"
                        class="mx-5 mb-4 px-4 py-2.5 rounded-xl bg-red-50 border border-red-100 text-xs text-red-600">
                        <span class="font-semibold">Motivo:</span> {{ m.motivo_rejeicao }}
                    </div>

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
            <div v-else-if="secao === 'vans'" class="space-y-3">
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
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="font-semibold text-slate-900">{{ v.placa }}</p>
                                    <span v-if="v.is_teste"
                                        class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-violet-100 text-violet-700 border border-violet-200 shrink-0">
                                        <BeakerIcon class="w-3 h-3" /> Teste
                                    </span>
                                </div>
                                <p class="text-xs text-slate-400">{{ v.marca }} {{ v.modelo }} · {{ v.ano_fabricacao }} · {{ v.nome_motorista }}</p>
                            </div>
                        </div>
                        <span class="text-xs px-2.5 py-1 rounded-full border font-semibold shrink-0"
                            :class="statusConfig[v.status_aprovacao]?.classes">
                            {{ statusConfig[v.status_aprovacao]?.label }}
                        </span>
                    </div>

                    <div class="px-5 pb-4 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs text-slate-500">
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

                    <div class="flex gap-2 px-5 pb-4 flex-wrap">
                        <button @click="abrirDocumentos(v)"
                            class="flex items-center gap-1.5 px-4 py-2 rounded-xl border border-blue-200 text-blue-600 hover:bg-blue-50 text-xs font-semibold transition">
                            <DocumentTextIcon class="w-4 h-4" /> Ver documentos
                        </button>
                        <template v-if="v.status_aprovacao === 'pendente'">
                            <button @click="aprovar('van', v.id_van)"
                                class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition">
                                <CheckCircleIcon class="w-4 h-4" /> Aprovar
                            </button>
                            <button @click="abrirRejeitar('van', v.id_van, v.placa)"
                                class="flex items-center gap-1.5 px-4 py-2 rounded-xl border border-red-200 text-red-600 hover:bg-red-50 text-xs font-semibold transition">
                                <XCircleIcon class="w-4 h-4" /> Rejeitar
                            </button>
                        </template>
                        <template v-else-if="v.status_aprovacao === 'aprovado'">
                            <button @click="abrirRejeitar('van', v.id_van, v.placa)"
                                class="flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 text-xs font-semibold transition">
                                <XCircleIcon class="w-4 h-4" /> Revogar aprovação
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- ── RESPONSÁVEIS ────────────────────────────────────────── -->
            <div v-else class="space-y-3">
                <div v-if="!responsaveis.length" class="bg-white rounded-2xl border border-dashed border-slate-200 py-14 text-center text-slate-400 text-sm">
                    Nenhum responsável cadastrado.
                </div>

                <div v-for="r in responsaveis" :key="r.id_responsavel"
                    class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                    <!-- Linha principal -->
                    <div class="flex items-center justify-between gap-4 px-5 py-4">
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center shrink-0">
                                <UsersIcon class="w-5 h-5 text-indigo-500" />
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="font-semibold text-slate-900 truncate">{{ r.nome }}</p>
                                    <span v-if="r.is_teste"
                                        class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-violet-100 text-violet-700 border border-violet-200 shrink-0">
                                        <BeakerIcon class="w-3 h-3" /> Usuário de teste
                                    </span>
                                </div>
                                <p class="text-xs text-slate-400 truncate">{{ r.email }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            <span class="text-xs px-2.5 py-1 rounded-full border border-slate-200 bg-slate-50 text-slate-600 font-medium hidden sm:inline-flex">
                                {{ TIPO_RESPONSAVEL[r.tipo_responsavel] ?? r.tipo_responsavel }}
                            </span>
                            <span class="text-xs text-slate-400">Desde {{ r.created_at }}</span>
                            <button v-if="r.passageiros.length"
                                @click="toggleExpandir(r.id_responsavel)"
                                class="flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-700 transition px-2 py-1 rounded-lg hover:bg-blue-50">
                                {{ r.passageiros.length }}
                                {{ r.passageiros.length === 1 ? 'passageiro' : 'passageiros' }}
                                <ChevronDownIcon v-if="!expandidos.has(r.id_responsavel)" class="w-3.5 h-3.5" />
                                <ChevronUpIcon v-else class="w-3.5 h-3.5" />
                            </button>
                            <span v-else class="text-xs text-slate-400 italic">Sem passageiros</span>
                        </div>
                    </div>

                    <!-- Lista de passageiros (expandível) -->
                    <div v-if="expandidos.has(r.id_responsavel) && r.passageiros.length"
                        class="px-5 pb-4 border-t border-slate-100 pt-3 space-y-2">
                        <p class="text-[10px] text-slate-400 uppercase tracking-wide font-semibold mb-2">Passageiros</p>
                        <div v-for="p in r.passageiros" :key="p.id_passageiro"
                            class="flex items-center justify-between px-3 py-2 rounded-xl bg-slate-50 border border-slate-100 text-sm">
                            <span class="font-medium text-slate-700">{{ p.nome }}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full"
                                :class="p.ativo ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-slate-100 text-slate-400'">
                                {{ p.ativo ? 'Ativo' : 'Inativo' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal documentos da van -->
    <Teleport to="body">
        <Transition name="fade">
            <div v-if="modalDocumentos"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
                @click.self="modalDocumentos = null">
                <Transition name="pop">
                    <div v-if="modalDocumentos" class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl border border-slate-100 max-h-[90vh] overflow-y-auto">

                        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base font-bold text-slate-900">{{ modalDocumentos.placa }}</h3>
                                    <span v-if="modalDocumentos.is_teste"
                                        class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-violet-100 text-violet-700 border border-violet-200">
                                        <BeakerIcon class="w-3 h-3" /> Teste
                                    </span>
                                </div>
                                <p class="text-xs text-slate-400 mt-0.5">
                                    {{ modalDocumentos.marca }} {{ modalDocumentos.modelo }} · {{ modalDocumentos.ano_fabricacao }} · {{ modalDocumentos.nome_motorista }}
                                </p>
                            </div>
                            <button @click="modalDocumentos = null" class="text-slate-400 hover:text-slate-600 transition">
                                <XCircleIcon class="w-5 h-5" />
                            </button>
                        </div>

                        <div class="px-6 py-5 space-y-6">
                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-3">Fotos da van</p>
                                <div class="grid grid-cols-5 gap-2">
                                    <a v-for="foto in [
                                        { url: modalDocumentos.foto_url,              label: 'Frontal'   },
                                        { url: modalDocumentos.foto_verso_url,        label: 'Verso'     },
                                        { url: modalDocumentos.foto_interior_url,     label: 'Interior'  },
                                        { url: modalDocumentos.foto_lateral_esq_url,  label: 'Lat. Esq.' },
                                        { url: modalDocumentos.foto_lateral_dir_url,  label: 'Lat. Dir.' },
                                    ]" :key="foto.label"
                                    :href="foto.url || undefined" :target="foto.url ? '_blank' : undefined"
                                    class="group flex flex-col items-center gap-1"
                                    :class="foto.url ? 'cursor-pointer' : 'cursor-default opacity-40'">
                                        <div class="w-full aspect-square rounded-xl overflow-hidden bg-slate-100 border border-slate-200 flex items-center justify-center">
                                            <img v-if="foto.url" :src="foto.url" :alt="foto.label"
                                                class="w-full h-full object-cover group-hover:scale-105 transition duration-200" />
                                            <PhotoIcon v-else class="w-6 h-6 text-slate-300" />
                                        </div>
                                        <p class="text-[10px] text-slate-400 text-center">{{ foto.label }}</p>
                                    </a>
                                </div>
                            </div>

                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-3">Documentos</p>
                                <div class="space-y-2">
                                    <div v-for="doc in [
                                        { url: modalDocumentos.crlv_url,                  label: 'CRLV',                    validade: modalDocumentos.crlv_validade                  },
                                        { url: modalDocumentos.seguro_url,                label: 'Seguro obrigatório',       validade: modalDocumentos.seguro_validade                },
                                        { url: modalDocumentos.autorizacao_municipal_url, label: 'Autorização municipal',   validade: modalDocumentos.autorizacao_municipal_validade  },
                                    ]" :key="doc.label"
                                    class="flex items-center justify-between px-4 py-3 rounded-xl border"
                                    :class="doc.url ? 'border-slate-200 bg-white' : 'border-dashed border-slate-200 bg-slate-50'">
                                        <div class="flex items-center gap-3">
                                            <DocumentTextIcon class="w-4 h-4 shrink-0"
                                                :class="doc.url ? 'text-blue-500' : 'text-slate-300'" />
                                            <div>
                                                <p class="text-sm font-medium text-slate-800">{{ doc.label }}</p>
                                                <p v-if="doc.validade" class="text-xs text-slate-400">Validade: {{ doc.validade }}</p>
                                            </div>
                                        </div>
                                        <a v-if="doc.url" :href="doc.url" target="_blank"
                                            class="flex items-center gap-1 text-xs text-blue-600 hover:text-blue-700 font-semibold transition">
                                            Abrir <ArrowTopRightOnSquareIcon class="w-3.5 h-3.5" />
                                        </a>
                                        <span v-else class="text-xs text-slate-400 italic">Não enviado</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 px-6 py-4 border-t border-slate-100 flex-wrap">
                            <template v-if="modalDocumentos.status_aprovacao === 'pendente'">
                                <button @click="aprovarEFechar('van', modalDocumentos.id_van)"
                                    class="flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold transition">
                                    <CheckCircleIcon class="w-4 h-4" /> Aprovar van
                                </button>
                                <button @click="rejeitarEFechar('van', modalDocumentos.id_van, modalDocumentos.placa)"
                                    class="flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-red-200 text-red-600 hover:bg-red-50 text-sm font-semibold transition">
                                    <XCircleIcon class="w-4 h-4" /> Rejeitar van
                                </button>
                            </template>
                            <template v-else-if="modalDocumentos.status_aprovacao === 'aprovado'">
                                <button @click="rejeitarEFechar('van', modalDocumentos.id_van, modalDocumentos.placa)"
                                    class="flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-semibold transition">
                                    <XCircleIcon class="w-4 h-4" /> Revogar aprovação
                                </button>
                            </template>
                            <button @click="modalDocumentos = null"
                                class="ml-auto px-4 py-2.5 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 text-sm font-semibold transition">
                                Fechar
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>

    <!-- Modal rejeição -->
    <Teleport to="body">
        <Transition name="fade">
            <div v-if="modalRejeitar"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
                @click.self="modalRejeitar = null">
                <Transition name="pop">
                    <div v-if="modalRejeitar" class="bg-white rounded-2xl p-6 max-w-md w-full shadow-2xl border border-slate-100">
                        <h3 class="text-base font-bold text-slate-900 mb-1">
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
.pop-enter-active { transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1); }
.pop-leave-active { transition: all 0.15s ease-in; }
.pop-enter-from { opacity: 0; transform: scale(0.93) translateY(12px); }
.pop-leave-to { opacity: 0; transform: scale(0.96); }
</style>
