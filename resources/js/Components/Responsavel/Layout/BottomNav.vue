<script setup>
import { Link } from '@inertiajs/vue3'
import {
    HomeIcon,
    UsersIcon,
    MapIcon,
    UserCircleIcon,
    MagnifyingGlassIcon,
} from '@heroicons/vue/24/outline'

defineProps({
    secaoAtiva: { type: String, required: true },
})

const emit = defineEmits(['mudar'])

const navItems = [
    { key: 'inicio',      label: 'Início',       icon: HomeIcon },
    { key: 'passageiros', label: 'Passageiros',  icon: UsersIcon },
    { key: 'acompanhar',  label: 'Acompanhar',   icon: MapIcon },
    { key: 'perfil',      label: 'Perfil',       icon: UserCircleIcon },
]
</script>

<template>
    <nav class="md:hidden fixed bottom-0 inset-x-0 bg-blue-950 text-white border-t border-blue-800/60 z-50 safe-area-pb">
        <div class="grid grid-cols-5">
            <button
                v-for="item in navItems"
                :key="item.key"
                @click="emit('mudar', item.key)"
                class="relative flex flex-col items-center justify-center py-2.5 gap-1 transition-all"
                :class="secaoAtiva === item.key ? 'text-blue-300' : 'text-blue-500/70'"
            >
                <component :is="item.icon" class="w-5 h-5" />
                <span class="text-[10px] font-medium leading-none">{{ item.label }}</span>
                <span
                    v-if="secaoAtiva === item.key"
                    class="absolute top-0 inset-x-3 h-0.5 bg-blue-400 rounded-full"
                ></span>
            </button>

            <!-- Buscar vai direto para o marketplace -->
            <Link :href="route('responsavel.marketplace')"
                class="relative flex flex-col items-center justify-center py-2.5 gap-1 transition-all text-blue-500/70 hover:text-blue-300">
                <MagnifyingGlassIcon class="w-5 h-5" />
                <span class="text-[10px] font-medium leading-none">Buscar</span>
            </Link>
        </div>
    </nav>
</template>
