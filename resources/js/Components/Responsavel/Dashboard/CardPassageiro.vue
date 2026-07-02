<script setup>
import { ref, nextTick } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import { XMarkIcon, ChevronDownIcon, ChevronUpIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    passageiro: { type: Object, required: true },
    modo:       { type: String, default: 'inicio' },
})

const emit = defineEmits(['buscar-van'])

// ── Solicitações pendentes ────────────────────────────────────────────────
const mostrarSolicitacoes = ref(false)

function cancelar(idSolicitacao) {
    if (!confirm('Cancelar esta solicitação?')) return
    useForm({}).delete(route('responsavel.solicitacoes.cancelar', idSolicitacao), {
        preserveScroll: true,
    })
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
    if (!confirm('Cancelar a falta e marcar como presente?')) return
    useForm({}).delete(route('responsavel.presenca.destroy', dia.id_presenca), {
        preserveScroll: true,
    })
}

function formatarData(data) {
    return new Date(data + 'T12:00:00').toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' })
}

// ── Status helpers ────────────────────────────────────────────────────────
const TURNOS   = { manha: 'Manhã', tarde: 'Tarde', integral: 'Integral' }
const DIAS_MAP = { seg: 'Seg', ter: 'Ter', qua: 'Qua', qui: 'Qui', sex: 'Sex', sab: 'Sáb', dom: 'Dom' }

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
        class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm hover:shadow-md hover:border-blue-200 transition-all">

        <!-- Cabeçalho: avatar + nome + status + ações -->
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center shrink-0 overflow-hidden">
                    <img v-if="passageiro.foto_url" :src="passageiro.foto_url" class="h-full w-full object-cover" :alt="passageiro.nome">
                    <span v-else class="text-blue-600 font-bold text-sm">{{ passageiro.nome?.charAt(0)?.toUpperCase() }}</span>
                </div>
                <div class="min-w-0">
                    <p class="font-semibold text-slate-900 truncate text-sm" style="font-family:'Sora',sans-serif;">{{ passageiro.nome }}</p>
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
                            <span class="font-bold text-[11px]">{{ dia.dia_label }}</span>
                            <span class="text-[10px] opacity-70">{{ formatarData(dia.data) }}</span>
                            <span class="text-[10px] font-semibold mt-0.5">
                                {{ faltaAberta && diaSelecionado?.data === dia.data ? 'Selecionado' : dia.vai ? 'Vai' : 'Falta' }}
                            </span>
                        </button>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1.5">Clique num dia para marcar ou cancelar falta.</p>
                </div>

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
        class="bg-white border border-slate-200 rounded-xl px-4 py-3 shadow-sm hover:shadow-md hover:border-blue-200 transition-all">
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
</template>
