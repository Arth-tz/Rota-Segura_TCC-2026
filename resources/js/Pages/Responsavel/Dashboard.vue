<script setup>
import { Link } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'

import FlashMessage     from '@/Components/UI/FlashMessage.vue'
import Sidebar    from '@/Components/Responsavel/Layout/Sidebar.vue'
import BottomNav  from '@/Components/Responsavel/Layout/BottomNav.vue'
import SecaoInicio      from '@/Components/Responsavel/Dashboard/SecaoInicio.vue'
import SecaoPassageiros from '@/Components/Responsavel/Dashboard/SecaoPassageiros.vue'
import SecaoBuscar      from '@/Components/Responsavel/Dashboard/SecaoBuscar.vue'
import SecaoPerfil      from '@/Components/Responsavel/Dashboard/SecaoPerfil.vue'

const props = defineProps({
    passageiros: {
        type: Array,
        default: () => [],
    },
})

const page    = usePage()
const usuario = computed(() => page.props.auth?.user ?? null)

const secaoAtiva = ref('inicio')

const titulos = {
    inicio:       'Início',
    passageiros:  'Meus Passageiros',
    buscar:       'Buscar Vans',
    perfil:       'Meu Perfil',
}
</script>

<template>
    <Head title="Dashboard — Responsável" />

    <FlashMessage />
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Nunito:wght@300;400;500;600&display=swap" rel="stylesheet" />

    <div class="min-h-screen flex bg-gradient-to-r from-sky-700 via-blue-600 to-blue-700">

        <!-- Sidebar — só desktop -->
        <Sidebar
            :secaoAtiva="secaoAtiva"
            :usuario="usuario"
            @mudar="secaoAtiva = $event"
        />

        <!-- Conteúdo principal -->
        <div class="flex-1 flex flex-col min-w-0 bg-white">

            <!-- Header -->
            <header class="bg-blue-600 text-white px-6 py-4 sticky top-0 z-10 shadow-md border-b border-blue-700/50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-widest text-blue-300 font-medium">Responsável</p>
                        <h2 class="text-xl font-bold mt-0.5" style="font-family:'Sora',sans-serif;">
                            {{ titulos[secaoAtiva] }}
                        </h2>
                    </div>
                </div>
            </header>

            <!-- Seções -->
            <main class="flex-1 px-4 md:px-8 py-6 pb-24 md:pb-8">
                <SecaoInicio
                    v-if="secaoAtiva === 'inicio'"
                    :passageiros="passageiros"
                    @buscar-van="secaoAtiva = 'buscar'"
                />
                <SecaoPassageiros
                    v-else-if="secaoAtiva === 'passageiros'"
                    :passageiros="passageiros"
                />
                <SecaoBuscar
                    v-else-if="secaoAtiva === 'buscar'"
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

<style>
html, body {
  background: linear-gradient(to right, #0ea5e9, #2563eb, #1e40af);
}
</style>