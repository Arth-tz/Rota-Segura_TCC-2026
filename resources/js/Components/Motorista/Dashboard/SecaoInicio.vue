<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { TruckIcon, UserGroupIcon, MapIcon, PlusIcon, PencilSquareIcon, CameraIcon, ExclamationCircleIcon, CheckCircleIcon, XCircleIcon, ArrowRightIcon, ShieldCheckIcon, EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    motorista:             { type: Object, default: null },
    van:                   { type: Object, default: null },
    passageiros:           { type: Array,  default: () => [] },
    disponibilidades:      { type: Array,  default: () => [] },
    solicitacoesPendentes: { type: Number, default: 0 },
    usuario:               { type: Object, default: null },
})

const emit = defineEmits(['ir-passageiros', 'ir-solicitacoes'])

// ─── CHECKLIST DE ONBOARDING ──────────────────────────────────────────────────
const checklist = computed(() => [
    {
        label: 'Van cadastrada',
        done:  !!props.van,
        link:  props.van ? null : route('motorista.van.create'),
        grupo: 'van',
    },
    {
        label: 'Foto frontal da van',
        done:  !!props.van?.foto_url,
        link:  route('motorista.van.documentos'),
        grupo: 'van',
    },
    {
        label: 'CRLV (Registro do Veículo)',
        done:  !!props.van?.has_crlv,
        link:  route('motorista.van.documentos'),
        grupo: 'van',
    },
    {
        label: 'Seguro de passageiros',
        done:  !!props.van?.has_seguro,
        link:  route('motorista.van.documentos'),
        grupo: 'van',
    },
    {
        label: 'Autorização municipal',
        done:  !!props.van?.has_autorizacao,
        link:  route('motorista.van.documentos'),
        grupo: 'van',
    },
    {
        label: 'Arquivo da CNH',
        done:  !!props.motorista?.has_cnh_foto,
        link:  route('motorista.documentos.index'),
        grupo: 'pessoal',
    },
    {
        label: 'Certidão de antecedentes',
        done:  !!props.motorista?.has_certidao,
        link:  route('motorista.documentos.index'),
        grupo: 'pessoal',
    },
])

const totalDone    = computed(() => checklist.value.filter(i => i.done).length)
const totalItems   = computed(() => checklist.value.length)
const progressoPct = computed(() => Math.round((totalDone.value / totalItems.value) * 100))
const tudo_pronto  = computed(() => totalDone.value === totalItems.value)

// Exibe o checklist enquanto pendente OU há itens faltando
const mostrarChecklist = computed(() =>
    props.motorista?.status_aprovacao !== 'aprovado' ||
    props.van?.status_aprovacao !== 'aprovado' ||
    !tudo_pronto.value
)

const statusConfig = {
    pendente:  { label: 'Aguardando aprovação', classes: 'bg-amber-100 text-amber-800 border-amber-200' },
    aprovado:  { label: 'Aprovado',             classes: 'bg-emerald-100 text-emerald-700 border-emerald-200' },
    rejeitado: { label: 'Rejeitado',            classes: 'bg-red-100 text-red-700 border-red-200' },
}

// ─── VISIBILIDADE NO ANÚNCIO DE MOTORISTAS ───────────────────────────────────
const statusVisibilidade = computed(() => {
    if (props.motorista?.status_aprovacao !== 'aprovado') {
        return { visivel: false, motivo: 'Seu cadastro ainda está aguardando aprovação pelo administrador.', link: null, cta: null }
    }
    if (!props.van) {
        return { visivel: false, motivo: 'Cadastre sua van para aparecer na busca de motoristas.', link: route('motorista.van.create'), cta: 'Cadastrar van' }
    }
    if (props.van.status_aprovacao !== 'aprovado') {
        return { visivel: false, motivo: 'Sua van está aguardando aprovação pelo administrador.', link: null, cta: null }
    }
    if (!props.van.foto_url) {
        return { visivel: false, motivo: 'Adicione a foto frontal da van — ela é obrigatória para aparecer na busca.', link: route('motorista.van.documentos'), cta: 'Adicionar foto' }
    }
    const dispAtivas = props.disponibilidades.filter(d => d.ativa)
    if (dispAtivas.length === 0) {
        return { visivel: false, motivo: 'Crie ao menos uma disponibilidade ativa para os responsáveis encontrarem você.', link: null, cta: null }
    }
    return { visivel: true, dispAtivas }
})

const turnoLabel = { manha: 'Manhã', tarde: 'Tarde', integral: 'Integral' }
const diasLabel  = { seg: 'Seg', ter: 'Ter', qua: 'Qua', qui: 'Qui', sex: 'Sex', sab: 'Sáb', dom: 'Dom' }

function bairrosResumo(regioes) {
    return (regioes ?? []).flatMap((r) => r.bairros ?? [])
}
</script>

<template>
    <div class="space-y-4">

        <!-- Hero -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-700 to-amber-800 p-6 text-white shadow-lg">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_rgba(255,255,255,0.12),_transparent_60%)] pointer-events-none"></div>
            <div class="relative">
                <!-- Greeting + status + quick action -->
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div>
                        <p class="text-sm text-amber-200">Olá, {{ usuario?.nome?.split(' ')[0] ?? 'Motorista' }}</p>
                        <span class="inline-flex items-center text-xs px-2.5 py-1 mt-2 rounded-full border font-semibold"
                            :class="statusConfig[motorista?.status_aprovacao]?.classes ?? 'bg-white/20 text-white border-white/20'">
                            {{ statusConfig[motorista?.status_aprovacao]?.label ?? 'Sem status' }}
                        </span>
                    </div>
                    <Link v-if="motorista?.status_aprovacao === 'aprovado' && !van"
                        :href="route('motorista.van.create')"
                        class="shrink-0 inline-flex items-center gap-1.5 rounded-xl bg-white/15 border border-white/20 px-3 py-2 text-xs font-semibold text-white hover:bg-white/25 transition">
                        <PlusIcon class="w-3.5 h-3.5" />
                        Cadastrar van
                    </Link>
                </div>

                <!-- State-aware body -->
                <div v-if="motorista?.status_aprovacao === 'pendente'">
                    <p class="text-lg font-bold">Cadastro em análise</p>
                    <p class="text-sm text-amber-100 mt-1 max-w-sm">Seus dados estão sendo verificados pelo administrador.</p>
                </div>
                <div v-else-if="motorista?.status_aprovacao === 'rejeitado'">
                    <p class="text-lg font-bold">Cadastro não aprovado</p>
                    <p v-if="motorista?.motivo_rejeicao" class="text-sm text-amber-100 mt-1">{{ motorista.motivo_rejeicao }}</p>
                </div>
                <div v-else>
                    <p class="text-2xl font-bold">{{ passageiros.length }} passageiro{{ passageiros.length !== 1 ? 's' : '' }}</p>
                    <div class="flex flex-wrap items-center gap-x-2 mt-1.5 text-sm text-amber-100">
                        <span>{{ disponibilidades.filter(d => d.ativa).length }} trajeto{{ disponibilidades.filter(d => d.ativa).length !== 1 ? 's ativos' : ' ativo' }}</span>
                        <template v-if="solicitacoesPendentes > 0">
                            <span class="opacity-40">·</span>
                            <button @click="emit('ir-solicitacoes')"
                                class="font-semibold text-white underline decoration-white/40 hover:decoration-white/80 transition">
                                {{ solicitacoesPendentes }} solicitação{{ solicitacoesPendentes > 1 ? 'ões' : '' }} pendente{{ solicitacoesPendentes > 1 ? 's' : '' }}
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Card de visibilidade na busca de motoristas ──────────────────── -->
        <div v-if="statusVisibilidade.visivel"
            class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3">
            <EyeIcon class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-emerald-800">Visível para responsáveis</p>
                <p class="text-xs text-emerald-700 mt-0.5">
                    Sua van está aparecendo na busca de motoristas.
                    <span v-if="statusVisibilidade.dispAtivas?.length">
                        Disponibilidades ativas:
                        <span v-for="(d, i) in statusVisibilidade.dispAtivas" :key="d.id_disponibilidade">
                            <strong>{{ d.nome }}</strong> ({{ turnoLabel[d.turno] ?? d.turno }}){{ i < statusVisibilidade.dispAtivas.length - 1 ? ', ' : '.' }}
                        </span>
                    </span>
                </p>
            </div>
        </div>
        <div v-else-if="motorista?.status_aprovacao === 'aprovado'"
            class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
            <EyeSlashIcon class="w-4 h-4 text-slate-400 shrink-0 mt-0.5" />
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-slate-700">Não aparece na busca de motoristas</p>
                <p class="text-xs text-slate-500 mt-0.5">{{ statusVisibilidade.motivo }}</p>
            </div>
            <Link v-if="statusVisibilidade.link" :href="statusVisibilidade.link"
                class="shrink-0 flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 px-3 py-1.5 rounded-lg transition whitespace-nowrap">
                {{ statusVisibilidade.cta }}
            </Link>
        </div>

        <!-- Alerta: foto frontal ausente -->
        <div v-if="van && !van.foto_url"
            class="flex items-start gap-3 rounded-xl border border-amber-300 bg-amber-50 px-4 py-3">
            <ExclamationCircleIcon class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" />
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-amber-800">Adicione a foto da van</p>
                <p class="text-xs text-amber-700 mt-0.5">
                    A <strong>foto frontal</strong> é obrigatória para que sua van apareça no marketplace para os responsáveis.
                </p>
            </div>
            <Link :href="route('motorista.van.documentos')"
                class="shrink-0 flex items-center gap-1.5 text-xs font-bold text-amber-700 hover:text-amber-900 bg-white border border-amber-300 px-3 py-1.5 rounded-lg transition whitespace-nowrap">
                <CameraIcon class="w-3.5 h-3.5" />
                Adicionar
            </Link>
        </div>

        <!-- ── CHECKLIST DE ONBOARDING ────────────────────────────────────── -->
        <div v-if="mostrarChecklist"
            class="rounded-2xl border bg-white shadow-sm overflow-hidden"
            :class="tudo_pronto ? 'border-slate-200' : 'border-amber-200'">

            <!-- Cabeçalho -->
            <div class="flex items-center justify-between gap-3 px-5 py-4 border-b"
                :class="tudo_pronto ? 'border-slate-100 bg-slate-50' : 'border-amber-100 bg-amber-50/70'">
                <div class="flex items-center gap-2.5">
                    <ShieldCheckIcon class="w-5 h-5 shrink-0"
                        :class="tudo_pronto ? 'text-emerald-500' : 'text-amber-500'" />
                    <div>
                        <p class="text-sm font-bold text-slate-800">
                            {{ tudo_pronto ? 'Documentação completa' : 'Complete seu cadastro' }}
                        </p>
                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ tudo_pronto
                                ? 'Aguardando avaliação do administrador.'
                                : 'Você não aparece no marketplace até estar aprovado.' }}
                        </p>
                    </div>
                </div>
                <span class="text-xs font-bold px-2.5 py-1 rounded-full shrink-0"
                    :class="tudo_pronto
                        ? 'bg-emerald-100 text-emerald-700'
                        : 'bg-amber-100 text-amber-700'">
                    {{ totalDone }}/{{ totalItems }}
                </span>
            </div>

            <!-- Barra de progresso -->
            <div class="h-1 bg-slate-100">
                <div class="h-full transition-all duration-500"
                    :class="tudo_pronto ? 'bg-emerald-400' : 'bg-amber-400'"
                    :style="{ width: progressoPct + '%' }" />
            </div>

            <!-- Itens -->
            <ul class="divide-y divide-slate-50 px-5 py-1">
                <li v-for="item in checklist" :key="item.label"
                    class="flex items-center gap-3 py-2.5">
                    <CheckCircleIcon v-if="item.done"
                        class="w-4 h-4 text-emerald-500 shrink-0" />
                    <XCircleIcon v-else
                        class="w-4 h-4 text-slate-300 shrink-0" />
                    <span class="flex-1 text-sm"
                        :class="item.done ? 'text-slate-500 line-through decoration-slate-300' : 'text-slate-800'">
                        {{ item.label }}
                    </span>
                    <Link v-if="!item.done && item.link" :href="item.link"
                        class="flex items-center gap-0.5 text-xs font-semibold text-amber-700 hover:text-amber-900 shrink-0">
                        Enviar
                        <ArrowRightIcon class="w-3 h-3" />
                    </Link>
                </li>
            </ul>
        </div>

        <!-- Alerta solicitações pendentes -->
        <div v-if="solicitacoesPendentes > 0"
            class="flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            <div class="w-2 h-2 rounded-full bg-amber-400 animate-pulse shrink-0"></div>
            <span class="flex-1">
                {{ solicitacoesPendentes }} solicitação{{ solicitacoesPendentes > 1 ? 'ões' : '' }} aguardando resposta.
            </span>
            <button @click="emit('ir-solicitacoes')"
                class="text-xs font-bold text-amber-700 hover:text-amber-900 underline shrink-0">
                Ver →
            </button>
        </div>

        <!-- Van + CNH -->
        <div class="grid gap-4 sm:grid-cols-2">

            <!-- Van -->
            <div v-if="!van"
                class="flex flex-col items-center justify-center gap-3 rounded-2xl border-2 border-dashed border-amber-200 bg-white p-6 text-center">
                <TruckIcon class="w-10 h-10 text-amber-300" />
                <div>
                    <p class="text-sm font-semibold text-slate-800">Nenhuma van cadastrada</p>
                    <p class="text-xs text-slate-400 mt-0.5">Cadastre para começar a receber passageiros</p>
                </div>
                <Link :href="route('motorista.van.create')"
                    class="inline-flex items-center rounded-xl bg-amber-700 hover:bg-amber-800 text-white text-xs font-semibold px-4 py-2 transition shadow-sm">
                    Cadastrar van
                </Link>
            </div>

            <div v-else class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                            <TruckIcon class="w-5 h-5 text-amber-700" />
                        </div>
                        <div>
                            <p class="font-bold text-slate-900">{{ van.nome_servico || van.placa }}</p>
                            <p class="text-xs text-slate-400">{{ [van.nome_servico ? van.placa : null, van.marca, van.modelo, van.ano_fabricacao].filter(Boolean).join(' · ') }}</p>
                        </div>
                    </div>
                    <div class="flex flex-col items-end gap-1.5 shrink-0">
                        <span class="text-xs px-2.5 py-1 rounded-full border font-semibold"
                            :class="statusConfig[van.status_aprovacao]?.classes">
                            {{ statusConfig[van.status_aprovacao]?.label ?? van.status_aprovacao }}
                        </span>
                        <Link :href="route('motorista.van.edit')"
                            class="flex items-center gap-1 text-xs text-amber-700 hover:text-amber-800 transition">
                            <PencilSquareIcon class="w-3.5 h-3.5" /> Editar van
                        </Link>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div class="rounded-xl bg-slate-50 px-3 py-2">
                        <p class="text-slate-400">Capacidade</p>
                        <p class="font-semibold text-slate-800 mt-0.5">{{ van.capacidade_passageiros }} passageiros</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 px-3 py-2">
                        <p class="text-slate-400">Documentação</p>
                        <p class="font-semibold mt-0.5" :class="van.documentacao_completa ? 'text-emerald-600' : 'text-amber-700'">
                            {{ van.documentacao_completa ? 'Completa' : 'Pendente' }}
                        </p>
                    </div>
                </div>
                <p v-if="van.motivo_rejeicao" class="mt-3 text-xs text-red-600 bg-red-50 rounded-xl px-3 py-2">
                    Motivo: {{ van.motivo_rejeicao }}
                </p>
            </div>

            <!-- CNH -->
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-semibold text-slate-700 mb-3">CNH</p>
                <div class="grid grid-cols-3 gap-2 text-xs">
                    <div class="rounded-xl bg-slate-50 px-3 py-2">
                        <p class="text-slate-400">Número</p>
                        <p class="font-semibold text-slate-800 mt-0.5 truncate">{{ motorista?.cnh_numero ?? '—' }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 px-3 py-2">
                        <p class="text-slate-400">Categoria</p>
                        <p class="font-semibold text-slate-800 mt-0.5">{{ motorista?.cnh_categoria ?? '—' }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 px-3 py-2">
                        <p class="text-slate-400">Validade</p>
                        <p class="font-semibold text-slate-800 mt-0.5">
                            {{ motorista?.cnh_validade ? new Date(motorista.cnh_validade + 'T00:00:00').toLocaleDateString('pt-BR') : '—' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trajetos: empty state -->
        <div v-if="van && !disponibilidades.length"
            class="flex flex-col items-center justify-center gap-3 rounded-2xl border-2 border-dashed border-amber-200 bg-white p-6 text-center">
            <MapIcon class="w-10 h-10 text-amber-300" />
            <div>
                <p class="text-sm font-semibold text-slate-800">Nenhum trajeto cadastrado</p>
                <p class="text-xs text-slate-400 mt-0.5">Crie um trajeto para que responsáveis possam encontrar você</p>
            </div>
            <Link :href="route('motorista.disponibilidades.create')"
                class="inline-flex items-center gap-1 rounded-xl bg-amber-700 hover:bg-amber-800 text-white text-xs font-semibold px-4 py-2 transition shadow-sm">
                <PlusIcon class="w-3.5 h-3.5" /> Criar trajeto
            </Link>
        </div>

        <!-- Trajetos -->
        <div v-if="disponibilidades.length" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <MapIcon class="w-4 h-4 text-amber-600" />
                    <p class="text-sm font-semibold text-slate-800">Meus trajetos</p>
                </div>
                <Link v-if="van" :href="route('motorista.disponibilidades.create')"
                    class="flex items-center gap-1 text-xs font-semibold text-amber-700 hover:text-amber-800 transition">
                    <PlusIcon class="w-3.5 h-3.5" /> Novo
                </Link>
            </div>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                <div v-for="d in disponibilidades" :key="d.id_disponibilidade"
                    class="rounded-xl border p-3"
                    :class="d.ativa ? 'border-amber-200 bg-amber-50' : 'border-slate-200 bg-slate-50'">
                    <div class="flex items-start justify-between gap-2 mb-1">
                        <p class="font-semibold text-slate-900 text-sm">{{ d.nome }}</p>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <span class="rounded-full px-2 py-0.5 text-xs font-semibold"
                                :class="d.ativa
                                    ? 'bg-amber-100 text-amber-700'
                                    : 'border border-slate-200 bg-white text-slate-500'">
                                {{ d.ativa ? 'Ativo' : 'Inativo' }}
                            </span>
                            <Link :href="route('motorista.disponibilidades.edit', d.id_disponibilidade)"
                                class="w-6 h-6 rounded-lg bg-slate-100 hover:bg-amber-100 flex items-center justify-center transition">
                                <PencilSquareIcon class="w-3.5 h-3.5 text-slate-500 hover:text-amber-700" />
                            </Link>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 mb-2">{{ turnoLabel[d.turno] ?? d.turno }}</p>
                    <div class="flex flex-wrap gap-1 mb-2">
                        <span v-for="dia in d.dias" :key="dia"
                            class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700">
                            {{ diasLabel[dia] ?? dia }}
                        </span>
                    </div>
                    <div v-if="d.regioes_atendidas?.length || d.escolas_atendidas?.length" class="flex flex-wrap gap-1 mb-2">
                        <span v-for="b in bairrosResumo(d.regioes_atendidas).slice(0,3)" :key="'b'+b"
                            class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] text-slate-500">{{ b }}</span>
                        <span v-for="e in (d.escolas_atendidas ?? []).slice(0,2)" :key="'e'+e"
                            class="rounded-full bg-blue-50 px-2 py-0.5 text-[10px] text-blue-600">{{ e }}</span>
                    </div>
                    <p class="text-sm font-bold text-amber-700">
                        R$ {{ Number(d.preco_mensal).toFixed(2).replace('.', ',') }}
                        <span class="text-xs text-slate-400 font-normal">/mês</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Passageiros resumo -->
        <div v-if="passageiros.length" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <UserGroupIcon class="w-4 h-4 text-amber-600" />
                    <p class="text-sm font-semibold text-slate-800">Passageiros</p>
                </div>
                <button @click="emit('ir-passageiros')"
                    class="text-xs font-semibold text-amber-700 hover:text-amber-800 transition">
                    Ver todos →
                </button>
            </div>
            <div class="flex flex-wrap gap-2">
                <div v-for="p in passageiros.slice(0, 6)" :key="p.id_passageiro"
                    class="flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                    <div class="w-6 h-6 rounded-full bg-amber-700 flex items-center justify-center shrink-0">
                        <span class="text-white text-[10px] font-bold">{{ p.nome?.charAt(0)?.toUpperCase() }}</span>
                    </div>
                    <span class="text-sm text-slate-700">{{ p.nome }}</span>
                </div>
                <div v-if="passageiros.length > 6"
                    class="flex items-center rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                    <span class="text-sm text-slate-400">+{{ passageiros.length - 6 }} mais</span>
                </div>
            </div>
        </div>

    </div>
</template>
