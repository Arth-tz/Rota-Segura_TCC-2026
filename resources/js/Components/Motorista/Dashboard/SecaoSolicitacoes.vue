<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import {
    CheckCircleIcon,
    XCircleIcon,
    UserIcon,
    CalendarDaysIcon,
    CurrencyDollarIcon,
    ChatBubbleLeftEllipsisIcon,
    MapPinIcon,
    ExclamationCircleIcon,
    PhoneIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
    solicitacoes: { type: Array, default: () => [] },
})

const TURNOS    = { manha: 'Manhã', tarde: 'Tarde', integral: 'Integral' }
const DIAS_MAP  = { seg: 'Seg', ter: 'Ter', qua: 'Qua', qui: 'Qui', sex: 'Sex', sab: 'Sáb', dom: 'Dom' }
const TIPO_END  = { embarque: 'Embarque', desembarque: 'Escola / Destino', residencia: 'Residência' }

function formatarPreco(v) {
    return Number(v).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
}

// ── Aceite ────────────────────────────────────────────────────────────────
function aceitar(id) {
    if (!confirm('Aceitar esta solicitação e criar o vínculo?')) return
    useForm({}).post(route('motorista.solicitacoes.aceitar', id), {
        preserveScroll: true,
    })
}

// ── Recusa ────────────────────────────────────────────────────────────────
const modalRecusaAberto    = ref(false)
const solicitacaoRecusando = ref(null)
const formRecusa           = useForm({ motivo_recusa: '' })

function abrirRecusa(s) {
    solicitacaoRecusando.value = s
    formRecusa.motivo_recusa   = ''
    modalRecusaAberto.value    = true
}

function confirmarRecusa() {
    formRecusa.post(route('motorista.solicitacoes.recusar', solicitacaoRecusando.value.id_solicitacao), {
        preserveScroll: true,
        onSuccess: () => { modalRecusaAberto.value = false },
    })
}
</script>

<template>
    <div class="space-y-4">

        <!-- Header -->
        <div class="rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 p-5 text-white shadow-lg">
            <h3 class="text-lg font-bold" style="font-family:'Sora',sans-serif;">Solicitações pendentes</h3>
            <p class="text-sm text-amber-100 mt-1">
                {{ solicitacoes.length === 0
                    ? 'Nenhuma solicitação no momento.'
                    : `${solicitacoes.length} solicitação${solicitacoes.length > 1 ? 'ões' : ''} aguardando sua resposta.` }}
            </p>
        </div>

        <!-- Empty state -->
        <div v-if="solicitacoes.length === 0"
            class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-amber-200 bg-amber-50/40 py-14 px-6 text-center">
            <CalendarDaysIcon class="w-10 h-10 text-amber-300 mb-3" />
            <p class="font-semibold text-slate-700" style="font-family:'Sora',sans-serif;">Tudo em dia!</p>
            <p class="mt-1 text-sm text-slate-400">Quando responsáveis solicitarem vagas, elas aparecerão aqui.</p>
        </div>

        <!-- Cards de solicitação -->
        <div v-for="s in solicitacoes" :key="s.id_solicitacao"
            class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

            <!-- Cabeçalho: passageiro -->
            <div class="flex items-center gap-3 px-5 py-4 bg-slate-50 border-b border-slate-100">
                <div class="w-10 h-10 rounded-full bg-amber-500 flex items-center justify-center shrink-0">
                    <span class="text-white font-bold text-sm">{{ s.passageiro.nome?.charAt(0)?.toUpperCase() ?? '?' }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-slate-900 text-sm" style="font-family:'Sora',sans-serif;">
                        {{ s.passageiro.nome ?? 'Passageiro' }}
                    </p>
                    <p class="text-xs text-slate-400">Solicitação enviada em {{ s.data_solicitacao }}</p>
                </div>
                <div class="text-right shrink-0">
                    <p class="text-lg font-bold text-emerald-700">{{ formatarPreco(s.preco_total) }}</p>
                    <p class="text-xs text-slate-400">/mês total</p>
                </div>
            </div>

            <!-- Info do passageiro: endereços + obs. médicas + contato -->
            <div class="px-5 py-3 space-y-2 border-b border-slate-100">

                <!-- Endereços -->
                <div v-if="s.passageiro.enderecos?.length">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1.5">Endereços</p>
                    <div class="space-y-1.5">
                        <div v-for="e in s.passageiro.enderecos" :key="e.tipo + e.resumo"
                            class="flex items-start gap-2">
                            <MapPinIcon class="w-3.5 h-3.5 shrink-0 mt-0.5"
                                :class="e.tipo === 'embarque' ? 'text-emerald-500' : e.tipo === 'desembarque' ? 'text-blue-500' : 'text-slate-400'" />
                            <div class="min-w-0">
                                <span class="text-xs font-semibold"
                                    :class="e.tipo === 'embarque' ? 'text-emerald-700' : e.tipo === 'desembarque' ? 'text-blue-700' : 'text-slate-500'">
                                    {{ TIPO_END[e.tipo] ?? e.tipo }}
                                </span>
                                <span class="text-xs text-slate-500 ml-1">{{ e.resumo }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Obs. médicas -->
                <div v-if="s.passageiro.observacoes_medicas"
                    class="flex items-start gap-2 rounded-lg bg-red-50 border border-red-100 px-3 py-2">
                    <ExclamationCircleIcon class="w-3.5 h-3.5 text-red-400 shrink-0 mt-0.5" />
                    <p class="text-xs text-red-700">
                        <span class="font-semibold">Obs. médicas: </span>{{ s.passageiro.observacoes_medicas }}
                    </p>
                </div>

                <!-- Contato do responsável -->
                <div v-if="s.responsavel?.telefone" class="flex items-center gap-2">
                    <PhoneIcon class="w-3.5 h-3.5 text-slate-400 shrink-0" />
                    <p class="text-xs text-slate-600">
                        <span class="font-semibold">Responsável:</span>
                        {{ s.responsavel.nome }} · {{ s.responsavel.telefone }}
                    </p>
                </div>
                <div v-else-if="s.responsavel?.nome" class="flex items-center gap-2">
                    <UserIcon class="w-3.5 h-3.5 text-slate-400 shrink-0" />
                    <p class="text-xs text-slate-600">
                        <span class="font-semibold">Responsável:</span> {{ s.responsavel.nome }}
                    </p>
                </div>
            </div>

            <!-- Disponibilidades solicitadas -->
            <div class="px-5 py-4 space-y-3">
                <div v-for="d in s.disponibilidades" :key="d.nome"
                    class="rounded-xl bg-amber-50 border border-amber-100 px-4 py-3">
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <p class="text-sm font-semibold text-slate-800">{{ d.nome }}</p>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 border border-amber-200 font-semibold shrink-0">
                            {{ TURNOS[d.turno] ?? d.turno }}
                        </span>
                    </div>

                    <!-- Dias contratados -->
                    <div class="flex flex-wrap gap-1 mb-2">
                        <span v-for="dia in d.dias_contratados" :key="dia"
                            class="text-xs px-2 py-0.5 rounded-md bg-amber-500 text-white font-medium">
                            {{ DIAS_MAP[dia] ?? dia }}
                        </span>
                        <span v-if="!d.dias_contratados?.length" class="text-xs text-slate-400 italic">Dias não especificados</span>
                    </div>

                    <p class="text-sm font-bold text-emerald-700">
                        {{ formatarPreco(d.preco_mensal) }}
                        <span class="text-xs font-normal text-slate-400">/mês</span>
                    </p>
                </div>

                <!-- Mensagem do responsável -->
                <div v-if="s.mensagem" class="flex items-start gap-2 rounded-xl bg-blue-50 border border-blue-100 px-4 py-3">
                    <ChatBubbleLeftEllipsisIcon class="w-4 h-4 text-blue-400 shrink-0 mt-0.5" />
                    <p class="text-xs text-slate-600 italic">{{ s.mensagem }}</p>
                </div>
            </div>

            <!-- Ações -->
            <div class="px-5 pb-5 flex gap-3">
                <button @click="abrirRecusa(s)"
                    class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-xl border border-red-200 text-red-600 text-sm font-semibold hover:bg-red-50 transition">
                    <XCircleIcon class="w-4 h-4" />
                    Recusar
                </button>
                <button @click="aceitar(s.id_solicitacao)"
                    class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold transition shadow-sm"
                    style="font-family:'Sora',sans-serif;">
                    <CheckCircleIcon class="w-4 h-4" />
                    Aceitar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de recusa -->
    <Teleport to="body">
        <Transition name="fade">
            <div v-if="modalRecusaAberto"
                class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
                @click.self="modalRecusaAberto = false">
                <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl border border-slate-100">

                    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                        <div>
                            <p class="text-xs text-slate-400 uppercase tracking-widest">Recusar solicitação</p>
                            <h3 class="font-bold text-slate-900 mt-0.5" style="font-family:'Sora',sans-serif;">
                                {{ solicitacaoRecusando?.passageiro?.nome }}
                            </h3>
                        </div>
                        <button @click="modalRecusaAberto = false"
                            class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition">
                            <XCircleIcon class="w-4 h-4 text-slate-500" />
                        </button>
                    </div>

                    <div class="px-5 py-4">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">
                            Motivo da recusa <span class="normal-case font-normal text-slate-400">(opcional)</span>
                        </label>
                        <textarea v-model="formRecusa.motivo_recusa" rows="3"
                            placeholder="Ex: Não atendo esse bairro / Vagas esgotadas para o turno…"
                            class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-red-300 focus:ring-2 focus:ring-red-100 transition resize-none" />
                        <p v-if="formRecusa.errors.motivo_recusa" class="mt-1 text-xs text-red-600">
                            {{ formRecusa.errors.motivo_recusa }}
                        </p>
                    </div>

                    <div class="px-5 pb-5 flex gap-3">
                        <button @click="modalRecusaAberto = false"
                            class="flex-1 py-3 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                            Voltar
                        </button>
                        <button @click="confirmarRecusa"
                            :disabled="formRecusa.processing"
                            class="flex-1 py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-bold transition shadow-sm"
                            style="font-family:'Sora',sans-serif;">
                            {{ formRecusa.processing ? 'Recusando…' : 'Confirmar recusa' }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
