<script setup>
import { ref, computed } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'

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

    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Nunito:wght@300;400;500;600&display=swap" rel="stylesheet" />

    <div class="min-h-screen flex bg-[radial-gradient(circle_at_top_right,_rgba(56,189,248,0.16),_transparent_25%),radial-gradient(circle_at_bottom_left,_rgba(59,130,246,0.18),_transparent_24%)]">

        <!-- Sidebar — só desktop -->
        <Sidebar
            :secaoAtiva="secaoAtiva"
            :usuario="usuario"
            @mudar="secaoAtiva = $event"
        />

        <!-- Conteúdo principal -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Header -->
            <header class="bg-gradient-to-r from-sky-700 via-blue-600 to-blue-700 text-white px-6 py-5 sticky top-0 z-10 shadow-2xl shadow-slate-900/10 border-b border-blue-700/80">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div class="space-y-2">
                        <p class="text-xs uppercase tracking-[0.25em] text-sky-200/80">Painel do responsável</p>
                        <h2 class="text-2xl md:text-3xl font-bold" style="font-family:'Sora',sans-serif;">
                            {{ titulos[secaoAtiva] }}
                        </h2>
                        <p class="text-sm text-sky-100/90 max-w-2xl">Acompanhe passageiros, organize rotas e acesse as ações mais importantes do seu painel.</p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <span class="rounded-full bg-white/10 px-4 py-2 text-xs uppercase tracking-[0.24em] text-sky-100">Mais azul, menos branco</span>
                        <span class="rounded-full bg-white/10 px-4 py-2 text-xs uppercase tracking-[0.24em] text-sky-100">Experiência mais fluida</span>
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
                    :passageiros="passageiros"
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