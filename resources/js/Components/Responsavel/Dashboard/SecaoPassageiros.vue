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

        <div class="rounded-[2rem] bg-gradient-to-r from-sky-700 via-blue-600 to-blue-500 p-6 text-white shadow-2xl shadow-slate-900/10">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.24em] text-sky-100/80">Seção de passageiros</p>
                    <h3 class="mt-2 text-2xl font-semibold">Meus passageiros</h3>
                    <p class="mt-2 text-sm text-sky-100/80 max-w-xl">Veja todos os seus passageiros cadastrados no painel, acompanhe o status e adicione novos perfis com rapidez.</p>
                </div>
                <Link
                    :href="route('responsavel.passageiros.adicionar')"
                    class="inline-flex items-center justify-center rounded-full bg-white px-5 py-3 text-sm font-semibold text-sky-700 shadow-lg shadow-slate-900/10 hover:bg-slate-100 transition"
                    style="font-family:'Sora',sans-serif;"
                >
                    + Adicionar passageiro
                </Link>
            </div>

            <div class="mt-5 grid gap-3 sm:grid-cols-3">
                <div class="rounded-3xl bg-white/10 p-4">
                    <p class="text-xs uppercase tracking-[0.2em] text-sky-100/80">Cadastrados</p>
                    <p class="mt-2 text-3xl font-semibold">{{ passageiros.length }}</p>
                </div>
                <div class="rounded-3xl bg-white/10 p-4">
                    <p class="text-xs uppercase tracking-[0.2em] text-sky-100/80">Organização</p>
                    <p class="mt-2 text-sm text-sky-100/90">Acesse os detalhes e gerencie cada passageiro com um único clique.</p>
                </div>
                <div class="rounded-3xl bg-white/10 p-4">
                    <p class="text-xs uppercase tracking-[0.2em] text-sky-100/80">Próximo passo</p>
                    <p class="mt-2 text-sm text-sky-100/90">Se precisar, escolha o passageiro e comece a buscar vans disponíveis.</p>
                </div>
            </div>
        </div>

        <!-- Lista de passageiros -->
        <div class="space-y-4">
            <CardPassageiro
                v-for="p in passageiros"
                :key="p.id_passageiro"
                :passageiro="p"
                modo="lista"
                @buscar-van="emit('buscar-van')"
            />
        </div>

        <!-- Empty state -->
        <div v-if="!passageiros.length" class="rounded-[2rem] border border-dashed border-slate-200/60 bg-white/95 p-8 text-center shadow-xl">
            <p class="text-xs uppercase tracking-[0.24em] text-sky-600">Sem passageiros</p>
            <h3 class="mt-3 text-2xl font-semibold text-slate-900">Ainda não há nenhum passageiro cadastrado</h3>
            <p class="mt-3 text-sm text-slate-500">Adicione um passageiro agora para começar a usar todos os recursos disponíveis.</p>
            <div class="mt-6">
                <Link
                    :href="route('responsavel.passageiros.adicionar')"
                    class="inline-flex items-center justify-center rounded-full bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-500/20 hover:bg-blue-700 transition"
                    style="font-family:'Sora',sans-serif;"
                >
                    Cadastrar primeiro passageiro
                </Link>
            </div>
        </div>

    </div>
</template>