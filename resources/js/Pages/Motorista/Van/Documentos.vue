<script setup>
import { reactive, computed, ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import {
    ArrowLeftIcon,
    CameraIcon,
    DocumentTextIcon,
    ShieldCheckIcon,
    CheckCircleIcon,
    ExclamationCircleIcon,
    ClockIcon,
    ArrowUpTrayIcon,
    LockClosedIcon,
    PaperClipIcon,
    ArrowTopRightOnSquareIcon,
    WrenchScrewdriverIcon,
} from '@heroicons/vue/24/outline'
import FlashMessage from '@/Components/UI/FlashMessage.vue'

const props = defineProps({
    van: { type: Object, required: true },
})

// ── STATUS DOS DOCUMENTOS ──────────────────────────────────────────────────

function docStatus(url, validade) {
    if (!url) return 'pendente'
    if (validade) {
        const hoje = new Date()
        hoje.setHours(0, 0, 0, 0)
        if (new Date(validade) < hoje) return 'vencido'
    }
    return 'enviado'
}

const statusCRLV         = computed(() => docStatus(props.van.crlv_url, props.van.crlv_validade))
const statusSeguro       = computed(() => docStatus(props.van.seguro_url, props.van.seguro_validade))
const statusAutorizacao  = computed(() => docStatus(props.van.autorizacao_municipal_url, props.van.autorizacao_municipal_validade))

const essenciaisEnviados = computed(() =>
    [statusCRLV.value, statusSeguro.value, statusAutorizacao.value].filter(s => s === 'enviado').length
)

// ── FOTOS — upload imediato ────────────────────────────────────────────────

const fotoInputRefs = {}
const fotoLoading   = reactive({})

const fotoSlots = [
    { key: 'frente',      label: 'Frente',       col: 'foto_url',             routeName: 'motorista.van.foto' },
    { key: 'verso',       label: 'Verso',         col: 'foto_verso_url',       routeName: 'motorista.van.foto.verso' },
    { key: 'interior',    label: 'Interior',      col: 'foto_interior_url',    routeName: 'motorista.van.foto.interior' },
    { key: 'lat_esq',     label: 'Lateral Esq.',  col: 'foto_lateral_esq_url', routeName: 'motorista.van.foto.lateral_esq' },
    { key: 'lat_dir',     label: 'Lateral Dir.',  col: 'foto_lateral_dir_url', routeName: 'motorista.van.foto.lateral_dir' },
]

function setFotoRef(el, key) { if (el) fotoInputRefs[key] = el }
function triggerFoto(key) { fotoInputRefs[key]?.click() }

function onFotoChange(event, slot) {
    const file = event.target.files[0]
    if (!file) return
    fotoLoading[slot.key] = true
    router.post(route(slot.routeName), { foto: file }, {
        forceFormData: true,
        preserveScroll: true,
        onFinish: () => { fotoLoading[slot.key] = false },
    })
}

// ── FORMULÁRIOS DE DOCUMENTOS ──────────────────────────────────────────────

const docForms = reactive({
    crlv: {
        arquivo: null, arquivoNome: null,
        validade: props.van.crlv_validade ?? '',
        processing: false, errors: {},
    },
    seguro: {
        arquivo: null, arquivoNome: null,
        validade: props.van.seguro_validade ?? '',
        processing: false, errors: {},
    },
    autorizacao_municipal: {
        arquivo: null, arquivoNome: null,
        validade: props.van.autorizacao_municipal_validade ?? '',
        prefixo: props.van.prefixo_municipal ?? '',
        processing: false, errors: {},
    },
    ipva: {
        arquivo: null, arquivoNome: null,
        validade: props.van.ipva_comprovante_data ?? '',
        processing: false, errors: {},
    },
    inspecao: {
        ultima:  props.van.data_ultima_inspecao ?? '',
        proxima: props.van.proxima_inspecao_prevista ?? '',
        processing: false, errors: {},
    },
})

const docInputRefs = {}
function setDocRef(el, tipo) { if (el) docInputRefs[tipo] = el }
function triggerDoc(tipo) { docInputRefs[tipo]?.click() }

function onDocFileChange(event, tipo) {
    const file = event.target.files[0]
    if (!file) return
    docForms[tipo].arquivo    = file
    docForms[tipo].arquivoNome = file.name
}

function submitDoc(tipo) {
    const f = docForms[tipo]
    f.processing = true

    const data = {}
    if (f.arquivo) data.arquivo = f.arquivo
    if (f.validade !== undefined) data.validade = f.validade || null
    if (tipo === 'autorizacao_municipal') data.prefixo_municipal = f.prefixo || null

    router.post(route('motorista.van.documento.upload', tipo), data, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => { f.arquivo = null; f.arquivoNome = null; f.errors = {} },
        onError: (errors) => { f.errors = errors },
        onFinish: () => { f.processing = false },
    })
}

function salvarInspecao() {
    const f = docForms.inspecao
    f.processing = true
    router.post(route('motorista.van.inspecao'), {
        data_ultima_inspecao:      f.ultima  || null,
        proxima_inspecao_prevista: f.proxima || null,
    }, {
        preserveScroll: true,
        onError:  (errors) => { f.errors = errors },
        onFinish: () => { f.processing = false },
    })
}

// ── HELPERS ────────────────────────────────────────────────────────────────

function isPdf(url) { return url?.toLowerCase().endsWith('.pdf') }

function nomeArquivo(url) {
    if (!url) return null
    return decodeURIComponent(url.split('/').pop())
}
</script>

<template>
    <Head title="Documentos da van — Motorista" />
    <FlashMessage />

    <div class="min-h-screen bg-slate-50">

        <!-- HEADER ─────────────────────────────────────────────────────────── -->
        <header class="bg-gradient-to-b from-amber-700 to-amber-800 shadow-sm sticky top-0 z-10">
            <div class="max-w-2xl mx-auto px-4 py-4 flex items-center gap-3">
                <Link :href="route('motorista.dashboard')"
                    class="w-9 h-9 rounded-xl bg-white/20 hover:bg-white/30 flex items-center justify-center transition shrink-0">
                    <ArrowLeftIcon class="w-5 h-5 text-white" />
                </Link>
                <div class="flex-1 min-w-0">
                    <h1 class="text-lg font-bold text-white leading-tight">Documentos e fotos</h1>
                    <p class="text-xs text-amber-200 truncate">{{ van.nome_servico || van.placa }}</p>
                </div>
                <!-- Chip de completude -->
                <div class="shrink-0 flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold"
                    :class="essenciaisEnviados === 3
                        ? 'bg-emerald-100 text-emerald-700'
                        : 'bg-white/20 text-white'">
                    <CheckCircleIcon v-if="essenciaisEnviados === 3" class="w-3.5 h-3.5" />
                    <span>{{ essenciaisEnviados }}/3 essenciais</span>
                </div>
            </div>
        </header>

        <main class="max-w-2xl mx-auto px-4 py-6 space-y-5 pb-12">

            <!-- AVISO DE PRIVACIDADE ─────────────────────────────────────────── -->
            <div class="flex gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3">
                <LockClosedIcon class="w-4 h-4 text-slate-400 shrink-0 mt-0.5" />
                <p class="text-xs text-slate-500 leading-relaxed">
                    Seus documentos são enviados de forma segura. O <strong class="text-slate-700">CRLV, seguro e autorização municipal</strong> são visíveis aos responsáveis para fins de verificação regulatória.
                    A <strong class="text-slate-700">CNH e documentos pessoais</strong> ficam restritos à equipe da Rota Segura.
                </p>
            </div>

            <!-- SEÇÃO: FOTOS DA VAN ─────────────────────────────────────────── -->
            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden"
                :class="!van.foto_url ? 'ring-2 ring-amber-400' : ''">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-amber-100 bg-amber-50/60">
                    <div class="w-8 h-8 rounded-xl bg-amber-700 flex items-center justify-center shrink-0">
                        <CameraIcon class="w-4 h-4 text-white" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-sm font-bold text-slate-800">Fotos da van</h2>
                        <p class="text-xs text-slate-500">Quanto mais fotos, mais confiança para os responsáveis</p>
                    </div>
                    <span v-if="!van.foto_url"
                        class="shrink-0 text-xs font-bold px-2 py-1 rounded-full bg-amber-100 text-amber-700 border border-amber-300">
                        Obrigatória
                    </span>
                </div>
                <!-- Aviso: foto frontal obrigatória -->
                <div v-if="!van.foto_url"
                    class="mx-5 mt-4 flex items-start gap-2.5 rounded-xl bg-amber-50 border border-amber-200 px-4 py-3">
                    <ExclamationCircleIcon class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" />
                    <p class="text-xs text-amber-800 leading-relaxed">
                        <strong>A foto frontal é obrigatória</strong> para que sua van apareça no marketplace.
                        Adicione ao menos a foto <strong>Frente</strong> para que os responsáveis possam te encontrar.
                    </p>
                </div>

                <div class="px-5 py-5">
                    <div class="grid grid-cols-5 gap-2.5">
                        <div v-for="slot in fotoSlots" :key="slot.key">
                            <button type="button" @click="triggerFoto(slot.key)"
                                class="relative group w-full focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-600 rounded-xl">
                                <div class="aspect-square rounded-xl overflow-hidden flex items-center justify-center border-2 transition"
                                    :class="van[slot.col] ? 'border-amber-300 bg-amber-50' : 'border-dashed border-slate-300 bg-slate-50'">
                                    <img v-if="van[slot.col]" :src="van[slot.col]"
                                        class="w-full h-full object-cover" :alt="slot.label" />
                                    <div v-else class="flex flex-col items-center gap-1 p-1">
                                        <CameraIcon class="w-5 h-5 text-slate-300" />
                                    </div>
                                </div>
                                <!-- overlay hover -->
                                <div class="absolute inset-0 rounded-xl bg-black/35 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                    <span v-if="fotoLoading[slot.key]"
                                        class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" />
                                    <CameraIcon v-else class="w-4 h-4 text-white" />
                                </div>
                                <!-- check quando tem foto -->
                                <span v-if="van[slot.col]"
                                    class="absolute top-1 right-1 w-4 h-4 rounded-full bg-green-500 flex items-center justify-center">
                                    <CheckCircleIcon class="w-3 h-3 text-white" />
                                </span>
                            </button>
                            <p class="mt-1.5 text-[10px] font-semibold text-slate-600 text-center">{{ slot.label }}</p>
                            <!-- Input oculto -->
                            <input :ref="el => setFotoRef(el, slot.key)" type="file" class="hidden"
                                accept="image/jpeg,image/png,image/webp"
                                @change="e => onFotoChange(e, slot)" />
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 mt-3">JPG, PNG ou WebP · máx. 2 MB por foto · clique para trocar</p>
                </div>
            </section>

            <!-- SEÇÃO: DOCUMENTOS ESSENCIAIS ────────────────────────────────── -->
            <div>
                <div class="flex items-center gap-2">
                    <ShieldCheckIcon class="w-4 h-4 text-amber-600" />
                    <h3 class="text-base font-bold text-slate-800">Documentos essenciais</h3>
                </div>
                <p class="text-xs text-slate-400 mt-0.5 ml-6">Obrigatórios para aprovação no marketplace</p>
            </div>

            <!-- CRLV ─────────────────────────────────────────────────────────── -->
            <DocCard
                titulo="CRLV"
                descricao="Certificado de Registro e Licenciamento do Veículo (categoria aluguel/passageiros)"
                :status="statusCRLV"
                :url-atual="van.crlv_url"
            >
                <template #form>
                    <FormDocumento tipo="crlv" label-validade="Data de validade do CRLV"
                        :form="docForms.crlv"
                        @file-change="e => onDocFileChange(e, 'crlv')"
                        @trigger="triggerDoc('crlv')"
                        @submit="submitDoc('crlv')"
                        :input-ref="el => setDocRef(el, 'crlv')" />
                </template>
            </DocCard>

            <!-- Seguro ──────────────────────────────────────────────────────── -->
            <DocCard
                titulo="Seguro de Passageiros"
                descricao="Apólice de seguro com cobertura de passageiros (mín. ~R$ 10.000/pessoa)"
                :status="statusSeguro"
                :url-atual="van.seguro_url"
            >
                <template #form>
                    <FormDocumento tipo="seguro" label-validade="Validade da apólice"
                        :form="docForms.seguro"
                        @file-change="e => onDocFileChange(e, 'seguro')"
                        @trigger="triggerDoc('seguro')"
                        @submit="submitDoc('seguro')"
                        :input-ref="el => setDocRef(el, 'seguro')" />
                </template>
            </DocCard>

            <!-- Autorização Municipal ──────────────────────────────────────── -->
            <DocCard
                titulo="Autorização Municipal"
                descricao="Credenciamento emitido pela prefeitura (SMTM/Secretaria de Trânsito)"
                :status="statusAutorizacao"
                :url-atual="van.autorizacao_municipal_url"
            >
                <template #form>
                    <!-- Prefixo -->
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Número do prefixo / código de autorização
                        </label>
                        <input v-model="docForms.autorizacao_municipal.prefixo"
                            type="text" maxlength="20"
                            placeholder="Ex: 47, RS-23, A-112…"
                            class="w-full rounded-xl border px-4 py-2.5 text-sm outline-none transition"
                            :class="docForms.autorizacao_municipal.errors.prefixo_municipal
                                ? 'border-red-300 bg-red-50'
                                : 'border-slate-200 focus:border-amber-600 focus:ring-2 focus:ring-amber-100'" />
                        <p v-if="docForms.autorizacao_municipal.errors.prefixo_municipal"
                            class="mt-1 text-xs text-red-600">{{ docForms.autorizacao_municipal.errors.prefixo_municipal }}</p>
                        <p class="mt-1 text-xs text-slate-400">Número que aparece no documento emitido pela prefeitura</p>
                    </div>

                    <FormDocumento tipo="autorizacao_municipal" label-validade="Validade da autorização"
                        :form="docForms.autorizacao_municipal"
                        @file-change="e => onDocFileChange(e, 'autorizacao_municipal')"
                        @trigger="triggerDoc('autorizacao_municipal')"
                        @submit="submitDoc('autorizacao_municipal')"
                        :input-ref="el => setDocRef(el, 'autorizacao_municipal')" />
                </template>
            </DocCard>

            <!-- SEÇÃO: DOCUMENTOS COMPLEMENTARES ────────────────────────────── -->
            <div class="flex items-center gap-2 mt-2">
                <DocumentTextIcon class="w-4 h-4 text-slate-400" />
                <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wide">Complementares</h3>
                <span class="text-xs text-slate-400">(opcionais mas recomendados)</span>
            </div>

            <!-- IPVA ─────────────────────────────────────────────────────────── -->
            <DocCard
                titulo="Comprovante de IPVA"
                descricao="Comprovante de pagamento do IPVA do exercício atual"
                :status="docStatus(van.ipva_comprovante_url, null)"
                :url-atual="van.ipva_comprovante_url"
            >
                <template #form>
                    <FormDocumento tipo="ipva" label-validade="Data de pagamento"
                        :form="docForms.ipva"
                        @file-change="e => onDocFileChange(e, 'ipva')"
                        @trigger="triggerDoc('ipva')"
                        @submit="submitDoc('ipva')"
                        :input-ref="el => setDocRef(el, 'ipva')" />
                </template>
            </DocCard>

            <!-- Inspeção ─────────────────────────────────────────────────────── -->
            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
                            <WrenchScrewdriverIcon class="w-4 h-4 text-slate-500" />
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800">Vistoria / Inspeção</p>
                            <p class="text-xs text-slate-500">Vistoria semestral e/ou pelo INMETRO (Canoas)</p>
                        </div>
                    </div>
                    <StatusChip :status="van.data_ultima_inspecao ? 'enviado' : 'pendente'" />
                </div>

                <div class="px-5 py-4 space-y-3">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Última inspeção</label>
                            <input v-model="docForms.inspecao.ultima" type="date"
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition focus:border-amber-600 focus:ring-2 focus:ring-amber-100" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Próxima prevista</label>
                            <input v-model="docForms.inspecao.proxima" type="date"
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition focus:border-amber-600 focus:ring-2 focus:ring-amber-100" />
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button @click="salvarInspecao" :disabled="docForms.inspecao.processing"
                            class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-amber-700 hover:bg-amber-800 disabled:opacity-60 text-white text-xs font-bold transition">
                            <span v-if="docForms.inspecao.processing"
                                class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin" />
                            {{ docForms.inspecao.processing ? 'Salvando…' : 'Salvar datas' }}
                        </button>
                    </div>
                </div>
            </section>

            <!-- LINK PARA EDITAR DADOS ──────────────────────────────────────── -->
            <div class="text-center pt-2">
                <Link :href="route('motorista.van.edit')"
                    class="text-xs text-slate-400 hover:text-amber-700 underline transition">
                    Editar dados da van (marca, modelo, capacidade…)
                </Link>
            </div>

        </main>
    </div>
</template>

<!-- ── SUB-COMPONENTES LOCAIS ──────────────────────────────────────────────── -->

<script>
// StatusChip — reutilizado em vários cards
const StatusChip = {
    props: { status: String },
    template: `
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold shrink-0"
            :class="{
                'bg-slate-100 text-slate-500': status === 'pendente',
                'bg-emerald-100 text-emerald-700': status === 'enviado',
                'bg-red-100 text-red-700': status === 'vencido',
            }">
            <span class="w-1.5 h-1.5 rounded-full"
                :class="{
                    'bg-slate-400': status === 'pendente',
                    'bg-emerald-500': status === 'enviado',
                    'bg-red-500': status === 'vencido',
                }" />
            {{ status === 'pendente' ? 'Pendente' : status === 'enviado' ? 'Enviado' : 'Vencido' }}
        </span>
    `,
}

// DocCard — wrapper de cada documento
const DocCard = {
    components: { StatusChip },
    props: { titulo: String, descricao: String, status: String, urlAtual: String },
    setup(props) {
        const isPdf = (url) => url?.toLowerCase().endsWith('.pdf')
        const nomeArquivo = (url) => url ? decodeURIComponent(url.split('/').pop()) : null
        return { isPdf, nomeArquivo }
    },
    template: `
        <section class="rounded-2xl border bg-white shadow-sm overflow-hidden"
            :class="status === 'enviado' ? 'border-emerald-200' : status === 'vencido' ? 'border-red-200' : 'border-slate-200'">
            <!-- Cabeçalho do card -->
            <div class="flex items-start justify-between gap-3 px-5 py-4 border-b"
                :class="status === 'enviado' ? 'border-emerald-100 bg-emerald-50/40'
                      : status === 'vencido' ? 'border-red-100 bg-red-50/40'
                      : 'border-slate-100 bg-slate-50/60'">
                <div>
                    <p class="text-base font-bold text-slate-800">{{ titulo }}</p>
                    <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">{{ descricao }}</p>
                </div>
                <StatusChip :status="status" />
            </div>

            <!-- Arquivo atual (se houver) -->
            <div v-if="urlAtual" class="flex items-center gap-2 px-5 py-2.5 border-b border-slate-100 bg-slate-50/50">
                <span class="text-xs text-slate-500">Arquivo atual:</span>
                <a :href="urlAtual" target="_blank"
                    class="flex items-center gap-1 text-xs text-amber-700 hover:underline font-medium truncate max-w-[200px]">
                    {{ nomeArquivo(urlAtual) }}
                    <ArrowTopRightOnSquareIcon class="w-3 h-3 shrink-0" />
                </a>
            </div>

            <!-- Formulário (slot) -->
            <div class="px-5 py-4">
                <slot name="form" />
            </div>
        </section>
    `,
}

// FormDocumento — form de upload reutilizável
const FormDocumento = {
    props: {
        tipo: String,
        labelValidade: String,
        form: Object,
        inputRef: Function,
    },
    emits: ['file-change', 'trigger', 'submit'],
    template: `
        <div class="space-y-3">
            <!-- Seletor de arquivo -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Arquivo (PDF ou imagem)</label>
                <button type="button" @click="$emit('trigger')"
                    class="w-full flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 border-dashed text-xs font-medium transition"
                    :class="form.arquivoNome
                        ? 'border-amber-300 bg-amber-50 text-amber-700'
                        : 'border-slate-200 hover:border-slate-300 text-slate-500 bg-white'">
                    <PaperClipIcon class="w-4 h-4 shrink-0" />
                    <span class="truncate">{{ form.arquivoNome || 'Selecionar arquivo…' }}</span>
                    <CheckCircleIcon v-if="form.arquivoNome" class="w-4 h-4 ml-auto text-amber-600 shrink-0" />
                </button>
                <input :ref="inputRef" type="file" class="hidden"
                    accept="image/jpeg,image/png,image/webp,application/pdf"
                    @change="$emit('file-change', $event)" />
                <p v-if="form.errors.arquivo" class="mt-1 text-xs text-red-600">{{ form.errors.arquivo }}</p>
            </div>

            <!-- Data de validade -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ labelValidade }}</label>
                <input v-model="form.validade" type="date"
                    class="w-full rounded-xl border px-4 py-2.5 text-sm outline-none transition"
                    :class="form.errors.validade
                        ? 'border-red-300 bg-red-50'
                        : 'border-slate-200 focus:border-amber-600 focus:ring-2 focus:ring-amber-100'" />
                <p v-if="form.errors.validade" class="mt-1 text-xs text-red-600">{{ form.errors.validade }}</p>
            </div>

            <!-- Botão salvar -->
            <div class="flex justify-end">
                <button @click="$emit('submit')" :disabled="form.processing"
                    class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-amber-700 hover:bg-amber-800 disabled:opacity-60 text-white text-xs font-bold transition">
                    <span v-if="form.processing"
                        class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin" />
                    <ArrowUpTrayIcon v-else class="w-3.5 h-3.5" />
                    {{ form.processing ? 'Enviando…' : 'Salvar' }}
                </button>
            </div>
        </div>
    `,
}

export default {
    components: { StatusChip, DocCard, FormDocumento },
}
</script>
