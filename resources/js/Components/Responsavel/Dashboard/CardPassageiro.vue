<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    passageiro: { type: Object, required: true },
    modo:       { type: String, default: 'inicio' },
})

const emit = defineEmits(['buscar-van'])

function statusClasses(color) {
    if (color === 'green') return 'bg-emerald-50 text-emerald-700 border-emerald-200'
    if (color === 'amber') return 'bg-amber-50 text-amber-700 border-amber-200'
    return 'bg-slate-50 text-slate-600 border-slate-200'
}

function statusIcone(status) {
    if (status === 'vinculo_ativo')        return '✅'
    if (status === 'solicitacao_pendente') return '⏳'
    return '🔍'
}
</script>

<template>

    <!-- MODO INÍCIO — card completo -->
    <article v-if="modo === 'inicio'" class="bg-white/95 border border-slate-200/80 rounded-2xl p-5 shadow-xl hover:shadow-2xl transition-shadow">
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 overflow-hidden">
                    <img v-if="passageiro.foto_url" :src="passageiro.foto_url" class="h-full w-full object-cover" :alt="passageiro.nome">
                    <span v-else class="text-blue-600 font-bold text-lg">{{ passageiro.nome?.charAt(0) }}</span>
                </div>
                <div class="min-w-0">
                    <p class="font-semibold text-slate-900 truncate" style="font-family:'Sora',sans-serif;">{{ passageiro.nome }}</p>
                    <p class="text-xs text-slate-400">Passageiro #{{ passageiro.id_passageiro }}</p>
                </div>
            </div>
            <span class="flex items-center gap-1 text-xs px-2.5 py-1 rounded-full border font-medium flex-shrink-0" :class="statusClasses(passageiro.status_color)">
                {{ statusIcone(passageiro.status) }} {{ passageiro.status_label }}
            </span>
        </div>

        <div class="mt-4">
            <div v-if="passageiro.status === 'vinculo_ativo'" class="bg-emerald-50 rounded-xl p-3 text-sm text-emerald-700">
                Van vinculada. Acompanhamento disponível quando a rota estiver ativa.
            </div>
            <div v-else-if="passageiro.status === 'solicitacao_pendente'" class="bg-amber-50 rounded-xl p-3 text-sm text-amber-700">
                Solicitação enviada. Aguardando aprovação do motorista.
            </div>
            <div v-else class="flex items-center justify-between">
                <p class="text-sm text-slate-500">Nenhuma van vinculada.</p>
                <button @click="emit('buscar-van')" class="text-sm font-semibold text-blue-600 hover:text-blue-700 transition">
                    Buscar van →
                </button>
            </div>
        </div>

        <div class="mt-4 pt-3 border-t border-slate-100 flex justify-end">
            <Link
                :href="route('responsavel.passageiros.show', passageiro.id_passageiro)"
                class="text-xs font-semibold text-slate-500 hover:text-blue-600 transition"
            >
                Ver detalhes →
            </Link>
        </div>
    </article>

    <!-- MODO LISTA — card compacto -->
    <article v-else class="bg-white/95 border border-slate-200/80 rounded-xl p-4 shadow-xl hover:shadow-2xl transition-shadow">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 overflow-hidden">
                    <img v-if="passageiro.foto_url" :src="passageiro.foto_url" class="h-full w-full object-cover" :alt="passageiro.nome">
                    <span v-else class="text-blue-600 font-bold">{{ passageiro.nome?.charAt(0) }}</span>
                </div>
                <div class="min-w-0">
                    <p class="font-semibold text-slate-900 truncate text-sm">{{ passageiro.nome }}</p>
                    <span class="text-xs px-2 py-0.5 rounded-full border" :class="statusClasses(passageiro.status_color)">
                        {{ statusIcone(passageiro.status) }} {{ passageiro.status_label }}
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <Link
                    :href="route('responsavel.passageiros.show', passageiro.id_passageiro)"
                    class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition px-2.5 py-1.5 border border-blue-200 rounded-lg"
                >
                    Detalhes
                </Link>
                <button
                    v-if="passageiro.status !== 'vinculo_ativo'"
                    @click="emit('buscar-van')"
                    class="text-xs font-semibold text-slate-600 hover:text-blue-600 transition px-2.5 py-1.5 border border-slate-200 rounded-lg hover:border-blue-200"
                >
                    Buscar van
                </button>
            </div>
        </div>
    </article>

</template>