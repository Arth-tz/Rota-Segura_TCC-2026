<script setup>
import { computed, ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { UsersIcon, CheckCircleIcon, XCircleIcon, XMarkIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    passageiros: { type: Array, default: () => [] },
})

const MOTIVOS = {
    doenca:  'Doença',
    feriado: 'Feriado',
    viagem:  'Viagem',
    evento:  'Evento escolar',
    outro:   'Outro',
}

const hojeLabel = new Date().toLocaleDateString('pt-BR', { weekday: 'long', day: '2-digit', month: '2-digit' })

const vaoHoje   = computed(() => props.passageiros.filter(p => !p.falta_hoje))
const faltaHoje = computed(() => props.passageiros.filter(p => p.falta_hoje))

// ── Encerrar vínculo ─────────────────────────────────────────────────────
const modalEncerrar     = ref(false)
const passageiroAlvo    = ref(null)
const formEncerrar      = useForm({ motivo_encerramento: '' })

function abrirEncerrar(p) {
    passageiroAlvo.value            = p
    formEncerrar.motivo_encerramento = ''
    modalEncerrar.value             = true
}

function confirmarEncerrar() {
    formEncerrar.post(route('motorista.vinculos.encerrar', passageiroAlvo.value.id_vinculo), {
        preserveScroll: true,
        onSuccess: () => { modalEncerrar.value = false },
    })
}
</script>

<template>
    <div class="space-y-4">

        <!-- Header -->
        <div class="rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 p-5 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold" style="font-family:'Sora',sans-serif;">Meus passageiros</h3>
                    <p class="text-xs text-amber-200 mt-0.5 capitalize">{{ hojeLabel }}</p>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold">{{ passageiros.length }}</p>
                    <p class="text-xs text-amber-200">vinculado{{ passageiros.length !== 1 ? 's' : '' }}</p>
                </div>
            </div>

            <!-- Resumo do dia -->
            <div v-if="passageiros.length" class="mt-4 grid grid-cols-2 gap-3">
                <div class="rounded-xl bg-white/15 px-4 py-2.5 flex items-center gap-2">
                    <CheckCircleIcon class="w-4 h-4 text-emerald-300 shrink-0" />
                    <div>
                        <p class="text-xl font-bold">{{ vaoHoje.length }}</p>
                        <p class="text-xs text-amber-200">vão hoje</p>
                    </div>
                </div>
                <div class="rounded-xl bg-white/15 px-4 py-2.5 flex items-center gap-2">
                    <XCircleIcon class="w-4 h-4 text-red-300 shrink-0" />
                    <div>
                        <p class="text-xl font-bold">{{ faltaHoje.length }}</p>
                        <p class="text-xs text-amber-200">falta hoje</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty state -->
        <div v-if="!passageiros.length"
            class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-amber-200 bg-amber-50/50 py-14 px-6 text-center">
            <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center mb-4">
                <UsersIcon class="w-6 h-6 text-amber-500" />
            </div>
            <p class="font-semibold text-slate-800" style="font-family:'Sora',sans-serif;">Nenhum passageiro vinculado</p>
            <p class="mt-1 text-sm text-slate-500 max-w-xs">Quando responsáveis solicitarem vagas e você aceitar, os passageiros aparecerão aqui.</p>
        </div>

        <!-- Lista -->
        <div v-else class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="p in passageiros" :key="p.id_passageiro"
                class="rounded-2xl border bg-white p-4 shadow-sm transition-all"
                :class="p.falta_hoje
                    ? 'border-red-200 hover:border-red-300'
                    : 'border-slate-200 hover:border-amber-200 hover:shadow-md'">

                <!-- Avatar + nome + badge -->
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                        :class="p.falta_hoje ? 'bg-red-100' : 'bg-amber-100'">
                        <span class="font-bold text-sm"
                            :class="p.falta_hoje ? 'text-red-500' : 'text-amber-700'">
                            {{ p.nome?.charAt(0)?.toUpperCase() }}
                        </span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold text-slate-900 truncate text-sm">{{ p.nome }}</p>
                        <p class="text-xs text-slate-400">
                            Desde {{ p.data_inicio ? new Date(p.data_inicio + 'T00:00:00').toLocaleDateString('pt-BR') : '—' }}
                        </p>
                    </div>
                    <!-- Badge status hoje -->
                    <span class="shrink-0 flex items-center gap-1 text-xs px-2 py-0.5 rounded-full font-semibold border"
                        :class="p.falta_hoje
                            ? 'bg-red-50 text-red-600 border-red-200'
                            : 'bg-emerald-50 text-emerald-700 border-emerald-200'">
                        <component :is="p.falta_hoje ? XCircleIcon : CheckCircleIcon" class="w-3 h-3" />
                        {{ p.falta_hoje ? 'Falta' : 'Vai' }}
                    </span>
                </div>

                <!-- Motivo da falta -->
                <div v-if="p.falta_hoje && p.motivo_falta"
                    class="rounded-xl bg-red-50 border border-red-100 px-3 py-2 text-xs text-red-700 mb-2">
                    {{ MOTIVOS[p.motivo_falta] ?? p.motivo_falta }}
                </div>

                <!-- Mensalidade + encerrar -->
                <div class="flex items-center gap-2">
                    <div class="flex-1 rounded-xl bg-slate-50 px-3 py-2 text-xs">
                        <p class="text-slate-400">Mensalidade</p>
                        <p class="font-bold text-amber-600 mt-0.5">R$ {{ Number(p.preco_total).toFixed(2).replace('.', ',') }}</p>
                    </div>
                    <button @click="abrirEncerrar(p)"
                        class="shrink-0 flex items-center gap-1 text-xs font-semibold text-red-500 hover:text-red-700 border border-red-200 hover:bg-red-50 px-2.5 py-1.5 rounded-xl transition">
                        <XMarkIcon class="w-3.5 h-3.5" />
                        Encerrar
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal encerrar vínculo -->
    <Teleport to="body">
        <Transition name="fade">
            <div v-if="modalEncerrar"
                class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
                @click.self="modalEncerrar = false">
                <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl border border-slate-100">

                    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                        <div>
                            <p class="text-xs text-slate-400 uppercase tracking-widest">Encerrar vínculo</p>
                            <h3 class="font-bold text-slate-900 mt-0.5" style="font-family:'Sora',sans-serif;">
                                {{ passageiroAlvo?.nome }}
                            </h3>
                        </div>
                        <button @click="modalEncerrar = false"
                            class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition">
                            <XMarkIcon class="w-4 h-4 text-slate-500" />
                        </button>
                    </div>

                    <div class="px-5 py-4 space-y-3">
                        <div class="rounded-xl bg-red-50 border border-red-100 px-4 py-3">
                            <p class="text-sm font-semibold text-red-800">Atenção: ação irreversível</p>
                            <p class="text-xs text-red-700 mt-1">O passageiro será desvinculado da van imediatamente. O responsável precisará fazer uma nova solicitação para voltar.</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">
                                Motivo <span class="normal-case font-normal text-slate-400">(opcional)</span>
                            </label>
                            <textarea v-model="formEncerrar.motivo_encerramento" rows="3"
                                placeholder="Ex: Rota alterada, passageiro mudou de escola…"
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-red-300 focus:ring-2 focus:ring-red-100 transition resize-none" />
                        </div>
                    </div>

                    <div class="px-5 pb-5 flex gap-3">
                        <button @click="modalEncerrar = false"
                            class="flex-1 py-3 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                            Cancelar
                        </button>
                        <button @click="confirmarEncerrar" :disabled="formEncerrar.processing"
                            class="flex-1 py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-bold transition shadow-sm"
                            style="font-family:'Sora',sans-serif;">
                            {{ formEncerrar.processing ? 'Encerrando…' : 'Confirmar encerramento' }}
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
