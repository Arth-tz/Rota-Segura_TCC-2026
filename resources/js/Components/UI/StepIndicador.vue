<script setup>
defineProps({
    passoAtual: { type: Number, required: true },
})

const passos = [
    { num: 1, label: 'Seus dados' },
    { num: 2, label: 'Van' },
    { num: 3, label: 'Documentos', opcional: true },
]
</script>

<template>
    <div class="flex items-start justify-center gap-0 mb-6 select-none">
        <template v-for="(passo, i) in passos" :key="passo.num">
            <!-- Linha conectora -->
            <div v-if="i > 0" class="h-px w-10 shrink-0 mt-3.5"
                :class="passo.num <= passoAtual ? 'bg-amber-400' : 'bg-slate-200'" />

            <!-- Step -->
            <div class="flex flex-col items-center w-16">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition-colors"
                    :class="{
                        'bg-amber-700 text-white':   passo.num === passoAtual,
                        'bg-emerald-500 text-white': passo.num < passoAtual,
                        'bg-slate-200 text-slate-400': passo.num > passoAtual,
                    }">
                    <svg v-if="passo.num < passoAtual" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span v-else>{{ passo.num }}</span>
                </div>
                <p class="mt-1.5 text-[10px] font-medium text-center leading-tight"
                    :class="passo.num === passoAtual ? 'text-amber-700' : 'text-slate-400'">
                    {{ passo.label }}
                    <span v-if="passo.opcional" class="block font-normal">(opcional)</span>
                </p>
            </div>
        </template>
    </div>
</template>
