<script setup>
import { Link } from '@inertiajs/vue3'
import {
    HomeIcon,
    ClipboardDocumentListIcon,
    UsersIcon,
    MapIcon,
    UserCircleIcon,
    ArrowRightOnRectangleIcon,
    IdentificationIcon,
} from '@heroicons/vue/24/outline'

const emit = defineEmits(['mudar'])

const props = defineProps({
    secaoAtiva:            { type: String, required: true },
    usuario:               { type: Object, default: null },
    solicitacoesPendentes: { type: Number, default: 0 },
})

const navItems = [
    { key: 'inicio',        label: 'Início',            icon: HomeIcon },
    { key: 'solicitacoes',  label: 'Solicitações',      icon: ClipboardDocumentListIcon },
    { key: 'passageiros',   label: 'Meus Passageiros',  icon: UsersIcon },
    { key: 'trajetos',      label: 'Meus Trajetos',     icon: MapIcon },
    { key: 'perfil',        label: 'Meu Perfil',        icon: UserCircleIcon },
]
</script>

<template>
    <aside class="hidden md:flex md:w-64 flex-col sticky top-0 h-screen bg-gradient-to-b from-amber-700 to-amber-900 text-white border-r border-amber-800/30">

        <!-- Logo -->
        <div class="px-5 py-5 border-b border-amber-800/30">
            <Link :href="route('home')" class="flex items-center gap-2.5">
                <img src="/images/Logo_rota-segura_branco.png" alt="Rota Segura" class="h-9 w-auto" />
                <div>
                    <p class="text-[10px] text-amber-200 font-semibold uppercase tracking-widest leading-none mb-0.5">Motorista</p>
                    <p class="text-sm font-bold text-white leading-none">Rota Segura</p>
                </div>
            </Link>
        </div>

        <!-- Nav -->
        <nav class="flex-1 p-3 space-y-0.5 overflow-y-auto">
            <button
                v-for="item in navItems"
                :key="item.key"
                @click="emit('mudar', item.key)"
                class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all"
                :class="secaoAtiva === item.key
                    ? 'bg-amber-800/80 text-white shadow-md shadow-amber-900/30'
                    : 'text-amber-100/80 hover:bg-amber-800/40 hover:text-white'"
            >
                <component :is="item.icon" class="shrink-0" style="width:18px;height:18px;" />
                <span class="flex-1 text-left">{{ item.label }}</span>
                <span v-if="item.key === 'solicitacoes' && solicitacoesPendentes > 0"
                    class="flex items-center justify-center w-5 h-5 rounded-full bg-white text-amber-900 text-xs font-bold shrink-0">
                    {{ solicitacoesPendentes }}
                </span>
            </button>

            <!-- Documentos pessoais — página separada -->
            <div class="pt-2 mt-1 border-t border-amber-800/30">
                <Link :href="route('motorista.documentos.index')"
                    class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-amber-100/80 hover:bg-amber-800/40 hover:text-white transition-all">
                    <IdentificationIcon style="width:18px;height:18px;" class="shrink-0" />
                    <span class="flex-1 text-left">Documentos Pessoais</span>
                </Link>
            </div>
        </nav>

        <!-- Rodapé: logout -->
        <div class="p-3 border-t border-amber-800/30">
            <Link
                :href="route('logout')"
                method="post"
                as="button"
                class="w-full flex items-center gap-3 px-3.5 py-2 rounded-xl text-sm font-medium text-amber-200/70 hover:bg-red-900/30 hover:text-red-300 transition-all"
            >
                <ArrowRightOnRectangleIcon style="width:18px;height:18px;" class="shrink-0" />
                Sair
            </Link>
        </div>
    </aside>
</template>
