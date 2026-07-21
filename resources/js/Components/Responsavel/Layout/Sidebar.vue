<script setup>
import { Link } from '@inertiajs/vue3'
import {
    HomeIcon,
    UsersIcon,
    MapIcon,
    UserCircleIcon,
    MagnifyingGlassIcon,
    ArrowRightOnRectangleIcon,
} from '@heroicons/vue/24/outline'

defineProps({
    secaoAtiva: { type: String, required: true },
    usuario:    { type: Object, default: null },
})

const emit = defineEmits(['mudar'])

const navItems = [
    { key: 'inicio',       label: 'Início',           icon: HomeIcon },
    { key: 'passageiros',  label: 'Meus Passageiros', icon: UsersIcon },
    { key: 'acompanhar',   label: 'Acompanhar',       icon: MapIcon },
    { key: 'perfil',       label: 'Meu Perfil',       icon: UserCircleIcon },
]
</script>

<template>
    <aside class="hidden md:flex md:w-64 flex-col sticky top-0 h-screen bg-gradient-to-b from-blue-800 to-blue-950 text-white border-r border-blue-900/60">

        <!-- Logo -->
        <div class="px-5 py-5 border-b border-blue-700/40">
            <Link :href="route('home')" class="flex items-center gap-2.5">
                <img src="/rota-segura/public/images/Logo_rota_segura-azul.png" alt="Logo Rota Segura" class="h-9 w-9" />
                <div>
                    <p class="text-[10px] text-blue-300 font-semibold uppercase tracking-widest leading-none mb-0.5">Responsável</p>
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
                    ? 'bg-blue-600/80 text-white shadow-md shadow-blue-900/30'
                    : 'text-blue-200/80 hover:bg-blue-700/50 hover:text-white'"
            >
                <component :is="item.icon" class="shrink-0" style="width:18px;height:18px;" />
                {{ item.label }}
            </button>

            <Link :href="route('responsavel.marketplace')"
                class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all text-blue-200/80 hover:bg-blue-700/50 hover:text-white">
                <MagnifyingGlassIcon class="shrink-0" style="width:18px;height:18px;" />
                Buscar Vans
            </Link>
        </nav>

        <!-- Rodapé: usuário + logout -->
        <div class="p-3 border-t border-blue-700/40 space-y-1">
            <div class="flex items-center gap-3 px-3 py-2">
                <div class="w-8 h-8 rounded-full overflow-hidden bg-blue-100 flex items-center justify-center shrink-0">
                    <img v-if="usuario?.foto_url" :src="usuario.foto_url" class="w-full h-full object-cover" alt="" />
                    <span v-else class="text-blue-700 text-xs font-bold">
                        {{ usuario?.nome?.charAt(0)?.toUpperCase() ?? '?' }}
                    </span>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-white truncate leading-tight">{{ usuario?.nome ?? 'Responsável' }}</p>
                    <p class="text-xs text-blue-300 truncate leading-tight">{{ usuario?.email ?? '' }}</p>
                </div>
            </div>
            <Link
                :href="route('logout')"
                method="post"
                as="button"
                class="w-full flex items-center gap-3 px-3.5 py-2 rounded-xl text-sm font-medium text-blue-300/70 hover:bg-red-900/30 hover:text-red-300 transition-all"
            >
                <ArrowRightOnRectangleIcon style="width:18px;height:18px;" class="shrink-0" />
                Sair
            </Link>
        </div>
    </aside>
</template>
