<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    secaoAtiva: { type: String, required: true },
})

const emit = defineEmits(['mudar'])

const navItems = [
    { key: 'inicio',      label: 'Início' },
    { key: 'passageiros', label: 'Passageiros' },
    { key: 'acompanhar',  label: 'Acompanhar' },
    { key: 'perfil',      label: 'Perfil' },
]
</script>

<template>
    <nav class="md:hidden fixed bottom-0 inset-x-0 bg-blue-900 text-white border-t border-blue-800 z-50 safe-area-pb">
        <div class="grid grid-cols-5">
            <button
                v-for="item in navItems"
                :key="item.key"
                @click="emit('mudar', item.key)"
                class="relative flex flex-col items-center justify-center py-3 gap-0.5 transition-all"
                :class="secaoAtiva === item.key ? 'text-blue-300 font-semibold' : 'text-blue-500'"
            >
                <span class="text-xs font-medium">{{ item.label }}</span>
                <span
                    v-if="secaoAtiva === item.key"
                    class="absolute -top-1 w-8 h-0.5 bg-blue-400 rounded-full"
                ></span>
            </button>

            <!-- Buscar vai direto para o marketplace -->
            <Link :href="route('responsavel.marketplace')"
                class="relative flex flex-col items-center justify-center py-3 gap-0.5 transition-all text-blue-500 hover:text-blue-300">
                <span class="text-xs font-medium">Buscar</span>
            </Link>
        </div>
    </nav>
</template>
