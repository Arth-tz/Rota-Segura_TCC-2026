<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'

import Sidebar            from '@/Components/Motorista/Layout/Sidebar.vue'
import BottomNav          from '@/Components/Motorista/Layout/BottomNav.vue'
import SecaoInicio        from '@/Components/Motorista/Dashboard/SecaoInicio.vue'
import SecaoSolicitacoes  from '@/Components/Motorista/Dashboard/SecaoSolicitacoes.vue'
import SecaoPassageiros   from '@/Components/Motorista/Dashboard/SecaoPassageiros.vue'
import SecaoPerfil        from '@/Components/Motorista/Dashboard/SecaoPerfil.vue'
import FlashMessage       from '@/Components/UI/FlashMessage.vue'

const props = defineProps({
    motorista:             { type: Object, default: null },
    van:                   { type: Object, default: null },
    passageiros:           { type: Array,  default: () => [] },
    disponibilidades:      { type: Array,  default: () => [] },
    solicitacoes:          { type: Array,  default: () => [] },
    solicitacoesPendentes: { type: Number, default: 0 },
    usuario:               { type: Object, default: null },
})

const secaoAtiva = ref('inicio')

const titulos = {
    inicio:       'Início',
    solicitacoes: 'Solicitações',
    passageiros:  'Meus Passageiros',
    perfil:       'Meu Perfil',
}
</script>

<template>
    <Head title="Dashboard — Motorista" />
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Nunito:wght@400;500;600&display=swap" rel="stylesheet" />

    <FlashMessage />
    <div class="min-h-screen flex bg-slate-100">

        <Sidebar :secaoAtiva="secaoAtiva" :usuario="usuario" :solicitacoesPendentes="solicitacoesPendentes" @mudar="secaoAtiva = $event" />

        <div class="flex-1 flex flex-col min-w-0 bg-slate-50">

            <!-- Header -->
            <header class="bg-gradient-to-b from-amber-500 to-amber-600 text-white px-6 py-4 sticky top-0 z-10 shadow-md border-b border-amber-700/40">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-widest text-amber-200 font-medium">Motorista</p>
                        <h2 class="text-xl font-bold mt-0.5" style="font-family:'Sora',sans-serif;">
                            {{ titulos[secaoAtiva] }}
                        </h2>
                    </div>
                    <div v-if="solicitacoesPendentes > 0"
                        class="flex items-center gap-2 rounded-xl bg-white/15 border border-white/20 px-3 py-1.5 text-xs font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                        {{ solicitacoesPendentes }} pendente{{ solicitacoesPendentes > 1 ? 's' : '' }}
                    </div>
                </div>
            </header>

            <main class="flex-1 px-4 md:px-8 py-6 pb-24 md:pb-8">
                <SecaoInicio
                    v-if="secaoAtiva === 'inicio'"
                    :motorista="motorista"
                    :van="van"
                    :passageiros="passageiros"
                    :disponibilidades="disponibilidades"
                    :solicitacoesPendentes="solicitacoesPendentes"
                    @ir-passageiros="secaoAtiva = 'passageiros'"
                    @ir-solicitacoes="secaoAtiva = 'solicitacoes'"
                />
                <SecaoSolicitacoes
                    v-else-if="secaoAtiva === 'solicitacoes'"
                    :solicitacoes="solicitacoes"
                />
                <SecaoPassageiros
                    v-else-if="secaoAtiva === 'passageiros'"
                    :passageiros="passageiros"
                />
                <SecaoPerfil
                    v-else-if="secaoAtiva === 'perfil'"
                    :usuario="usuario"
                    :motorista="motorista"
                />
            </main>
        </div>

        <BottomNav :secaoAtiva="secaoAtiva" :solicitacoesPendentes="solicitacoesPendentes" @mudar="secaoAtiva = $event" />
    </div>
</template>
