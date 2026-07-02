<script setup>
import { reactive, ref, computed } from 'vue'
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import {
    ArrowLeftIcon,
    ExclamationTriangleIcon,
    MagnifyingGlassIcon,
    FunnelIcon,
    ShieldCheckIcon,
    PhoneIcon,
    CurrencyDollarIcon,
    UsersIcon,
    XMarkIcon,
    SunIcon,
    MoonIcon,
    ClockIcon,
    ChatBubbleLeftRightIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
} from '@heroicons/vue/24/outline'
import FlashMessage from '@/Components/UI/FlashMessage.vue'

const props = defineProps({
    disponibilidades: { type: Object, required: true },
    passageiros:      { type: Array, default: () => [] },
    filtros:          { type: Object, default: () => ({}) },
})

// ── Filtros locais (refletem URL) ─────────────────────────────────────────
const f = reactive({
    turno:     props.filtros.turno     ?? '',
    bairro:    props.filtros.bairro    ?? '',
    escola:    props.filtros.escola    ?? '',
    motorista: props.filtros.motorista ?? '',
    dias:      [...(props.filtros.dias ?? [])],
})

const TURNOS    = { manha: 'Manhã', tarde: 'Tarde', integral: 'Integral' }
const DIAS_MAP  = { seg: 'Seg', ter: 'Ter', qua: 'Qua', qui: 'Qui', sex: 'Sex', sab: 'Sáb', dom: 'Dom' }
const DIAS_ORDEM = ['seg', 'ter', 'qua', 'qui', 'sex', 'sab', 'dom']

function toggleDia(dia) {
    const idx = f.dias.indexOf(dia)
    idx === -1 ? f.dias.push(dia) : f.dias.splice(idx, 1)
}

const filtrosAtivos = () =>
    f.turno || f.bairro || f.escola || f.motorista || f.dias.length > 0

function buscar() {
    const params = {}
    if (f.turno)        params.turno     = f.turno
    if (f.bairro)       params.bairro    = f.bairro
    if (f.escola)       params.escola    = f.escola
    if (f.motorista)    params.motorista = f.motorista
    if (f.dias.length)  params.dias      = f.dias
    router.get(route('responsavel.marketplace'), params, { preserveState: false })
}

function limpar() {
    f.turno = ''; f.bairro = ''; f.escola = ''; f.motorista = ''; f.dias = []
    router.get(route('responsavel.marketplace'), {})
}

// ── Paginação ─────────────────────────────────────────────────────────────
function irParaPagina(url) {
    if (!url) return
    router.get(url, {}, { preserveState: false })
}

// ── Modal de solicitação ──────────────────────────────────────────────────
const modalAberto       = ref(false)
const disponSelecionada = ref(null)

const form = useForm({
    id_disponibilidade: '',
    id_passageiro:      '',
    dias_contratados:   [],
    mensagem:           '',
})

function abrirModal(disp) {
    disponSelecionada.value     = disp
    form.id_disponibilidade     = disp.id_disponibilidade
    form.id_passageiro          = props.passageiros.length === 1
        ? props.passageiros[0].id_passageiro : ''
    form.dias_contratados       = [...disp.dias]   // pré-seleciona todos os dias da van
    form.mensagem               = ''
    modalAberto.value           = true
}

function toggleDiaContratado(dia) {
    const idx = form.dias_contratados.indexOf(dia)
    idx === -1 ? form.dias_contratados.push(dia) : form.dias_contratados.splice(idx, 1)
}

function fecharModal() { modalAberto.value = false }

const passageiroSelecionado = computed(() =>
    props.passageiros.find(p => p.id_passageiro == form.id_passageiro) ?? null
)

const semEnderecos = computed(() => {
    if (!passageiroSelecionado.value) return false
    return !passageiroSelecionado.value.tem_embarque || !passageiroSelecionado.value.tem_desembarque
})

function enviarSolicitacao() {
    form.post(route('responsavel.solicitacoes.store'), {
        onSuccess: () => fecharModal(),
    })
}

// ── Helpers ───────────────────────────────────────────────────────────────
function formatarPreco(v) {
    return Number(v).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
}

function turnoIcon(t) {
    return t === 'manha' ? SunIcon : t === 'tarde' ? MoonIcon : ClockIcon
}

function diasOrdenados(dias) {
    return DIAS_ORDEM.filter(d => dias.includes(d))
}

function whatsappUrl(telefone) {
    const num = (telefone ?? '').replace(/\D/g, '')
    const msg = encodeURIComponent('Olá! Vi sua van no Rota Segura e gostaria de mais informações.')
    return `https://wa.me/55${num}?text=${msg}`
}

// Paginação — links numéricos (exclui "Previous" e "Next")
function paginaLinks() {
    return (props.disponibilidades.links ?? []).filter(l =>
        !isNaN(Number(l.label))
    )
}
</script>

<template>
    <Head title="Buscar Vans — Marketplace" />
    <FlashMessage />

    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Nunito:wght@300;400;500;600&display=swap" rel="stylesheet" />

    <div class="min-h-screen bg-slate-50">

        <!-- Topbar -->
        <header class="bg-gradient-to-b from-blue-700 to-blue-800 shadow-md sticky top-0 z-10">
            <div class="max-w-6xl mx-auto px-4 py-4 flex items-center gap-3">
                <Link :href="route('responsavel.dashboard')"
                    class="w-9 h-9 rounded-xl bg-white/20 hover:bg-white/30 flex items-center justify-center transition shrink-0">
                    <ArrowLeftIcon class="w-5 h-5 text-white" />
                </Link>
                <div class="flex-1">
                    <p class="text-xs font-semibold text-blue-200 uppercase tracking-widest">Responsável</p>
                    <h1 class="text-lg font-bold text-white" style="font-family:'Sora',sans-serif;">Buscar Vans</h1>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold text-white">{{ disponibilidades.total }}</p>
                    <p class="text-xs text-blue-300">resultado{{ disponibilidades.total !== 1 ? 's' : '' }}</p>
                </div>
            </div>
        </header>

        <main class="max-w-6xl mx-auto px-4 py-6 space-y-5">

            <!-- ── Filtros ───────────────────────────────────────────────── -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
                <div class="flex items-center gap-2 mb-4">
                    <FunnelIcon class="w-4 h-4 text-slate-400" />
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Filtros</span>
                    <button v-if="filtrosAtivos()" @click="limpar"
                        class="ml-auto flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 font-semibold">
                        <XMarkIcon class="w-3.5 h-3.5" /> Limpar tudo
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                    <!-- Motorista -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Nome do motorista</label>
                        <div class="relative">
                            <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                            <input v-model="f.motorista" type="text" placeholder="Ex: João Silva…"
                                class="w-full rounded-xl border border-slate-200 pl-9 pr-4 py-2.5 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition"
                                @keydown.enter="buscar" />
                        </div>
                    </div>

                    <!-- Bairro -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Bairro</label>
                        <div class="relative">
                            <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                            <input v-model="f.bairro" type="text" placeholder="Ex: Igara, Olaria…"
                                class="w-full rounded-xl border border-slate-200 pl-9 pr-4 py-2.5 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition"
                                @keydown.enter="buscar" />
                        </div>
                    </div>

                    <!-- Escola -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Escola</label>
                        <div class="relative">
                            <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                            <input v-model="f.escola" type="text" placeholder="Ex: Nossa Senhora…"
                                class="w-full rounded-xl border border-slate-200 pl-9 pr-4 py-2.5 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition"
                                @keydown.enter="buscar" />
                        </div>
                    </div>

                    <!-- Turno -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Turno</label>
                        <div class="flex gap-1.5">
                            <button v-for="(label, val) in TURNOS" :key="val"
                                @click="f.turno = f.turno === val ? '' : val"
                                class="flex-1 py-2.5 rounded-xl border text-xs font-semibold transition"
                                :class="f.turno === val
                                    ? 'bg-blue-600 text-white border-blue-600'
                                    : 'bg-white text-slate-600 border-slate-200 hover:border-blue-300'">
                                {{ label }}
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Dias -->
                <div class="mt-4">
                    <label class="block text-xs font-semibold text-slate-500 mb-2 uppercase tracking-wide">Dias da semana necessários</label>
                    <div class="flex gap-1.5 flex-wrap">
                        <button v-for="dia in DIAS_ORDEM" :key="dia"
                            @click="toggleDia(dia)"
                            class="px-3 py-1.5 rounded-lg border text-xs font-semibold transition"
                            :class="f.dias.includes(dia)
                                ? 'bg-blue-600 text-white border-blue-600'
                                : 'bg-white text-slate-600 border-slate-200 hover:border-blue-300'">
                            {{ DIAS_MAP[dia] }}
                        </button>
                    </div>
                </div>

                <!-- Botão buscar -->
                <div class="mt-4 flex justify-end">
                    <button @click="buscar"
                        class="flex items-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold transition shadow-sm"
                        style="font-family:'Sora',sans-serif;">
                        <MagnifyingGlassIcon class="w-4 h-4" />
                        Buscar
                    </button>
                </div>
            </div>

            <!-- ── Info de resultados ─────────────────────────────────────── -->
            <div class="flex items-center justify-between text-sm text-slate-500">
                <span>
                    Exibindo <strong class="text-slate-700">{{ disponibilidades.from ?? 0 }}–{{ disponibilidades.to ?? 0 }}</strong>
                    de <strong class="text-slate-700">{{ disponibilidades.total }}</strong> resultado{{ disponibilidades.total !== 1 ? 's' : '' }}
                </span>
                <span v-if="disponibilidades.last_page > 1">
                    Página <strong class="text-slate-700">{{ disponibilidades.current_page }}</strong> de {{ disponibilidades.last_page }}
                </span>
            </div>

            <!-- ── Empty state ────────────────────────────────────────────── -->
            <div v-if="disponibilidades.data.length === 0"
                class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-blue-200 bg-blue-50/40 py-16 px-6 text-center">
                <div class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center mb-4">
                    <MagnifyingGlassIcon class="w-7 h-7 text-blue-400" />
                </div>
                <p class="font-bold text-slate-800 text-lg" style="font-family:'Sora',sans-serif;">Nenhuma van encontrada</p>
                <p class="mt-2 text-sm text-slate-500 max-w-sm">
                    {{ filtrosAtivos() ? 'Tente ajustar os filtros para ver mais resultados.' : 'Ainda não há vans aprovadas disponíveis.' }}
                </p>
                <button v-if="filtrosAtivos()" @click="limpar"
                    class="mt-4 px-4 py-2 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition">
                    Limpar filtros
                </button>
            </div>

            <!-- ── Cards ──────────────────────────────────────────────────── -->
            <div v-else class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                <div v-for="disp in disponibilidades.data" :key="disp.id_disponibilidade"
                    class="rounded-2xl border border-slate-200 bg-white shadow-sm hover:shadow-md hover:border-blue-200 transition-all flex flex-col overflow-hidden">

                    <!-- Motorista + badge -->
                    <div class="flex items-center gap-3 px-4 pt-4 pb-3 border-b border-slate-100">
                        <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center shrink-0">
                            <span class="text-white font-bold text-sm">{{ disp.motorista.nome?.charAt(0)?.toUpperCase() ?? '?' }}</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-bold text-slate-900 text-sm truncate" style="font-family:'Sora',sans-serif;">
                                {{ disp.motorista.nome ?? '—' }}
                            </p>
                            <p class="text-xs text-slate-400">{{ disp.van.marca }} {{ disp.van.modelo }} · {{ disp.van.ano }}</p>
                        </div>
                        <span class="flex items-center gap-1 text-xs px-2 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold shrink-0">
                            <ShieldCheckIcon class="w-3.5 h-3.5" />Verificado
                        </span>
                    </div>

                    <!-- Corpo -->
                    <div class="px-4 py-3 space-y-3 flex-1">

                        <!-- Nome + turno -->
                        <div class="flex items-center gap-2">
                            <component :is="turnoIcon(disp.turno)"
                                class="w-4 h-4 shrink-0"
                                :class="disp.turno === 'manha' ? 'text-amber-500' : disp.turno === 'tarde' ? 'text-indigo-500' : 'text-slate-400'" />
                            <p class="text-sm font-semibold text-slate-800 truncate">{{ disp.nome }}</p>
                            <span class="ml-auto text-xs px-2 py-0.5 rounded-full font-semibold shrink-0"
                                :class="disp.turno === 'manha'
                                    ? 'bg-amber-50 text-amber-700 border border-amber-200'
                                    : disp.turno === 'tarde'
                                    ? 'bg-indigo-50 text-indigo-700 border border-indigo-200'
                                    : 'bg-slate-100 text-slate-600 border border-slate-200'">
                                {{ TURNOS[disp.turno] ?? disp.turno }}
                            </span>
                        </div>

                        <!-- Dias -->
                        <div class="flex gap-1 flex-wrap">
                            <span v-for="dia in diasOrdenados(disp.dias)" :key="dia"
                                class="text-xs px-2 py-0.5 rounded-md font-medium"
                                :class="f.dias.includes(dia)
                                    ? 'bg-blue-100 text-blue-700'
                                    : 'bg-slate-100 text-slate-600'">
                                {{ DIAS_MAP[dia] }}
                            </span>
                        </div>

                        <!-- Bairros -->
                        <div v-if="disp.bairros_atendidos?.length">
                            <p class="text-xs text-slate-400 mb-1">Bairros atendidos</p>
                            <div class="flex flex-wrap gap-1">
                                <span v-for="b in disp.bairros_atendidos" :key="b"
                                    class="text-xs px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                                    {{ b }}
                                </span>
                            </div>
                        </div>

                        <!-- Escolas -->
                        <div v-if="disp.escolas_atendidas?.length">
                            <p class="text-xs text-slate-400 mb-1">Escolas</p>
                            <div class="flex flex-wrap gap-1">
                                <span v-for="e in disp.escolas_atendidas" :key="e"
                                    class="text-xs px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ e }}
                                </span>
                            </div>
                        </div>

                        <!-- Preço e vagas -->
                        <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2.5">
                            <div class="flex items-center gap-1.5">
                                <CurrencyDollarIcon class="w-4 h-4 text-emerald-600" />
                                <span class="text-sm font-bold text-emerald-700">
                                    {{ formatarPreco(disp.preco_mensal) }}<span class="text-xs font-normal text-slate-400">/mês</span>
                                </span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <UsersIcon class="w-4 h-4 text-slate-400" />
                                <span class="text-xs font-semibold"
                                    :class="disp.vagas_disponiveis > 0 ? 'text-slate-600' : 'text-red-500'">
                                    {{ disp.vagas_disponiveis > 0
                                        ? `${disp.vagas_disponiveis} vaga${disp.vagas_disponiveis !== 1 ? 's' : ''}`
                                        : 'Sem vagas' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Ações -->
                    <div class="px-4 pb-4 flex gap-2">
                        <a v-if="disp.motorista.telefone"
                            :href="whatsappUrl(disp.motorista.telefone)"
                            target="_blank" rel="noopener noreferrer"
                            class="flex items-center justify-center gap-1.5 flex-1 py-2.5 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-700 text-xs font-semibold hover:bg-emerald-100 transition">
                            <ChatBubbleLeftRightIcon class="w-4 h-4" />
                            WhatsApp
                        </a>
                        <button
                            :disabled="disp.vagas_disponiveis === 0 || passageiros.length === 0"
                            @click="abrirModal(disp)"
                            class="flex-1 py-2.5 rounded-xl text-xs font-bold transition"
                            :class="disp.vagas_disponiveis > 0 && passageiros.length > 0
                                ? 'bg-blue-600 text-white hover:bg-blue-700 shadow-sm'
                                : 'bg-slate-100 text-slate-400 cursor-not-allowed'">
                            {{ passageiros.length === 0
                                ? 'Sem passageiro'
                                : disp.vagas_disponiveis === 0
                                ? 'Sem vagas'
                                : 'Solicitar vaga' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- ── Paginação ───────────────────────────────────────────────── -->
            <div v-if="disponibilidades.last_page > 1"
                class="flex items-center justify-center gap-1 pt-2 pb-6">

                <button
                    :disabled="!disponibilidades.prev_page_url"
                    @click="irParaPagina(disponibilidades.prev_page_url)"
                    class="flex items-center gap-1 px-3 py-2 rounded-xl border text-sm font-medium transition"
                    :class="disponibilidades.prev_page_url
                        ? 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:border-blue-300'
                        : 'border-slate-100 bg-slate-50 text-slate-300 cursor-not-allowed'">
                    <ChevronLeftIcon class="w-4 h-4" /> Anterior
                </button>

                <button
                    v-for="link in paginaLinks()" :key="link.label"
                    @click="irParaPagina(link.url)"
                    class="w-9 h-9 rounded-xl border text-sm font-semibold transition"
                    :class="link.active
                        ? 'bg-blue-600 text-white border-blue-600 shadow-sm'
                        : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:border-blue-300'">
                    {{ link.label }}
                </button>

                <button
                    :disabled="!disponibilidades.next_page_url"
                    @click="irParaPagina(disponibilidades.next_page_url)"
                    class="flex items-center gap-1 px-3 py-2 rounded-xl border text-sm font-medium transition"
                    :class="disponibilidades.next_page_url
                        ? 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:border-blue-300'
                        : 'border-slate-100 bg-slate-50 text-slate-300 cursor-not-allowed'">
                    Próximo <ChevronRightIcon class="w-4 h-4" />
                </button>
            </div>

        </main>
    </div>

    <!-- ── Modal de solicitação ───────────────────────────────────────────── -->
    <Teleport to="body">
        <Transition name="fade">
            <div v-if="modalAberto"
                class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
                @click.self="fecharModal">
                <Transition name="pop">
                    <div v-if="modalAberto" class="bg-white rounded-2xl w-full max-w-md shadow-2xl border border-slate-100">

                        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                            <div>
                                <p class="text-xs text-slate-400 uppercase tracking-widest">Solicitar vaga</p>
                                <h3 class="font-bold text-slate-900 mt-0.5" style="font-family:'Sora',sans-serif;">
                                    {{ disponSelecionada?.nome }}
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    {{ disponSelecionada?.motorista?.nome }} · {{ formatarPreco(disponSelecionada?.preco_mensal ?? 0) }}/mês
                                </p>
                            </div>
                            <button @click="fecharModal"
                                class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center hover:bg-slate-200 transition">
                                <XMarkIcon class="w-4 h-4 text-slate-500" />
                            </button>
                        </div>

                        <div class="px-5 py-4 space-y-4">

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Para qual passageiro?</label>
                                <select v-model="form.id_passageiro"
                                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition bg-white"
                                    :class="form.errors.id_passageiro ? 'border-red-300 bg-red-50' : ''">
                                    <option value="" disabled>Selecione o passageiro</option>
                                    <option v-for="p in passageiros" :key="p.id_passageiro" :value="p.id_passageiro">
                                        {{ p.nome }}{{ (!p.tem_embarque || !p.tem_desembarque) ? ' — sem endereço' : '' }}
                                    </option>
                                </select>
                                <p v-if="form.errors.id_passageiro" class="mt-1 text-xs text-red-600">{{ form.errors.id_passageiro }}</p>

                                <!-- Aviso: passageiro sem endereço -->
                                <div v-if="semEnderecos"
                                    class="mt-2 flex items-start gap-2 rounded-xl bg-amber-50 border border-amber-200 px-3 py-2.5">
                                    <ExclamationTriangleIcon class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" />
                                    <div class="text-xs text-amber-800">
                                        <p class="font-semibold">Endereços não cadastrados</p>
                                        <p class="mt-0.5">
                                            Cadastre os endereços de
                                            <strong>{{ !passageiroSelecionado?.tem_embarque ? 'embarque' : '' }}{{ (!passageiroSelecionado?.tem_embarque && !passageiroSelecionado?.tem_desembarque) ? ' e ' : '' }}{{ !passageiroSelecionado?.tem_desembarque ? 'destino (escola)' : '' }}</strong>
                                            antes de solicitar. O motorista precisa dessas informações.
                                        </p>
                                        <a :href="route('responsavel.passageiros.enderecos', passageiroSelecionado?.id_passageiro)"
                                            class="mt-1.5 inline-block font-semibold text-amber-700 underline hover:text-amber-900">
                                            Cadastrar endereços
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Dias contratados -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">
                                    Dias da semana
                                    <span class="ml-1 text-slate-400 normal-case font-normal">(selecione os que precisar)</span>
                                </label>
                                <div class="flex gap-1.5 flex-wrap">
                                    <button
                                        v-for="dia in diasOrdenados(disponSelecionada?.dias ?? [])"
                                        :key="dia"
                                        type="button"
                                        @click="toggleDiaContratado(dia)"
                                        class="px-3 py-1.5 rounded-lg border text-xs font-semibold transition"
                                        :class="form.dias_contratados.includes(dia)
                                            ? 'bg-blue-600 text-white border-blue-600'
                                            : 'bg-white text-slate-600 border-slate-200 hover:border-blue-300'">
                                        {{ DIAS_MAP[dia] }}
                                    </button>
                                </div>
                                <p v-if="form.errors.dias_contratados" class="mt-1 text-xs text-red-600">{{ form.errors.dias_contratados }}</p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Mensagem (opcional)</label>
                                <textarea v-model="form.mensagem" rows="3"
                                    placeholder="Ex: Meu filho estuda no turno da manhã e mora no Igara…"
                                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition resize-none"
                                    :class="form.errors.mensagem ? 'border-red-300 bg-red-50' : ''" />
                                <p v-if="form.errors.mensagem" class="mt-1 text-xs text-red-600">{{ form.errors.mensagem }}</p>
                            </div>

                        </div>

                        <div class="px-5 pb-5 flex gap-3">
                            <button @click="fecharModal"
                                class="flex-1 py-3 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                                Cancelar
                            </button>
                            <button @click="enviarSolicitacao"
                                :disabled="!form.id_passageiro || form.dias_contratados.length === 0 || semEnderecos || form.processing"
                                class="flex-1 py-3 rounded-xl text-sm font-bold transition"
                                :class="form.id_passageiro && form.dias_contratados.length > 0 && !semEnderecos && !form.processing
                                    ? 'bg-blue-600 text-white hover:bg-blue-700 shadow-sm'
                                    : 'bg-slate-100 text-slate-400 cursor-not-allowed'"
                                style="font-family:'Sora',sans-serif;">
                                {{ form.processing ? 'Enviando…'
                                    : form.dias_contratados.length === 0 ? 'Selecione um dia'
                                    : semEnderecos ? 'Cadastre os endereços'
                                    : 'Confirmar solicitação' }}
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
.pop-enter-active { transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1); }
.pop-leave-active { transition: all 0.15s ease-in; }
.pop-enter-from { opacity: 0; transform: scale(0.94) translateY(20px); }
.pop-leave-to { opacity: 0; transform: scale(0.96); }
</style>
