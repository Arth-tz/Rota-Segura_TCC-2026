<script setup>
defineProps({
    secaoAtiva: { type: String, required: true },
})

const emit = defineEmits(['mudar'])

const navItems = [
    { key: 'inicio',      label: 'Início' },
    { key: 'passageiros', label: 'Passageiros' },
    { key: 'buscar',      label: 'Buscar' },
    { key: 'perfil',      label: 'Perfil' },
]
</script>

<template>
    <nav class="md:hidden fixed bottom-0 inset-x-0 bg-slate-950 text-slate-200 border-t border-slate-800 z-50 safe-area-pb">
        <div class="grid grid-cols-4">
            <button
                v-for="item in navItems"
                :key="item.key"
                @click="emit('mudar', item.key)"
                class="relative flex flex-col items-center justify-center py-3 gap-0.5 transition-all"
                :class="secaoAtiva === item.key ? 'text-sky-400 font-semibold' : 'text-slate-500'"
            >
                                <span class="text-xs font-medium">{{ item.label }}</span>

                <span
                    v-if="secaoAtiva === item.key"
                    class="absolute -top-1 w-8 h-0.5 bg-sky-400 rounded-full"
                ></span>
            </button>
        </div>
    </nav>
</template>
