<script setup>
defineProps({
    secaoAtiva: { type: String, required: true },
    usuario:    { type: Object, default: null },
})

const emit = defineEmits(['mudar'])

const navItems = [
    { key: 'inicio',      label: 'Início' },
    { key: 'passageiros', label: 'Meus Passageiros' },
    { key: 'buscar',      label: 'Buscar Vans' },
    { key: 'perfil',      label: 'Meu Perfil' },
]
</script>

<template>
    <aside class="hidden md:flex md:w-72 flex-col sticky top-0 h-screen bg-slate-950 text-slate-100 border-r border-slate-900/40">

        <!-- Logo -->
        <div class="p-6 border-b border-slate-800">
            
                <Link href="http://localhost/rota-segura/public/" class="flex items-center gap-2">
                    <img src="/rota-segura/public/images/Logo_rota_segura-azul.png" alt="Logo Rota Segura" class="h-10 w-10" />    
                    <div>
                        <p class="text-xs text-blue-600 font-semibold uppercase tracking-widest">Responsável</p>
                        <p class="text-base font-bold text-slate-900" style="font-family:'Sora',sans-serif;">Rota Segura</p>
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
                    ? 'bg-sky-500/15 text-white shadow-lg shadow-sky-500/10'
                    : 'text-slate-300 hover:bg-slate-900/70 hover:text-white'"
            >
                                {{ item.label }}
            </button>
        </nav>

        <!-- Usuário logado -->
        <div class="p-4 border-t border-slate-100">
            <div class="flex items-center gap-3 px-2 py-2">
                <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <span class="text-blue-600 text-sm font-bold">
                        {{ usuario?.nome?.charAt(0)?.toUpperCase() ?? '?' }}
                    </span>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-slate-900 truncate">{{ usuario?.nome ?? 'Responsável' }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ usuario?.email ?? '' }}</p>
                </div>
            </div>
        </div>
    </aside>
</template>

