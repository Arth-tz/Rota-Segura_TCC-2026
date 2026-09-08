<script setup>
import { HomeIcon, UsersIcon, UserIcon, ClipboardDocumentListIcon, MapPinIcon } from '@heroicons/vue/24/outline'

defineProps({
    secaoAtiva:            { type: String, required: true },
    solicitacoesPendentes: { type: Number, default: 0 },
})
const emit = defineEmits(['mudar'])

const navItems = [
    { key: 'inicio',       label: 'Início',      icon: HomeIcon },
    { key: 'solicitacoes', label: 'Pedidos',      icon: ClipboardDocumentListIcon },
    { key: 'passageiros',  label: 'Passageiros',  icon: UsersIcon },
    { key: 'trajetos',     label: 'Trajetos',     icon: MapPinIcon },
    { key: 'perfil',       label: 'Perfil',       icon: UserIcon },
]
</script>

<template>
    <nav class="md:hidden fixed bottom-0 inset-x-0 z-50 bg-amber-900 border-t border-amber-800 flex">
        <button
            v-for="item in navItems"
            :key="item.key"
            @click="emit('mudar', item.key)"
            class="flex-1 min-w-0 flex flex-col items-center justify-center gap-1 py-2.5 px-0.5 transition-colors relative"
            :class="secaoAtiva === item.key ? 'text-amber-300' : 'text-amber-600'"
        >
            <span v-if="secaoAtiva === item.key"
                class="absolute top-0 inset-x-3 h-0.5 bg-amber-400 rounded-full"></span>
            <div class="relative shrink-0">
                <component :is="item.icon" class="w-5 h-5" />
                <span v-if="item.key === 'solicitacoes' && solicitacoesPendentes > 0"
                    class="absolute -top-1.5 -right-1.5 w-4 h-4 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center ring-2 ring-amber-900">
                    {{ solicitacoesPendentes > 9 ? '9+' : solicitacoesPendentes }}
                </span>
            </div>
            <span class="text-[10px] font-medium leading-none truncate max-w-full">{{ item.label }}</span>
        </button>
    </nav>
</template>
