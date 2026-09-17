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
    TruckIcon,
    InformationCircleIcon,
    DocumentTextIcon,
    IdentificationIcon,
    CheckCircleIcon,
    XCircleIcon,
    UserCircleIcon,
    PhotoIcon,
    MapPinIcon,
    AcademicCapIcon,
    ChevronDownIcon,
    ArrowTopRightOnSquareIcon,
    BeakerIcon,
} from '@heroicons/vue/24/outline'
import FlashMessage from '@/Components/UI/FlashMessage.vue'

const props = defineProps({
    disponibilidades: { type: Object, required: true },
    passageiros:      { type: Array, default: () => [] },
    filtros:          { type: Object, default: () => ({}) },
    ids_vinculados:   { type: Array, default: () => [] },
    ids_solicitados:  { type: Array, default: () => [] },
})

// ── Filtros ───────────────────────────────────────────────────────────────────
const f = reactive({
    turno:     props.filtros.turno     ?? '',
    cidade:    props.filtros.cidade    ?? '',
    bairro:    props.filtros.bairro    ?? '',
    escola:    props.filtros.escola    ?? '',
    motorista: props.filtros.motorista ?? '',
    prefixo:   props.filtros.prefixo   ?? '',
    dias:      [...(props.filtros.dias ?? [])],
})

const TURNOS     = { manha: 'Manhã', tarde: 'Tarde', integral: 'Integral' }
const DIAS_MAP   = { seg: 'Seg', ter: 'Ter', qua: 'Qua', qui: 'Qui', sex: 'Sex', sab: 'Sáb', dom: 'Dom' }
const DIAS_ORDEM = ['seg', 'ter', 'qua', 'qui', 'sex', 'sab', 'dom']

function toggleDia(dia) {
    const idx = f.dias.indexOf(dia)
    idx === -1 ? f.dias.push(dia) : f.dias.splice(idx, 1)
}

const filtrosAtivos = () =>
    f.turno || f.cidade || f.bairro || f.escola || f.motorista || f.prefixo || f.dias.length > 0

function buscar() {
    const params = {}
    if (f.turno)       params.turno     = f.turno
    if (f.cidade)      params.cidade    = f.cidade
    if (f.bairro)      params.bairro    = f.bairro
    if (f.escola)      params.escola    = f.escola
    if (f.motorista)   params.motorista = f.motorista
    if (f.prefixo)     params.prefixo   = f.prefixo
    if (f.dias.length) params.dias      = f.dias
    router.get(route('responsavel.marketplace'), params, { preserveState: false })
}

function limpar() {
    f.turno = ''; f.cidade = ''; f.bairro = ''; f.escola = ''; f.motorista = ''; f.prefixo = ''; f.dias = []
    router.get(route('responsavel.marketplace'), {})
}

// ── Paginação ─────────────────────────────────────────────────────────────────
function irParaPagina(url) {
    if (!url) return
    router.get(url, {}, { preserveState: false })
}

function paginaLinks() {
    return (props.disponibilidades.links ?? []).filter(l => !isNaN(Number(l.label)))
}

// ── Bottom sheet de DETALHES ──────────────────────────────────────────────────
const detalheAberto     = ref(false)
const detalheSelecionado = ref(null)
const abaAtiva          = ref('servico')
const fotoAtiva         = ref(0)

const ABAS = [
    { key: 'servico',      label: 'Serviço',      icon: InformationCircleIcon },
    { key: 'van',          label: 'Van',           icon: TruckIcon },
    { key: 'motorista',    label: 'Motorista',     icon: UserCircleIcon },
    { key: 'verificacao',  label: 'Verificação',   icon: ShieldCheckIcon },
]

const fotos = computed(() => {
    if (!detalheSelecionado.value) return []
    const v = detalheSelecionado.value.van
    return [
        { url: v.foto_url,              label: 'Frente' },
        { url: v.foto_verso_url,        label: 'Verso' },
        { url: v.foto_interior_url,     label: 'Interior' },
        { url: v.foto_lateral_esq_url,  label: 'Lat. Esq.' },
        { url: v.foto_lateral_dir_url,  label: 'Lat. Dir.' },
    ].filter(f => f.url)
})

function abrirDetalhe(disp) {
    detalheSelecionado.value = disp
    abaAtiva.value  = 'servico'
    fotoAtiva.value = 0
    detalheAberto.value = true
}

function fecharDetalhe() { detalheAberto.value = false }

// ── Modal de SOLICITAÇÃO ──────────────────────────────────────────────────────
const modalAberto       = ref(false)
const disponSelecionada = ref(null)

const form = useForm({
    id_disponibilidade: '',
    id_passageiro:      '',
    dias_contratados:   [],
    mensagem:           '',
})

function abrirModal(disp) {
    fecharDetalhe()
    disponSelecionada.value     = disp
    form.id_disponibilidade     = disp.id_disponibilidade
    form.id_passageiro          = props.passageiros.length === 1
        ? props.passageiros[0].id_passageiro : ''
    form.dias_contratados       = [...disp.dias]
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

// ── Helpers ───────────────────────────────────────────────────────────────────
function formatarPreco(v) {
    if (v === null || v === undefined) return 'À combinar'
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

function jaVinculado(id)  { return props.ids_vinculados.includes(id) }
function jaSolicitado(id) { return props.ids_solicitados.includes(id) }

function inicialNome(nome) {
    return nome?.charAt(0)?.toUpperCase() ?? '?'
}
</script>

<template>
    <Head title="Buscar Motoristas" />
    <FlashMessage />

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
                    <h1 class="text-lg font-bold text-white">Buscar Vans</h1>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold text-white">{{ disponibilidades.total }}</p>
                    <p class="text-xs text-blue-300">resultado{{ disponibilidades.total !== 1 ? 's' : '' }}</p>
                </div>
            </div>
        </header>

        <main class="max-w-6xl mx-auto px-4 py-6 space-y-5">

            <!-- Filtros -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
                <div class="flex items-center gap-2 mb-4">
                    <FunnelIcon class="w-4 h-4 text-slate-400" />
                    <span class="text-sm font-semibold text-slate-700">Filtros</span>
                    <button v-if="filtrosAtivos()" @click="limpar"
                        class="ml-auto flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 font-semibold">
                        <XMarkIcon class="w-3.5 h-3.5" /> Limpar tudo
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Nome do motorista</label>
                        <div class="relative">
                            <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                            <input v-model="f.motorista" type="text" placeholder="Ex: João Silva…"
                                class="w-full rounded-xl border border-slate-200 pl-9 pr-4 py-2.5 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition"
                                @keydown.enter="buscar" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Cidade</label>
                        <div class="relative">
                            <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                            <input v-model="f.cidade" type="text" placeholder="Ex: Canoas, Sapucaia do Sul…"
                                class="w-full rounded-xl border border-slate-200 pl-9 pr-4 py-2.5 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition"
                                @keydown.enter="buscar" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Bairro</label>
                        <div class="relative">
                            <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                            <input v-model="f.bairro" type="text" placeholder="Ex: Igara, Olaria…"
                                class="w-full rounded-xl border border-slate-200 pl-9 pr-4 py-2.5 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition"
                                @keydown.enter="buscar" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Escola</label>
                        <div class="relative">
                            <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                            <input v-model="f.escola" type="text" placeholder="Ex: Nossa Senhora…"
                                class="w-full rounded-xl border border-slate-200 pl-9 pr-4 py-2.5 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition"
                                @keydown.enter="buscar" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Prefixo municipal</label>
                        <div class="relative">
                            <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                            <input v-model="f.prefixo" type="text" placeholder="Ex: 47, RS-23…"
                                class="w-full rounded-xl border border-slate-200 pl-9 pr-4 py-2.5 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition"
                                @keydown.enter="buscar" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Turno</label>
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

                <div class="mt-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Dias da semana necessários</label>
                    <div class="flex gap-1.5 flex-wrap">
                        <button v-for="dia in DIAS_ORDEM" :key="dia" @click="toggleDia(dia)"
                            class="px-3 py-1.5 rounded-lg border text-xs font-semibold transition"
                            :class="f.dias.includes(dia)
                                ? 'bg-blue-600 text-white border-blue-600'
                                : 'bg-white text-slate-600 border-slate-200 hover:border-blue-300'">
                            {{ DIAS_MAP[dia] }}
                        </button>
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <button @click="buscar"
                        class="flex items-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold transition shadow-sm">
                        <MagnifyingGlassIcon class="w-4 h-4" />
                        Buscar
                    </button>
                </div>
            </div>

            <!-- Info resultados -->
            <div class="flex items-center justify-between text-sm text-slate-500">
                <span>
                    Exibindo <strong class="text-slate-700">{{ disponibilidades.from ?? 0 }}–{{ disponibilidades.to ?? 0 }}</strong>
                    de <strong class="text-slate-700">{{ disponibilidades.total }}</strong> resultado{{ disponibilidades.total !== 1 ? 's' : '' }}
                </span>
                <span v-if="disponibilidades.last_page > 1">
                    Página <strong class="text-slate-700">{{ disponibilidades.current_page }}</strong> de {{ disponibilidades.last_page }}
                </span>
            </div>

            <!-- Empty state -->
            <div v-if="disponibilidades.data.length === 0"
                class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-blue-200 bg-blue-50/40 py-16 px-6 text-center">
                <div class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center mb-4">
                    <MagnifyingGlassIcon class="w-7 h-7 text-blue-400" />
                </div>
                <p class="font-bold text-slate-800 text-lg">Nenhuma van encontrada</p>
                <p class="mt-2 text-sm text-slate-500 max-w-sm">
                    {{ filtrosAtivos() ? 'Tente ajustar os filtros para ver mais resultados.' : 'Ainda não há vans aprovadas disponíveis.' }}
                </p>
                <button v-if="filtrosAtivos()" @click="limpar"
                    class="mt-4 px-4 py-2 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition">
                    Limpar filtros
                </button>
            </div>

            <!-- Cards -->
            <div v-else class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                <div v-for="disp in disponibilidades.data" :key="disp.id_disponibilidade"
                    class="rounded-2xl border bg-white shadow-sm hover:shadow-md transition-all flex flex-col overflow-hidden cursor-pointer group"
                    :class="jaVinculado(disp.id_disponibilidade)
                        ? 'border-emerald-300 hover:border-emerald-400'
                        : jaSolicitado(disp.id_disponibilidade)
                        ? 'border-amber-300 hover:border-amber-400'
                        : 'border-slate-200 hover:border-blue-200'"
                    @click="abrirDetalhe(disp)">

                    <!-- Banner foto da van -->
                    <div class="relative h-36 w-full overflow-hidden bg-amber-100 shrink-0">
                        <img v-if="disp.van.foto_url"
                            :src="disp.van.foto_url"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            :alt="disp.van.nome_servico || disp.van.placa" />
                        <div v-else class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-amber-400 to-amber-600">
                            <TruckIcon class="w-14 h-14 text-white/60" />
                        </div>
                        <!-- Overlay sutil "ver detalhes" -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-3">
                            <span class="text-xs font-semibold text-white/90">Ver detalhes</span>
                        </div>
                        <!-- Badge vinculado/solicitado -->
                        <div v-if="jaVinculado(disp.id_disponibilidade)"
                            class="absolute top-2 right-2 text-xs px-2 py-0.5 rounded-full bg-emerald-600 text-white font-bold shadow">
                            Vinculado
                        </div>
                        <div v-else-if="jaSolicitado(disp.id_disponibilidade)"
                            class="absolute top-2 right-2 text-xs px-2 py-0.5 rounded-full bg-amber-500 text-white font-bold shadow">
                            Solicitado
                        </div>
                    </div>

                    <!-- Motorista + badges -->
                    <div class="flex items-center gap-3 px-4 pt-4 pb-3 border-b border-slate-100">
                        <div class="w-9 h-9 rounded-full overflow-hidden shrink-0">
                            <img v-if="disp.motorista.foto_url"
                                :src="disp.motorista.foto_url"
                                class="w-full h-full object-cover"
                                :alt="disp.motorista.nome" />
                            <div v-else class="w-full h-full bg-blue-600 flex items-center justify-center">
                                <span class="text-white font-bold text-sm">{{ inicialNome(disp.motorista.nome) }}</span>
                            </div>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <p class="font-bold text-slate-900 text-sm truncate">
                                    {{ disp.van.nome_servico || disp.motorista.nome || '—' }}
                                </p>
                                <span v-if="disp.motorista.is_teste"
                                    class="inline-flex items-center gap-1 text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-violet-100 text-violet-700 border border-violet-200 shrink-0">
                                    <BeakerIcon class="w-2.5 h-2.5" /> Teste
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 truncate">
                                <template v-if="disp.van.nome_servico">{{ disp.motorista.nome }} · </template>{{ disp.van.marca }} {{ disp.van.modelo }}
                            </p>
                            <span v-if="disp.van.prefixo_municipal"
                                class="inline-block mt-0.5 text-[10px] font-bold px-1.5 py-0.5 rounded bg-blue-100 text-blue-700">
                                Pref. {{ disp.van.prefixo_municipal }}
                            </span>
                        </div>
                        <span class="flex items-center gap-1 text-xs px-2 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold shrink-0">
                            <ShieldCheckIcon class="w-3.5 h-3.5" />Verificado
                        </span>
                    </div>

                    <!-- Corpo resumido -->
                    <div class="px-4 py-3 space-y-2.5 flex-1">
                        <!-- Turno + nome -->
                        <div class="flex items-center gap-2">
                            <component :is="turnoIcon(disp.turno)" class="w-4 h-4 shrink-0"
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
                                :class="f.dias.includes(dia) ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-600'">
                                {{ DIAS_MAP[dia] }}
                            </span>
                        </div>
                        <!-- Preço e vagas -->
                        <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2">
                            <div class="flex items-center gap-1.5">
                                <CurrencyDollarIcon class="w-4 h-4 text-emerald-600" />
                                <span class="text-sm font-bold text-emerald-700">
                                    {{ formatarPreco(disp.preco_mensal) }}<span v-if="disp.preco_mensal !== null" class="text-xs font-normal text-slate-400">/mês</span>
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

                    <!-- Ações do card (click.stop para não abrir o detalhe) -->
                    <div class="px-4 pb-4 flex gap-2" @click.stop>
                        <a v-if="disp.motorista.telefone"
                            :href="whatsappUrl(disp.motorista.telefone)"
                            target="_blank" rel="noopener noreferrer"
                            class="flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-700 text-xs font-semibold hover:bg-emerald-100 transition shrink-0">
                            <ChatBubbleLeftRightIcon class="w-4 h-4" />
                        </a>
                        <div v-if="jaVinculado(disp.id_disponibilidade)"
                            class="flex-1 py-2.5 rounded-xl text-xs font-bold text-center bg-emerald-100 text-emerald-700 border border-emerald-200">
                            Já vinculado
                        </div>
                        <div v-else-if="jaSolicitado(disp.id_disponibilidade)"
                            class="flex-1 py-2.5 rounded-xl text-xs font-bold text-center bg-amber-100 text-amber-700 border border-amber-200">
                            Aguardando resposta
                        </div>
                        <button v-else
                            :disabled="disp.vagas_disponiveis === 0 || passageiros.length === 0"
                            @click="abrirModal(disp)"
                            class="flex-1 py-2.5 rounded-xl text-xs font-bold transition"
                            :class="disp.vagas_disponiveis > 0 && passageiros.length > 0
                                ? 'bg-blue-600 text-white hover:bg-blue-700 shadow-sm'
                                : 'bg-slate-100 text-slate-400 cursor-not-allowed'">
                            {{ passageiros.length === 0 ? 'Sem passageiro'
                                : disp.vagas_disponiveis === 0 ? 'Sem vagas'
                                : 'Solicitar vaga' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Paginação -->
            <div v-if="disponibilidades.last_page > 1"
                class="flex items-center justify-center gap-1 pt-2 pb-6">
                <button :disabled="!disponibilidades.prev_page_url"
                    @click="irParaPagina(disponibilidades.prev_page_url)"
                    class="flex items-center gap-1 px-3 py-2 rounded-xl border text-sm font-medium transition"
                    :class="disponibilidades.prev_page_url
                        ? 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:border-blue-300'
                        : 'border-slate-100 bg-slate-50 text-slate-300 cursor-not-allowed'">
                    <ChevronLeftIcon class="w-4 h-4" /> Anterior
                </button>
                <button v-for="link in paginaLinks()" :key="link.label"
                    @click="irParaPagina(link.url)"
                    class="w-9 h-9 rounded-xl border text-sm font-semibold transition"
                    :class="link.active
                        ? 'bg-blue-600 text-white border-blue-600 shadow-sm'
                        : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:border-blue-300'">
                    {{ link.label }}
                </button>
                <button :disabled="!disponibilidades.next_page_url"
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

    <!-- ═══════════════════════════════════════════════════════════════════════
         BOTTOM SHEET — DETALHES COMPLETOS
    ════════════════════════════════════════════════════════════════════════════ -->
    <Teleport to="body">
        <Transition name="fade">
            <div v-if="detalheAberto"
                class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-slate-900/60 backdrop-blur-sm"
                @click.self="fecharDetalhe">
                <Transition name="slide-up">
                    <div v-if="detalheAberto"
                        class="bg-white w-full max-w-2xl rounded-t-3xl sm:rounded-2xl shadow-2xl flex flex-col"
                        style="max-height: 92vh">

                        <!-- ── Header fixo ── -->
                        <div class="shrink-0">
                            <!-- Handle bar (mobile) -->
                            <div class="flex justify-center pt-3 pb-1 sm:hidden">
                                <div class="w-10 h-1 rounded-full bg-slate-200"></div>
                            </div>

                            <div class="flex items-start gap-3 px-5 py-4 border-b border-slate-100">
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-slate-400 font-medium">Serviço de transporte escolar</p>
                                    <h2 class="font-bold text-slate-900 text-lg leading-tight mt-0.5">
                                        {{ detalheSelecionado?.van?.nome_servico || detalheSelecionado?.motorista?.nome }}
                                    </h2>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        {{ detalheSelecionado?.van?.marca }} {{ detalheSelecionado?.van?.modelo }}
                                        <span v-if="detalheSelecionado?.van?.prefixo_municipal" class="ml-1 font-semibold text-blue-600">
                                            · Prefixo {{ detalheSelecionado.van.prefixo_municipal }}
                                        </span>
                                    </p>
                                </div>
                                <button @click="fecharDetalhe"
                                    class="shrink-0 w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center hover:bg-slate-200 transition mt-0.5">
                                    <XMarkIcon class="w-4 h-4 text-slate-500" />
                                </button>
                            </div>

                            <!-- Tab bar -->
                            <div class="flex border-b border-slate-100 overflow-x-auto scrollbar-none">
                                <button v-for="aba in ABAS" :key="aba.key"
                                    @click="abaAtiva = aba.key"
                                    class="flex items-center gap-1.5 px-4 py-3 text-xs font-semibold whitespace-nowrap border-b-2 transition"
                                    :class="abaAtiva === aba.key
                                        ? 'border-blue-600 text-blue-600'
                                        : 'border-transparent text-slate-500 hover:text-slate-700'">
                                    <component :is="aba.icon" class="w-3.5 h-3.5" />
                                    {{ aba.label }}
                                </button>
                            </div>
                        </div>

                        <!-- ── Conteúdo rolável ── -->
                        <div class="flex-1 overflow-y-auto">

                            <!-- ABA: SERVIÇO -->
                            <div v-if="abaAtiva === 'servico'" class="p-5 space-y-4">

                                <!-- Preço + vagas -->
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-center">
                                        <p class="text-xs text-emerald-600 font-medium mb-0.5">Mensalidade</p>
                                        <p class="text-xl font-bold text-emerald-700">
                                            {{ formatarPreco(detalheSelecionado?.preco_mensal ?? 0) }}
                                        </p>
                                        <p class="text-xs text-emerald-500">por mês / passageiro</p>
                                    </div>
                                    <div class="rounded-xl bg-blue-50 border border-blue-200 px-4 py-3 text-center">
                                        <p class="text-xs text-blue-600 font-medium mb-0.5">Vagas disponíveis</p>
                                        <p class="text-xl font-bold"
                                            :class="(detalheSelecionado?.vagas_disponiveis ?? 0) > 0 ? 'text-blue-700' : 'text-red-600'">
                                            {{ detalheSelecionado?.vagas_disponiveis ?? 0 }}
                                        </p>
                                        <p class="text-xs text-blue-500">de {{ detalheSelecionado?.capacidade_total }} lugares</p>
                                    </div>
                                </div>

                                <!-- Turno -->
                                <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3 flex items-center gap-3">
                                    <component :is="turnoIcon(detalheSelecionado?.turno)"
                                        class="w-5 h-5 shrink-0"
                                        :class="detalheSelecionado?.turno === 'manha' ? 'text-amber-500' : detalheSelecionado?.turno === 'tarde' ? 'text-indigo-500' : 'text-slate-400'" />
                                    <div>
                                        <p class="text-xs text-slate-400 font-medium">Turno</p>
                                        <p class="text-sm font-semibold text-slate-800">{{ TURNOS[detalheSelecionado?.turno] ?? detalheSelecionado?.turno }}</p>
                                    </div>
                                </div>

                                <!-- Dias da semana -->
                                <div>
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Dias de atendimento</p>
                                    <div class="flex gap-2 flex-wrap">
                                        <span v-for="dia in diasOrdenados(detalheSelecionado?.dias ?? [])" :key="dia"
                                            class="px-3 py-1.5 rounded-lg bg-blue-100 text-blue-700 text-xs font-semibold">
                                            {{ DIAS_MAP[dia] }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Regiões atendidas -->
                                <div v-if="detalheSelecionado?.regioes_atendidas?.length">
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Regiões atendidas</p>
                                    <div class="space-y-2">
                                        <div v-for="regiao in detalheSelecionado.regioes_atendidas" :key="regiao.cidade"
                                            class="flex flex-wrap items-center gap-1.5">
                                            <span class="flex items-center gap-1 text-xs font-bold px-2.5 py-1 rounded-full bg-amber-100 text-amber-800">
                                                <MapPinIcon class="w-3 h-3" />
                                                {{ regiao.cidade }}
                                            </span>
                                            <span v-for="b in regiao.bairros" :key="b"
                                                class="text-xs px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                                                {{ b }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Escolas atendidas -->
                                <div v-if="detalheSelecionado?.escolas_atendidas?.length">
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Escolas atendidas</p>
                                    <div class="flex flex-wrap gap-1.5">
                                        <span v-for="e in detalheSelecionado.escolas_atendidas" :key="e"
                                            class="flex items-center gap-1 text-xs px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                            <AcademicCapIcon class="w-3 h-3" />
                                            {{ e }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- ABA: VAN -->
                            <div v-if="abaAtiva === 'van'" class="p-5 space-y-4">

                                <!-- Galeria de fotos -->
                                <div v-if="fotos.length > 0">
                                    <div class="rounded-xl overflow-hidden bg-slate-100 aspect-video">
                                        <img :src="fotos[fotoAtiva]?.url"
                                            :alt="fotos[fotoAtiva]?.label"
                                            class="w-full h-full object-cover" />
                                    </div>
                                    <div v-if="fotos.length > 1" class="flex gap-2 mt-2.5 overflow-x-auto pb-1">
                                        <button v-for="(foto, idx) in fotos" :key="idx"
                                            @click="fotoAtiva = idx"
                                            class="shrink-0 w-16 h-12 rounded-lg overflow-hidden border-2 transition"
                                            :class="fotoAtiva === idx ? 'border-blue-500' : 'border-transparent opacity-60 hover:opacity-90'">
                                            <img :src="foto.url" :alt="foto.label" class="w-full h-full object-cover" />
                                        </button>
                                    </div>
                                </div>
                                <div v-else class="rounded-xl bg-slate-100 aspect-video flex items-center justify-center">
                                    <div class="flex flex-col items-center gap-2 text-slate-400">
                                        <PhotoIcon class="w-10 h-10" />
                                        <p class="text-xs">Sem fotos</p>
                                    </div>
                                </div>

                                <!-- Dados do veículo -->
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="rounded-xl border border-slate-100 bg-slate-50 px-3 py-2.5">
                                        <p class="text-xs text-slate-400">Placa</p>
                                        <p class="text-sm font-bold text-slate-800 font-mono tracking-widest mt-0.5">{{ detalheSelecionado?.van?.placa }}</p>
                                    </div>
                                    <div class="rounded-xl border border-slate-100 bg-slate-50 px-3 py-2.5">
                                        <p class="text-xs text-slate-400">Cor</p>
                                        <p class="text-sm font-semibold text-slate-800 mt-0.5">{{ detalheSelecionado?.van?.cor ?? '—' }}</p>
                                    </div>
                                    <div class="rounded-xl border border-slate-100 bg-slate-50 px-3 py-2.5">
                                        <p class="text-xs text-slate-400">Marca / Modelo</p>
                                        <p class="text-sm font-semibold text-slate-800 mt-0.5">{{ detalheSelecionado?.van?.marca }} {{ detalheSelecionado?.van?.modelo }}</p>
                                    </div>
                                    <div class="rounded-xl border border-slate-100 bg-slate-50 px-3 py-2.5">
                                        <p class="text-xs text-slate-400">Ano</p>
                                        <p class="text-sm font-semibold text-slate-800 mt-0.5">{{ detalheSelecionado?.van?.ano ?? '—' }}</p>
                                    </div>
                                    <div class="rounded-xl border border-slate-100 bg-slate-50 px-3 py-2.5">
                                        <p class="text-xs text-slate-400">Capacidade</p>
                                        <p class="text-sm font-semibold text-slate-800 mt-0.5">{{ detalheSelecionado?.van?.capacidade_passageiros ?? '—' }} passageiros</p>
                                    </div>
                                    <div v-if="detalheSelecionado?.van?.prefixo_municipal" class="rounded-xl border border-slate-100 bg-slate-50 px-3 py-2.5">
                                        <p class="text-xs text-slate-400">Prefixo municipal</p>
                                        <p class="text-sm font-bold text-blue-700 mt-0.5">{{ detalheSelecionado.van.prefixo_municipal }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- ABA: MOTORISTA -->
                            <div v-if="abaAtiva === 'motorista'" class="p-5 space-y-4">

                                <!-- Foto + nome -->
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-2xl overflow-hidden shrink-0">
                                        <img v-if="detalheSelecionado?.motorista?.foto_url"
                                            :src="detalheSelecionado.motorista.foto_url"
                                            :alt="detalheSelecionado.motorista.nome"
                                            class="w-full h-full object-cover" />
                                        <div v-else class="w-full h-full bg-blue-600 flex items-center justify-center">
                                            <span class="text-white font-bold text-2xl">
                                                {{ inicialNome(detalheSelecionado?.motorista?.nome) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 text-base">{{ detalheSelecionado?.motorista?.nome ?? '—' }}</p>
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full mt-1">
                                            <ShieldCheckIcon class="w-3.5 h-3.5" />
                                            Aprovado pela Rota Segura
                                        </span>
                                    </div>
                                </div>

                                <!-- Contato -->
                                <div v-if="detalheSelecionado?.motorista?.telefone"
                                    class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3 flex items-center gap-3">
                                    <PhoneIcon class="w-4 h-4 text-slate-400 shrink-0" />
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-slate-400">Telefone</p>
                                        <p class="text-sm font-semibold text-slate-800">{{ detalheSelecionado.motorista.telefone }}</p>
                                    </div>
                                    <a :href="whatsappUrl(detalheSelecionado.motorista.telefone)"
                                        target="_blank" rel="noopener noreferrer"
                                        class="flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-3 py-1.5 rounded-lg transition"
                                        @click.stop>
                                        <ChatBubbleLeftRightIcon class="w-4 h-4" />
                                        WhatsApp
                                    </a>
                                </div>

                                <!-- CNH -->
                                <div v-if="detalheSelecionado?.motorista?.cnh_categoria"
                                    class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3 flex items-center gap-3">
                                    <IdentificationIcon class="w-4 h-4 text-slate-400 shrink-0" />
                                    <div>
                                        <p class="text-xs text-slate-400">Categoria da CNH</p>
                                        <p class="text-sm font-bold text-slate-800">Categoria {{ detalheSelecionado.motorista.cnh_categoria }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- ABA: VERIFICAÇÃO -->
                            <div v-if="abaAtiva === 'verificacao'" class="p-5 space-y-4">

                                <!-- Badge de aprovação geral -->
                                <div class="flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3.5">
                                    <ShieldCheckIcon class="w-6 h-6 text-emerald-600 shrink-0" />
                                    <div>
                                        <p class="text-sm font-bold text-emerald-800">Serviço verificado pela equipe Rota Segura</p>
                                        <p class="text-xs text-emerald-600 mt-0.5">Motorista e veículo foram analisados e aprovados.</p>
                                    </div>
                                </div>

                                <!-- Status dos documentos -->
                                <div>
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2.5">Documentos do veículo</p>
                                    <div class="space-y-2">

                                        <div v-for="doc in [
                                            { label: 'CRLV (Registro do Veículo)',   ok: detalheSelecionado?.van?.doc_crlv,        url: detalheSelecionado?.van?.crlv_url,                   validade: detalheSelecionado?.van?.crlv_validade },
                                            { label: 'Seguro de Passageiros',        ok: detalheSelecionado?.van?.doc_seguro,       url: detalheSelecionado?.van?.seguro_url,                 validade: detalheSelecionado?.van?.seguro_validade },
                                            { label: 'Autorização Municipal (SMTM)', ok: detalheSelecionado?.van?.doc_autorizacao,  url: detalheSelecionado?.van?.autorizacao_municipal_url,  validade: detalheSelecionado?.van?.autorizacao_municipal_validade },
                                            { label: 'IPVA (Comprovante)',           ok: detalheSelecionado?.van?.doc_ipva,          url: null,                                               validade: null },
                                        ]" :key="doc.label"
                                            class="flex items-center gap-3 rounded-xl border px-4 py-3"
                                            :class="doc.ok ? 'border-emerald-100 bg-emerald-50/60' : 'border-slate-100 bg-slate-50'">
                                            <component :is="doc.ok ? CheckCircleIcon : XCircleIcon"
                                                class="w-5 h-5 shrink-0"
                                                :class="doc.ok ? 'text-emerald-500' : 'text-slate-300'" />
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium"
                                                    :class="doc.ok ? 'text-slate-800' : 'text-slate-400'">
                                                    {{ doc.label }}
                                                </p>
                                                <p v-if="doc.validade" class="text-xs text-slate-400 mt-0.5">Válido até {{ doc.validade }}</p>
                                            </div>
                                            <a v-if="doc.url" :href="doc.url" target="_blank" rel="noopener noreferrer"
                                                class="flex items-center gap-1 text-xs font-semibold text-blue-600 hover:underline shrink-0">
                                                <ArrowTopRightOnSquareIcon class="w-3.5 h-3.5" />
                                                Ver
                                            </a>
                                            <span v-else class="text-xs font-semibold px-2 py-0.5 rounded-full shrink-0"
                                                :class="doc.ok ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-400'">
                                                {{ doc.ok ? 'Enviado' : 'Pendente' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Nota LGPD -->
                                <div class="flex items-start gap-2.5 rounded-xl bg-slate-50 border border-slate-200 px-4 py-3">
                                    <InformationCircleIcon class="w-4 h-4 text-slate-400 shrink-0 mt-0.5" />
                                    <p class="text-xs text-slate-500 leading-relaxed">
                                        O <strong class="text-slate-700">CRLV, seguro e autorização municipal</strong> são documentos regulatórios que você pode consultar.
                                        A CNH e certidões pessoais do motorista ficam restritas à equipe da Rota Segura, em conformidade com a LGPD.
                                    </p>
                                </div>
                            </div>

                        </div>

                        <!-- ── Footer fixo com ações ── -->
                        <div class="shrink-0 px-5 py-4 border-t border-slate-100 flex gap-3">
                            <a v-if="detalheSelecionado?.motorista?.telefone"
                                :href="whatsappUrl(detalheSelecionado.motorista.telefone)"
                                target="_blank" rel="noopener noreferrer"
                                class="flex items-center gap-1.5 px-4 py-3 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-700 text-sm font-semibold hover:bg-emerald-100 transition shrink-0"
                                @click.stop>
                                <ChatBubbleLeftRightIcon class="w-4 h-4" />
                                WhatsApp
                            </a>

                            <div v-if="jaVinculado(detalheSelecionado?.id_disponibilidade)"
                                class="flex-1 py-3 rounded-xl text-sm font-bold text-center bg-emerald-100 text-emerald-700 border border-emerald-200">
                                Já vinculado
                            </div>
                            <div v-else-if="jaSolicitado(detalheSelecionado?.id_disponibilidade)"
                                class="flex-1 py-3 rounded-xl text-sm font-bold text-center bg-amber-100 text-amber-700 border border-amber-200">
                                Aguardando resposta
                            </div>
                            <button v-else
                                :disabled="(detalheSelecionado?.vagas_disponiveis ?? 0) === 0 || passageiros.length === 0"
                                @click="abrirModal(detalheSelecionado)"
                                class="flex-1 py-3 rounded-xl text-sm font-bold transition shadow-sm"
                                :class="(detalheSelecionado?.vagas_disponiveis ?? 0) > 0 && passageiros.length > 0
                                    ? 'bg-blue-600 text-white hover:bg-blue-700'
                                    : 'bg-slate-100 text-slate-400 cursor-not-allowed'">
                                {{ passageiros.length === 0 ? 'Sem passageiro cadastrado'
                                    : (detalheSelecionado?.vagas_disponiveis ?? 0) === 0 ? 'Sem vagas disponíveis'
                                    : 'Solicitar vaga' }}
                            </button>
                        </div>

                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>

    <!-- ═══════════════════════════════════════════════════════════════════════
         MODAL — SOLICITAR VAGA
    ════════════════════════════════════════════════════════════════════════════ -->
    <Teleport to="body">
        <Transition name="fade">
            <div v-if="modalAberto"
                class="fixed inset-0 z-[60] flex items-end sm:items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
                @click.self="fecharModal">
                <Transition name="pop">
                    <div v-if="modalAberto" class="bg-white rounded-2xl w-full max-w-md shadow-2xl border border-slate-100">

                        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                            <div>
                                <p class="text-xs text-slate-400 font-medium">Solicitação de vaga</p>
                                <h3 class="font-bold text-slate-900 mt-0.5">{{ disponSelecionada?.nome }}</h3>
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
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Para qual passageiro?</label>
                                <select v-model="form.id_passageiro"
                                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition bg-white"
                                    :class="form.errors.id_passageiro ? 'border-red-300 bg-red-50' : ''">
                                    <option value="" disabled>Selecione o passageiro</option>
                                    <option v-for="p in passageiros" :key="p.id_passageiro" :value="p.id_passageiro">
                                        {{ p.nome }}{{ (!p.tem_embarque || !p.tem_desembarque) ? ' — sem endereço' : '' }}
                                    </option>
                                </select>
                                <p v-if="form.errors.id_passageiro" class="mt-1 text-xs text-red-600">{{ form.errors.id_passageiro }}</p>

                                <div v-if="semEnderecos"
                                    class="mt-2 flex items-start gap-2 rounded-xl bg-amber-50 border border-amber-200 px-3 py-2.5">
                                    <ExclamationTriangleIcon class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" />
                                    <div class="text-xs text-amber-800">
                                        <p class="font-semibold">Endereços não cadastrados</p>
                                        <p class="mt-0.5">
                                            Cadastre os endereços de
                                            <strong>{{ !passageiroSelecionado?.tem_embarque ? 'embarque' : '' }}{{ (!passageiroSelecionado?.tem_embarque && !passageiroSelecionado?.tem_desembarque) ? ' e ' : '' }}{{ !passageiroSelecionado?.tem_desembarque ? 'destino (escola)' : '' }}</strong>
                                            antes de solicitar.
                                        </p>
                                        <a :href="route('responsavel.passageiros.enderecos', passageiroSelecionado?.id_passageiro)"
                                            class="mt-1.5 inline-block font-semibold text-amber-700 underline hover:text-amber-900">
                                            Cadastrar endereços
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                    Dias da semana
                                    <span class="ml-1 text-slate-400 font-normal">(selecione os que precisar)</span>
                                </label>
                                <div class="flex gap-1.5 flex-wrap">
                                    <button v-for="dia in diasOrdenados(disponSelecionada?.dias ?? [])" :key="dia"
                                        type="button" @click="toggleDiaContratado(dia)"
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
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Mensagem (opcional)</label>
                                <textarea v-model="form.mensagem" rows="3"
                                    placeholder="Ex: Pode buscar entre 7h e 7h30. Mora no Igara, escola começa às 8h. Deixa às 12h30."
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
                                    : 'bg-slate-100 text-slate-400 cursor-not-allowed'">
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
/* Fade backdrop */
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

/* Slide-up do bottom sheet */
.slide-up-enter-active { transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
.slide-up-leave-active { transition: all 0.2s ease-in; }
.slide-up-enter-from  { opacity: 0; transform: translateY(40px); }
.slide-up-leave-to    { opacity: 0; transform: translateY(20px); }

/* Pop do modal de solicitação */
.pop-enter-active { transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1); }
.pop-leave-active { transition: all 0.15s ease-in; }
.pop-enter-from   { opacity: 0; transform: scale(0.94) translateY(20px); }
.pop-leave-to     { opacity: 0; transform: scale(0.96); }

/* Esconder scrollbar da tab bar */
.scrollbar-none { scrollbar-width: none; }
.scrollbar-none::-webkit-scrollbar { display: none; }
</style>
