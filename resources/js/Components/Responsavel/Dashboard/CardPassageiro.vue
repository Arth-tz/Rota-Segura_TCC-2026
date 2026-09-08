<script setup>
import { ref, nextTick, computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import { XMarkIcon, ChevronDownIcon, ChevronUpIcon, PhoneIcon, TruckIcon, PencilSquareIcon, ArrowsRightLeftIcon } from '@heroicons/vue/24/outline'
import ConfirmModal from '@/Components/UI/ConfirmModal.vue'

const props = defineProps({
    passageiro: { type: Object, required: true },
    modo:       { type: String, default: 'inicio' },
})

const emit = defineEmits(['buscar-van'])

// ── Solicitações pendentes ────────────────────────────────────────────────
const mostrarSolicitacoes = ref(false)

// ── Confirmação de cancelamento (substitui confirm() nativo) ──────────────
const acaoConfirmar = ref(null) // { tipo, payload, mensagem }

function fecharConfirmar() {
    acaoConfirmar.value = null
}

function executarAcaoConfirmada() {
    const acao = acaoConfirmar.value
    if (!acao) return
    if (acao.tipo === 'cancelar' || acao.tipo === 'cancelarAlteracao') {
        useForm({}).delete(route('responsavel.solicitacoes.cancelar', acao.payload), {
            preserveScroll: true,
        })
    } else if (acao.tipo === 'cancelarFalta') {
        useForm({}).delete(route('responsavel.presenca.destroy', acao.payload.id_presenca), {
            preserveScroll: true,
        })
    }
    acaoConfirmar.value = null
}

function cancelar(idSolicitacao) {
    acaoConfirmar.value = { tipo: 'cancelar', payload: idSolicitacao, mensagem: 'Cancelar esta solicitação?' }
}

// ── Presença / falta ─────────────────────────────────────────────────────
const faltaAberta        = ref(false)
const diaSelecionado     = ref(null)
const formFalta          = useForm({ id_vinculo: '', data_falta: '', motivo_falta: '' })
const refFormFalta       = ref(null)

const MOTIVOS = {
    doenca:   'Doença',
    feriado:  'Feriado',
    viagem:   'Viagem',
    evento:   'Evento escolar',
    outro:    'Outro',
}

function abrirFalta(dia) {
    diaSelecionado.value      = dia
    formFalta.id_vinculo      = dia.id_vinculo
    formFalta.data_falta      = dia.data
    formFalta.motivo_falta    = ''
    faltaAberta.value         = true
    nextTick(() => refFormFalta.value?.scrollIntoView({ behavior: 'smooth', block: 'nearest' }))
}

function confirmarFalta() {
    formFalta.post(route('responsavel.presenca.store'), {
        preserveScroll: true,
        onSuccess: () => { faltaAberta.value = false },
    })
}

function cancelarFalta(dia) {
    acaoConfirmar.value = { tipo: 'cancelarFalta', payload: dia, mensagem: 'Cancelar a falta e marcar como presente?' }
}

function formatarData(data) {
    return new Date(data + 'T12:00:00').toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' })
}

// ── Status helpers ────────────────────────────────────────────────────────
const TURNOS   = { manha: 'Manhã', tarde: 'Tarde', integral: 'Integral' }
const DIAS_MAP = { seg: 'Seg', ter: 'Ter', qua: 'Qua', qui: 'Qui', sex: 'Sex', sab: 'Sáb', dom: 'Dom' }

// ── Alteração de dias ─────────────────────────────────────────────────
const alteracaoAberta     = ref(null) // id_disponibilidade sendo alterada
const formAlteracao       = useForm({ id_disponibilidade: null, dias_contratados: [], mensagem: '' })

const DIAS_ORDEM = ['seg','ter','qua','qui','sex','sab','dom']

function abrirAlteracao(disp) {
    alteracaoAberta.value           = disp.id_disponibilidade
    formAlteracao.id_disponibilidade = disp.id_disponibilidade
    formAlteracao.dias_contratados  = [...disp.dias_contratados]
    formAlteracao.mensagem          = ''
    formAlteracao.clearErrors()
}

function fecharAlteracao() {
    alteracaoAberta.value = null
}

function confirmarAlteracao(idVinculo) {
    formAlteracao.post(route('responsavel.vinculos.alterar', idVinculo), {
        preserveScroll: true,
        onSuccess: () => { alteracaoAberta.value = null },
    })
}

function cancelarAlteracao(idSolicitacao) {
    acaoConfirmar.value = { tipo: 'cancelarAlteracao', payload: idSolicitacao, mensagem: 'Cancelar a solicitação de alteração?' }
}

function whatsappUrl(telefone) {
    if (!telefone) return null
    const num = telefone.replace(/\D/g, '')
    const br = num.startsWith('55') ? num : '55' + num
    return `https://wa.me/${br}`
}

function statusClasses(status) {
    if (status === 'vinculo_ativo')        return 'bg-emerald-50 text-emerald-700 border-emerald-200'
    if (status === 'solicitacao_pendente') return 'bg-amber-50 text-amber-700 border-amber-200'
    return 'bg-slate-100 text-slate-500 border-slate-200'
}

function statusLabel(status) {
    if (status === 'vinculo_ativo')        return 'Van vinculada'
    if (status === 'solicitacao_pendente') return 'Aguardando'
    return 'Sem van'
}
</script>

<template>
    <!-- MODO INÍCIO — card completo -->
    <article v-if="modo === 'inicio'"
        class="min-w-0 bg-white border border-slate-200 rounded-2xl p-4 shadow-sm hover:shadow-md hover:border-blue-200 transition-all">

        <!-- Cabeçalho: avatar + nome + status + ações -->
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center shrink-0 overflow-hidden">
                    <img v-if="passageiro.foto_url" :src="passageiro.foto_url" class="h-full w-full object-cover" :alt="passageiro.nome">
                    <span v-else class="text-blue-600 font-bold text-sm">{{ passageiro.nome?.charAt(0)?.toUpperCase() }}</span>
                </div>
                <div class="min-w-0">
                    <p class="font-semibold text-slate-900 truncate text-sm">{{ passageiro.nome }}</p>
                    <span class="inline-flex items-center text-xs px-2 py-0.5 rounded-full border font-medium mt-0.5"
                        :class="statusClasses(passageiro.status)">
                        {{ statusLabel(passageiro.status) }}
                    </span>
                </div>
            </div>

            <div class="shrink-0 flex items-center gap-2">
                <Link v-if="passageiro.status === 'sem_van'"
                    :href="route('responsavel.marketplace')"
                    class="text-xs font-semibold text-blue-600 hover:text-blue-700 border border-blue-200 hover:bg-blue-50 px-3 py-1.5 rounded-lg transition">
                    Buscar van
                </Link>
                <Link :href="route('responsavel.passageiros.show', passageiro.id_passageiro)"
                    class="text-xs font-semibold text-slate-500 hover:text-blue-600 border border-slate-200 hover:border-blue-200 px-3 py-1.5 rounded-lg transition">
                    Detalhes
                </Link>
            </div>
        </div>

        <!-- Seção inferior (separada por borda) -->
        <div class="mt-3 pt-3 border-t border-slate-100 space-y-3">

            <!-- ── Vínculo ativo: próximos dias ─────────────────────────── -->
            <template v-if="passageiro.status === 'vinculo_ativo'">
                <div v-if="passageiro.proximos_dias?.length">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-2">Próximas aulas</p>
                    <div class="flex gap-1.5 flex-wrap">
                        <button
                            v-for="dia in passageiro.proximos_dias"
                            :key="dia.data"
                            @click="dia.vai ? abrirFalta(dia) : cancelarFalta(dia)"
                            class="flex flex-col items-center px-2.5 py-1.5 rounded-xl border text-xs font-medium transition-all"
                            :class="faltaAberta && diaSelecionado?.data === dia.data
                                ? 'bg-amber-100 text-amber-700 border-amber-400 ring-2 ring-amber-300 scale-105'
                                : dia.vai
                                ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100'
                                : 'bg-red-50 text-red-600 border-red-200 hover:bg-red-100'">
                            <span class="font-bold text-[10px]">{{ dia.dia_label }}</span>
                            <span class="text-[10px] opacity-70">{{ formatarData(dia.data) }}</span>
                            <span class="text-[10px] font-semibold mt-0.5">
                                {{ faltaAberta && diaSelecionado?.data === dia.data ? 'Selecionado' : dia.vai ? 'Vai' : 'Falta' }}
                            </span>
                        </button>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1.5">Clique num dia para marcar ou cancelar falta.</p>
                </div>

                <!-- Motoristas vinculados — um bloco por vínculo -->
                <template v-if="passageiro.motorista?.length">
                    <div v-for="m in passageiro.motorista" :key="m.id_vinculo"
                        class="rounded-xl border border-blue-100 bg-blue-50/60 p-3 space-y-2">
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Motorista</p>
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-7 h-7 rounded-full bg-blue-200 flex items-center justify-center shrink-0">
                                    <TruckIcon class="w-3.5 h-3.5 text-blue-700" />
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-800 truncate">
                                        {{ m.nome_servico || m.nome }}
                                    </p>
                                    <p class="text-xs text-slate-500 truncate">
                                        {{ m.van_modelo }}
                                        <span v-if="m.van_placa"> · {{ m.van_placa }}</span>
                                        <span v-if="m.van_cor"> · {{ m.van_cor }}</span>
                                    </p>
                                </div>
                            </div>
                            <a v-if="m.telefone"
                                :href="whatsappUrl(m.telefone)"
                                target="_blank" rel="noopener"
                                class="shrink-0 flex items-center gap-1 text-xs font-semibold text-emerald-700 bg-emerald-100 hover:bg-emerald-200 px-2.5 py-1.5 rounded-lg transition">
                                <PhoneIcon class="w-3.5 h-3.5" />
                                WhatsApp
                            </a>
                        </div>
                        <!-- Disponibilidades + botão alterar dias -->
                        <div v-if="m.disponibilidades?.length" class="space-y-2 pt-0.5">
                            <div v-for="d in m.disponibilidades" :key="d.id_disponibilidade"
                                class="rounded-lg border border-blue-100 bg-white px-3 py-2 space-y-1.5">

                                <!-- Linha principal da disponibilidade -->
                                <div class="flex items-center justify-between gap-2">
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-blue-700 truncate">{{ d.nome }} · {{ TURNOS[d.turno] ?? d.turno }}</p>
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            <span v-for="dia in (d.dias_contratados ?? [])" :key="dia"
                                                class="text-[10px] px-1.5 py-0.5 rounded bg-blue-100 text-blue-700 font-medium">
                                                {{ DIAS_MAP[dia] ?? dia }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Pendente ou botão alterar -->
                                    <div class="shrink-0">
                                        <template v-if="d.alteracao_pendente">
                                            <div class="flex items-center gap-1 text-xs text-amber-600 font-medium">
                                                <ArrowsRightLeftIcon class="w-3 h-3" />
                                                <span>Alteração pendente</span>
                                            </div>
                                            <button @click="cancelarAlteracao(d.alteracao_pendente.id_solicitacao)"
                                                class="mt-1 text-[10px] text-red-500 hover:text-red-700 underline block">
                                                Cancelar
                                            </button>
                                        </template>
                                        <button v-else
                                            @click="alteracaoAberta === d.id_disponibilidade ? fecharAlteracao() : abrirAlteracao(d)"
                                            class="flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-700 border border-blue-200 hover:bg-blue-50 px-2 py-1 rounded-lg transition">
                                            <PencilSquareIcon class="w-3 h-3" />
                                            Alterar dias
                                        </button>
                                    </div>
                                </div>

                                <!-- Form inline de alteração -->
                                <div v-if="alteracaoAberta === d.id_disponibilidade"
                                    class="rounded-lg bg-blue-50 border border-blue-200 p-3 space-y-2">
                                    <p class="text-xs font-semibold text-blue-800 uppercase tracking-wide">Novos dias para {{ d.nome }}</p>

                                    <div class="flex flex-wrap gap-1.5">
                                        <label v-for="dia in DIAS_ORDEM.filter(x => (d.dias_disponiveis ?? []).includes(x))"
                                            :key="dia"
                                            class="flex items-center gap-1 cursor-pointer select-none">
                                            <input type="checkbox" :value="dia" v-model="formAlteracao.dias_contratados"
                                                class="rounded border-blue-300 text-blue-600 focus:ring-blue-500" />
                                            <span class="text-xs font-medium"
                                                :class="formAlteracao.dias_contratados.includes(dia) ? 'text-blue-700' : 'text-slate-500'">
                                                {{ DIAS_MAP[dia] ?? dia }}
                                            </span>
                                        </label>
                                    </div>
                                    <p v-if="formAlteracao.errors.dias_contratados" class="text-xs text-red-600">
                                        {{ formAlteracao.errors.dias_contratados }}
                                    </p>

                                    <textarea v-model="formAlteracao.mensagem" rows="2"
                                        placeholder="Motivo da alteração (opcional)…"
                                        class="w-full rounded-lg border border-blue-200 bg-white px-3 py-1.5 text-xs outline-none focus:border-blue-400 resize-none" />
                                    <p v-if="formAlteracao.errors.mensagem" class="text-xs text-red-600">
                                        {{ formAlteracao.errors.mensagem }}
                                    </p>

                                    <div class="flex gap-2">
                                        <button @click="fecharAlteracao"
                                            class="flex-1 py-1.5 rounded-lg border border-slate-200 text-xs text-slate-600 hover:bg-slate-50 transition">
                                            Cancelar
                                        </button>
                                        <button @click="confirmarAlteracao(d.id_vinculo)"
                                            :disabled="formAlteracao.processing || formAlteracao.dias_contratados.length === 0"
                                            class="flex-1 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white text-xs font-semibold transition">
                                            {{ formAlteracao.processing ? '…' : 'Solicitar alteração' }}
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </template>

                <!-- Form inline de marcar falta -->
                <div v-if="faltaAberta" ref="refFormFalta" class="rounded-xl bg-red-100 border border-red-300 p-3 space-y-2">
                    <p class="text-xs font-semibold text-red-800">
                        Marcar falta — {{ diaSelecionado?.dia_label }}, {{ formatarData(diaSelecionado?.data) }}
                    </p>
                    <select v-model="formFalta.motivo_falta"
                        class="w-full rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs outline-none focus:border-red-400 transition">
                        <option value="">Sem motivo</option>
                        <option v-for="(label, val) in MOTIVOS" :key="val" :value="val">{{ label }}</option>
                    </select>
                    <p v-if="formFalta.errors.data_falta" class="text-xs text-red-600">{{ formFalta.errors.data_falta }}</p>
                    <div class="flex gap-2">
                        <button @click="faltaAberta = false"
                            class="flex-1 py-1.5 rounded-lg border border-slate-200 text-xs text-slate-600 hover:bg-slate-50 transition">
                            Cancelar
                        </button>
                        <button @click="confirmarFalta" :disabled="formFalta.processing"
                            class="flex-1 py-1.5 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-semibold transition">
                            {{ formFalta.processing ? '…' : 'Confirmar falta' }}
                        </button>
                    </div>
                </div>
            </template>

            <!-- ── Solicitação pendente ───────────────────────────────────── -->
            <template v-else-if="passageiro.status === 'solicitacao_pendente'">
                <button @click="mostrarSolicitacoes = !mostrarSolicitacoes"
                    class="w-full flex items-center justify-between text-xs text-amber-700 font-medium hover:text-amber-800 transition">
                    <span class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                        {{ passageiro.solicitacoes_pendentes.length }} solicitação{{ passageiro.solicitacoes_pendentes.length > 1 ? 'ões' : '' }} aguardando aprovação
                    </span>
                    <component :is="mostrarSolicitacoes ? ChevronUpIcon : ChevronDownIcon" class="w-3.5 h-3.5" />
                </button>

                <div v-if="mostrarSolicitacoes" class="space-y-2">
                    <div v-for="s in passageiro.solicitacoes_pendentes" :key="s.id_solicitacao"
                        class="rounded-xl border border-amber-100 bg-amber-50 px-3 py-2.5">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <div v-for="d in s.disponibilidades" :key="d.nome" class="mb-1 last:mb-0">
                                    <p class="text-xs font-semibold text-slate-800 truncate">{{ d.nome }}</p>
                                    <p class="text-xs text-slate-500">
                                        {{ TURNOS[d.turno] ?? d.turno }}
                                        <span v-if="d.dias_contratados?.length"> ·
                                            {{ d.dias_contratados.map(dia => DIAS_MAP[dia] ?? dia).join(', ') }}
                                        </span>
                                    </p>
                                </div>
                                <p class="text-xs text-amber-600 mt-1">Enviada em {{ s.data_solicitacao }}</p>
                            </div>
                            <button @click="cancelar(s.id_solicitacao)"
                                class="shrink-0 flex items-center gap-1 text-xs font-semibold text-red-600 hover:text-red-700 border border-red-200 hover:bg-red-50 px-2 py-1 rounded-lg transition">
                                <XMarkIcon class="w-3 h-3" /> Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            <!-- ── Sem van ────────────────────────────────────────────────── -->
            <p v-else class="text-xs text-slate-400">Nenhuma van vinculada ainda.</p>

        </div>
    </article>

    <!-- MODO LISTA — linha compacta -->
    <article v-else
        class="min-w-0 bg-white border border-slate-200 rounded-xl px-4 py-3 shadow-sm hover:shadow-md hover:border-blue-200 transition-all">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <div class="h-9 w-9 rounded-full bg-blue-100 flex items-center justify-center shrink-0 overflow-hidden">
                    <img v-if="passageiro.foto_url" :src="passageiro.foto_url" class="h-full w-full object-cover" :alt="passageiro.nome">
                    <span v-else class="text-blue-600 font-bold text-xs">{{ passageiro.nome?.charAt(0)?.toUpperCase() }}</span>
                </div>
                <div class="min-w-0">
                    <p class="font-semibold text-slate-900 truncate text-sm">{{ passageiro.nome }}</p>
                    <span class="inline-flex text-xs px-2 py-0.5 rounded-full border font-medium"
                        :class="statusClasses(passageiro.status)">
                        {{ statusLabel(passageiro.status) }}
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <Link v-if="passageiro.status === 'sem_van'"
                    :href="route('responsavel.marketplace')"
                    class="text-xs font-semibold text-blue-600 hover:text-blue-700 border border-blue-200 hover:bg-blue-50 px-3 py-1.5 rounded-lg transition">
                    Buscar van
                </Link>
                <Link :href="route('responsavel.passageiros.show', passageiro.id_passageiro)"
                    class="text-xs font-semibold text-slate-500 hover:text-blue-600 border border-slate-200 hover:border-blue-200 px-3 py-1.5 rounded-lg transition">
                    Detalhes
                </Link>
            </div>
        </div>
    </article>

    <ConfirmModal
        :show="!!acaoConfirmar"
        title="Confirmar cancelamento"
        :message="acaoConfirmar?.mensagem"
        confirm-label="Sim, cancelar"
        cancel-label="Voltar"
        variant="danger"
        @confirm="executarAcaoConfirmada"
        @close="fecharConfirmar" />
</template>
