<script setup>
import { HomeIcon, UsersIcon, UserIcon, ClipboardDocumentListIcon, MapPinIcon } from '@heroicons/vue/24/outline'

defineProps({
    secaoAtiva:            { type: String, required: true },
    solicitacoesPendentes: { type: Number, default: 0 },
})
const emit = defineEmits(['mudar'])

const navItems = [
    { key: 'inicio',       label: 'Início',       icon: HomeIcon },
    { key: 'solicitacoes', label: 'Solicitações',  icon: ClipboardDocumentListIcon },
    { key: 'passageiros',  label: 'Passageiros',  icon: UsersIcon },
    { key: 'trajetos',     label: 'Trajetos',      icon: MapPinIcon },
    { key: 'perfil',       label: 'Perfil',        icon: UserIcon },
]
</script>

<template>
    <nav class="md:hidden fixed bottom-0 inset-x-0 z-20 bg-amber-900 border-t border-amber-800 flex">
        <button
            v-for="item in navItems"
            :key="item.key"
            @click="emit('mudar', item.key)"
            class="flex-1 flex flex-col items-center justify-center py-2.5 text-xs font-medium transition-colors relative"
            :class="secaoAtiva === item.key ? 'text-amber-300' : 'text-amber-600'"
        >
            <div class="relative">
                <component :is="item.icon" class="w-5 h-5 mb-1" />
                <span v-if="item.key === 'solicitacoes' && solicitacoesPendentes > 0"
                    class="absolute -top-1 -right-2 w-4 h-4 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center">
                    {{ solicitacoesPendentes > 9 ? '9+' : solicitacoesPendentes }}
                </span>
            </div>
            {{ item.label }}
        </button>
    </nav>
</template>
