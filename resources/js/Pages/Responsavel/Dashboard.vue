<script setup>
import { Link } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'

import FlashMessage     from '@/Components/UI/FlashMessage.vue'
import Sidebar    from '@/Components/Responsavel/Layout/Sidebar.vue'
import BottomNav  from '@/Components/Responsavel/Layout/BottomNav.vue'
import SecaoInicio      from '@/Components/Responsavel/Dashboard/SecaoInicio.vue'
import SecaoPassageiros from '@/Components/Responsavel/Dashboard/SecaoPassageiros.vue'
import SecaoBuscar      from '@/Components/Responsavel/Dashboard/SecaoBuscar.vue'
import SecaoPerfil      from '@/Components/Responsavel/Dashboard/SecaoPerfil.vue'
import SecaoAcompanhar  from '@/Components/Responsavel/Dashboard/SecaoAcompanhar.vue'

const props = defineProps({
    passageiros: {
        type: Array,
        default: () => [],
    },
})

const page    = usePage()
const usuario = computed(() => page.props.auth?.user ?? null)

onMounted(() => { document.body.style.backgroundColor = '#1e40af' })
onUnmounted(() => { document.body.style.backgroundColor = '' })

const secaoAtiva = ref('inicio')

const titulos = {
    inicio:       'Início',
    passageiros:  'Meus Passageiros',
    acompanhar:   'Acompanhar Trajeto',
    buscar:       'Buscar Vans',
    perfil:       'Meu Perfil',
}
</script>

<template>
    <Head title="Painel — Responsável" />

    <FlashMessage />

    <div class="min-h-screen flex bg-slate-100">

        <!-- Sidebar — só desktop -->
        <Sidebar
            :secaoAtiva="secaoAtiva"
            :usuario="usuario"
            @mudar="secaoAtiva = $event"
        />

        <!-- Conteúdo principal -->
        <div class="flex-1 flex flex-col min-w-0 bg-slate-50">

            <!-- Header -->
            <header class="bg-gradient-to-b from-blue-700 to-blue-800 text-white px-6 py-4 sticky top-0 z-10 shadow-md border-b border-blue-900/40">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-blue-300 font-medium">Portal Responsável</p>
                        <h2 class="text-xl font-bold leading-tight">{{ titulos[secaoAtiva] }}</h2>
                    </div>

                    <!-- Avatar → Meu Perfil (só desktop) -->
                    <button
                        @click="secaoAtiva = 'perfil'"
                        class="hidden md:flex items-center gap-2.5 rounded-xl px-2 py-1.5 hover:bg-white/10 transition-colors"
                    >
                        <span class="text-sm font-medium text-blue-100 truncate max-w-[140px]">{{ usuario?.nome }}</span>
                        <div class="w-9 h-9 rounded-full overflow-hidden bg-blue-600/60 border-2 border-white/30 flex items-center justify-center shrink-0">
                            <img v-if="usuario?.foto_url" :src="usuario.foto_url" class="w-full h-full object-cover" alt="" />
                            <span v-else class="text-white text-sm font-bold">{{ usuario?.nome?.charAt(0)?.toUpperCase() ?? '?' }}</span>
                        </div>
                    </button>
                </div>
            </header>

            <!-- Seções -->
            <main class="flex-1 min-w-0 overflow-x-hidden px-4 md:px-8 py-6 pb-24 md:pb-8">
                <SecaoInicio
                    v-if="secaoAtiva === 'inicio'"
                    :passageiros="passageiros"
                    :usuario="usuario"
                    @buscar-van="secaoAtiva = 'buscar'"
                />
                <SecaoPassageiros
                    v-else-if="secaoAtiva === 'passageiros'"
                    :passageiros="passageiros"
                />
                <SecaoBuscar
                    v-else-if="secaoAtiva === 'buscar'"
                />
                <SecaoAcompanhar
                    v-else-if="secaoAtiva === 'acompanhar'"
                />
                <SecaoPerfil
                    v-else-if="secaoAtiva === 'perfil'"
                    :usuario="usuario"
                />
            </main>
        </div>

        <!-- Bottom nav — só mobile -->
        <BottomNav
            :secaoAtiva="secaoAtiva"
            @mudar="secaoAtiva = $event"
        />
    </div>
</template>