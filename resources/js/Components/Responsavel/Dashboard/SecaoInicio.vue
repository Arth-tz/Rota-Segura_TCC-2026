<script setup>
import { Link } from '@inertiajs/vue3'
import CardPassageiro from '@/Components/Responsavel/Dashboard/CardPassageiro.vue'

defineProps({
    passageiros: { type: Array, default: () => [] },
})

const emit = defineEmits(['buscar-van'])
</script>

<template>
    <div class="space-y-4">

        <div class="grid gap-4 xl:grid-cols-[1.35fr_0.85fr]">
            <div class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-sky-700 via-blue-600 to-indigo-600 p-6 text-white shadow-2xl shadow-slate-900/10">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(255,255,255,0.24),_transparent_20%)] pointer-events-none"></div>
                <div class="relative flex flex-col justify-between h-full">
                    <div class="space-y-4">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs uppercase tracking-[0.24em] text-sky-100">
                            Visão geral
                        </span>
                        <div>
                            <p class="text-5xl font-semibold">{{ passageiros.length }}</p>
                            <p class="text-sm text-sky-100/80 mt-1">passageiro(s) cadastrados</p>
                        </div>
                        <p class="max-w-xl text-sm leading-6 text-sky-100/80">
                            Neste painel você vê seus passageiros, consulta o status das solicitações e encontra as ações mais importantes em um só lugar.
                        </p>
                    </div>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <Link
                            :href="route('responsavel.passageiros.adicionar')"
                            class="inline-flex items-center justify-center rounded-full bg-white px-5 py-3 text-sm font-semibold text-sky-700 shadow-lg shadow-slate-900/10 hover:bg-slate-100 transition"
                            style="font-family:'Sora',sans-serif;"
                        >
                            Cadastrar novo passageiro
                        </Link>
                        <button
                            @click="emit('buscar-van')"
                            class="inline-flex items-center justify-center rounded-full border border-white/20 bg-white/10 px-5 py-3 text-sm font-semibold text-white hover:bg-white/15 transition"
                            style="font-family:'Sora',sans-serif;"
                        >
                            Buscar van agora
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid gap-4">
                <div class="rounded-[1.75rem] border border-slate-200/20 bg-white/80 p-5 shadow-lg shadow-slate-900/5">
                    <p class="text-xs uppercase tracking-[0.24em] text-sky-600">Dica rápida</p>
                    <h3 class="mt-3 text-lg font-semibold text-slate-900">Use o painel de passageiros</h3>
                    <p class="mt-2 text-sm text-slate-600">Verifique o status de cada passageiro e inicie a busca por vans diretamente da lista.</p>
                </div>
                <div class="rounded-[1.75rem] border border-slate-200/20 bg-white/80 p-5 shadow-lg shadow-slate-900/5">
                    <p class="text-xs uppercase tracking-[0.24em] text-sky-600">Recomendado</p>
                    <h3 class="mt-3 text-lg font-semibold text-slate-900">Primeiro passo</h3>
                    <p class="mt-2 text-sm text-slate-600">Se ainda não há passageiros, crie o cadastro inicial para liberar o acompanhamento das rotas.</p>
                </div>
            </div>
        </div>

        <div class="grid gap-4">
            <div class="rounded-[2rem] bg-slate-950/90 px-6 py-5 text-slate-100 shadow-2xl shadow-slate-900/10">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.24em] text-sky-300/80">Resumo rápido</p>
                        <h3 class="mt-2 text-xl font-semibold">Seu painel está pronto para uso</h3>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-sky-500/10 px-3 py-1 text-xs font-semibold text-sky-100">
                        Atualizado automaticamente
                    </span>
                </div>
                <p class="mt-4 max-w-2xl text-sm leading-6 text-slate-300">Se já tiver passageiros cadastrados, eles aparecerão abaixo. Caso contrário, clique em “Cadastrar novo passageiro” para começar.</p>
            </div>

            <div v-if="!passageiros.length" class="rounded-[2rem] border border-sky-200/40 bg-white/90 p-8 text-center shadow-xl">
                <p class="text-xs uppercase tracking-[0.2em] text-sky-600">Primeiro passageiro</p>
                <h3 class="mt-3 text-2xl font-semibold text-slate-900">Cadastre seu primeiro passageiro</h3>
                <p class="mt-3 text-sm text-slate-500">Acompanhe rotas, solicite vans e mantenha tudo organizado em um só lugar.</p>
                <div class="mt-6 flex justify-center">
                    <Link
                        :href="route('responsavel.passageiros.create')"
                        class="inline-flex items-center justify-center rounded-full bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-500/20 hover:bg-blue-700 transition"
                        style="font-family:'Sora',sans-serif;"
                    >
                        Cadastrar passageiro
                    </Link>
                </div>
            </div>

            <div v-else class="grid gap-4 lg:grid-cols-2">
                <CardPassageiro
                    v-for="p in passageiros"
                    :key="p.id_passageiro"
                    :passageiro="p"
                    modo="inicio"
                    @buscar-van="emit('buscar-van')"
                />
            </div>
        </div>

    </div>
</template>