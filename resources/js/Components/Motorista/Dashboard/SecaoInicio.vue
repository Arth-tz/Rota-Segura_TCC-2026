<script setup>
import { Link } from '@inertiajs/vue3'
import { TruckIcon, UserGroupIcon, MapIcon, PlusIcon, PencilSquareIcon } from '@heroicons/vue/24/outline'

defineProps({
    motorista:             { type: Object, default: null },
    van:                   { type: Object, default: null },
    passageiros:           { type: Array,  default: () => [] },
    disponibilidades:      { type: Array,  default: () => [] },
    solicitacoesPendentes: { type: Number, default: 0 },
})

const emit = defineEmits(['ir-passageiros', 'ir-solicitacoes'])

const statusConfig = {
    pendente:  { label: 'Aguardando aprovação', classes: 'bg-amber-100 text-amber-800 border-amber-200' },
    aprovado:  { label: 'Aprovado',             classes: 'bg-emerald-100 text-emerald-700 border-emerald-200' },
    rejeitado: { label: 'Rejeitado',            classes: 'bg-red-100 text-red-700 border-red-200' },
}

const turnoLabel = { manha: 'Manhã', tarde: 'Tarde', integral: 'Integral' }
const diasLabel  = { seg: 'Seg', ter: 'Ter', qua: 'Qua', qui: 'Qui', sex: 'Sex', sab: 'Sáb', dom: 'Dom' }
</script>

<template>
    <div class="space-y-4">

        <!-- Hero -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 p-6 text-white shadow-lg">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_rgba(255,255,255,0.12),_transparent_60%)] pointer-events-none"></div>
            <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
                <div>
                    <!-- Badge status motorista -->
                    <span class="inline-flex items-center text-xs px-2.5 py-1 rounded-full border font-semibold mb-3"
                        :class="statusConfig[motorista?.status_aprovacao]?.classes ?? 'bg-white/20 text-white border-white/20'">
                        {{ statusConfig[motorista?.status_aprovacao]?.label ?? 'Sem status' }}
                    </span>

                    <!-- Mensagem contextual -->
                    <div v-if="motorista?.status_aprovacao === 'pendente'">
                        <p class="font-bold text-lg" style="font-family:'Sora',sans-serif;">Cadastro em análise</p>
                        <p class="text-sm text-amber-100 mt-1 max-w-sm">Seus dados estão sendo verificados pelo administrador.</p>
                    </div>
                    <div v-else-if="motorista?.status_aprovacao === 'rejeitado'">
                        <p class="font-bold text-lg" style="font-family:'Sora',sans-serif;">Cadastro não aprovado</p>
                        <p v-if="motorista?.motivo_rejeicao" class="text-sm text-amber-100 mt-1">{{ motorista.motivo_rejeicao }}</p>
                    </div>
                    <div v-else class="flex items-center gap-6">
                        <div class="text-center">
                            <p class="text-3xl font-bold">{{ passageiros.length }}</p>
                            <p class="text-xs text-amber-200 mt-0.5">passageiros</p>
                        </div>
                        <div class="w-px h-10 bg-white/20"></div>
                        <div class="text-center">
                            <p class="text-3xl font-bold">{{ disponibilidades.filter(d => d.ativa).length }}</p>
                            <p class="text-xs text-amber-200 mt-0.5">trajetos ativos</p>
                        </div>
                        <template v-if="solicitacoesPendentes > 0">
                            <div class="w-px h-10 bg-white/20"></div>
                            <div class="text-center">
                                <p class="text-3xl font-bold">{{ solicitacoesPendentes }}</p>
                                <p class="text-xs text-amber-200 mt-0.5">solicitações</p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
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
                    class="inline-flex items-center rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2 transition shadow-sm"
                    style="font-family:'Sora',sans-serif;">
                    Cadastrar van
                </Link>
            </div>

            <div v-else class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                            <TruckIcon class="w-5 h-5 text-amber-600" />
                        </div>
                        <div>
                            <p class="font-bold text-slate-900">{{ van.placa }}</p>
                            <p class="text-xs text-slate-400">{{ [van.marca, van.modelo, van.ano_fabricacao].filter(Boolean).join(' · ') }}</p>
                        </div>
                    </div>
                    <span class="text-xs px-2.5 py-1 rounded-full border font-semibold shrink-0"
                        :class="statusConfig[van.status_aprovacao]?.classes">
                        {{ statusConfig[van.status_aprovacao]?.label ?? van.status_aprovacao }}
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div class="rounded-xl bg-slate-50 px-3 py-2">
                        <p class="text-slate-400">Capacidade</p>
                        <p class="font-semibold text-slate-800 mt-0.5">{{ van.capacidade_passageiros }} passageiros</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 px-3 py-2">
                        <p class="text-slate-400">Documentação</p>
                        <p class="font-semibold mt-0.5" :class="van.documentacao_completa ? 'text-emerald-600' : 'text-amber-600'">
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
                <p class="text-xs font-semibold text-amber-500 uppercase tracking-wide mb-3">CNH</p>
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
                class="inline-flex items-center gap-1 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2 transition shadow-sm"
                style="font-family:'Sora',sans-serif;">
                <PlusIcon class="w-3.5 h-3.5" /> Criar trajeto
            </Link>
        </div>

        <!-- Trajetos -->
        <div v-if="disponibilidades.length" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <MapIcon class="w-4 h-4 text-amber-500" />
                    <p class="text-sm font-semibold text-slate-800" style="font-family:'Sora',sans-serif;">Meus trajetos</p>
                </div>
                <Link v-if="van" :href="route('motorista.disponibilidades.create')"
                    class="flex items-center gap-1 text-xs font-semibold text-amber-600 hover:text-amber-700 transition">
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
                                :class="d.ativa ? 'bg-amber-100 text-amber-700' : 'bg-slate-200 text-slate-500'">
                                {{ d.ativa ? 'Ativo' : 'Inativo' }}
                            </span>
                            <Link :href="route('motorista.disponibilidades.edit', d.id_disponibilidade)"
                                class="w-6 h-6 rounded-lg bg-slate-100 hover:bg-amber-100 flex items-center justify-center transition">
                                <PencilSquareIcon class="w-3.5 h-3.5 text-slate-500 hover:text-amber-600" />
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
                    <div v-if="d.bairros_atendidos?.length || d.escolas_atendidas?.length" class="flex flex-wrap gap-1 mb-2">
                        <span v-for="b in (d.bairros_atendidos ?? []).slice(0,3)" :key="'b'+b"
                            class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] text-slate-500">{{ b }}</span>
                        <span v-for="e in (d.escolas_atendidas ?? []).slice(0,2)" :key="'e'+e"
                            class="rounded-full bg-blue-50 px-2 py-0.5 text-[11px] text-blue-600">{{ e }}</span>
                    </div>
                    <p class="text-sm font-bold text-amber-600">
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
                    <UserGroupIcon class="w-4 h-4 text-amber-500" />
                    <p class="text-sm font-semibold text-slate-800" style="font-family:'Sora',sans-serif;">Passageiros</p>
                </div>
                <button @click="emit('ir-passageiros')"
                    class="text-xs font-semibold text-amber-600 hover:text-amber-700 transition">
                    Ver todos →
                </button>
            </div>
            <div class="flex flex-wrap gap-2">
                <div v-for="p in passageiros.slice(0, 6)" :key="p.id_passageiro"
                    class="flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                    <div class="w-6 h-6 rounded-full bg-amber-500 flex items-center justify-center shrink-0">
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
