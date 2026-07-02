<script setup>
import { Link } from '@inertiajs/vue3'


const emit = defineEmits(['mudar'])

const props = defineProps({
    secaoAtiva:            { type: String, required: true },
    usuario:               { type: Object, default: null },
    solicitacoesPendentes: { type: Number, default: 0 },
})

const navItems = [
    { key: 'inicio',        label: 'Início' },
    { key: 'solicitacoes',  label: 'Solicitações' },
    { key: 'passageiros',   label: 'Meus Passageiros' },
    { key: 'perfil',        label: 'Meu Perfil' },
]
</script>

<template>
    <aside class="hidden md:flex md:w-72 flex-col sticky top-0 h-screen bg-gradient-to-b from-amber-500 to-yellow-500 text-white border-r border-amber-600/30">

        <!-- Logo -->
        <div class="p-6 border-b border-amber-600/30">
            <Link :href="route('home')" class="flex items-center gap-2">
                <img src="/rota-segura/public/images/Logo_rota-segura_branco.png" alt="Rota Segura" class="h-10 w-auto" />
                <div>
                    <p class="text-xs text-amber-100 font-semibold uppercase tracking-widest">Motorista</p>
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
                class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all"
                :class="secaoAtiva === item.key
                    ? 'bg-amber-600 text-white shadow-lg shadow-amber-800/20'
                    : 'text-amber-100 hover:bg-amber-600/40 hover:text-white'"
            >
                {{ item.label }}
                <span v-if="item.key === 'solicitacoes' && solicitacoesPendentes > 0"
                    class="flex items-center justify-center w-5 h-5 rounded-full bg-white text-amber-700 text-xs font-bold shrink-0">
                    {{ solicitacoesPendentes }}
                </span>
            </button>
        </nav>

        <!-- Usuário logado -->
        <div class="p-4 border-t border-amber-600/30">
            <div class="flex items-center gap-3 px-2 py-2">
                <div class="w-9 h-9 rounded-full bg-white flex items-center justify-center shrink-0">
                    <span class="text-amber-600 text-sm font-bold">
                        {{ usuario?.nome?.charAt(0)?.toUpperCase() ?? 'M' }}
                    </span>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-white truncate">{{ usuario?.nome ?? 'Motorista' }}</p>
                    <p class="text-xs text-amber-100 truncate">{{ usuario?.email ?? '' }}</p>
                </div>
            </div>
        </div>
    </aside>
</template>
