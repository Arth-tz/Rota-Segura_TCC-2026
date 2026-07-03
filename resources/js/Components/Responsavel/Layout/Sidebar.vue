<script setup>
import { Link } from '@inertiajs/vue3'
defineProps({
    secaoAtiva: { type: String, required: true },
    usuario:    { type: Object, default: null },
})

const emit = defineEmits(['mudar'])

const navItems = [
    { key: 'inicio',       label: 'Início' },
    { key: 'passageiros',  label: 'Meus Passageiros' },
    { key: 'acompanhar',   label: 'Acompanhar' },
    { key: 'perfil',       label: 'Meu Perfil' },
]
</script>

<template>
    <aside class="hidden md:flex md:w-72 flex-col sticky top-0 h-screen bg-gradient-to-b from-blue-800 to-blue-950 text-white border-r border-blue-900/60">

        <!-- Logo -->
        <div class="p-6 border-b border-blue-700/50">
            
                <Link href="http://localhost/rota-segura/public/" class="flex items-center gap-2">
                    <img src="/rota-segura/public/images/Logo_rota_segura-azul.png" alt="Logo Rota Segura" class="h-10 w-10" />    
                    <div>
                        <p class="text-xs text-white font-semibold uppercase tracking-widest">Responsável</p>
                        <p class="text-base font-bold text-white" style="font-family:'Sora',sans-serif;">Rota Segura</p>
                    </div>
                </Link>
        </div>

        <!-- Nav -->
        <nav class="flex-1 p-4 space-y-1">
            <button
                v-for="item in navItems"
                :key="item.key"
                @click="emit('mudar', item.key)"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all"
                :class="secaoAtiva === item.key
                    ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/40'
                    : 'text-blue-200 hover:bg-blue-700/60 hover:text-white'"
            >
                {{ item.label }}
            </button>
            <Link :href="route('responsavel.marketplace')"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all text-blue-200 hover:bg-blue-700/60 hover:text-white">
                Buscar Vans
            </Link>
        </nav>

        <!-- Usuário logado -->
        <div class="p-4 border-t border-blue-700/40">
            <div class="flex items-center gap-3 px-2 py-2">
                <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <span class="text-blue-600 text-sm font-bold">
                        {{ usuario?.nome?.charAt(0)?.toUpperCase() ?? '?' }}
                    </span>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-white truncate">{{ usuario?.nome ?? 'Responsável' }}</p>
                    <p class="text-xs text-blue-300 truncate">{{ usuario?.email ?? '' }}</p>
                </div>
            </div>
        </div>
    </aside>
</template>

