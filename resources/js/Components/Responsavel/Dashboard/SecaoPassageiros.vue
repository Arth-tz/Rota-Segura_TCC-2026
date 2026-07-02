<script setup>
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import { UserPlusIcon } from '@heroicons/vue/24/outline'
import CardPassageiro from '@/Components/Responsavel/Dashboard/CardPassageiro.vue'

const props = defineProps({
    passageiros: { type: Array, default: () => [] },
})

const emit = defineEmits(['buscar-van'])

const comVan   = computed(() => props.passageiros.filter(p => p.status === 'vinculo_ativo').length)
const semVan   = computed(() => props.passageiros.filter(p => p.status !== 'vinculo_ativo').length)
const pendente = computed(() => props.passageiros.filter(p => p.status === 'solicitacao_pendente').length)
</script>

<template>
    <div class="space-y-4">

        <!-- Header com stats reais -->
        <div class="rounded-2xl bg-gradient-to-br from-blue-600 to-blue-700 p-5 text-white shadow-lg">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h3 class="text-lg font-bold" style="font-family:'Sora',sans-serif;">Meus passageiros</h3>
                <Link
                    :href="route('responsavel.passageiros.adicionar')"
                    class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-50 transition self-start sm:self-auto"
                    style="font-family:'Sora',sans-serif;"
                >
                    <UserPlusIcon class="w-4 h-4" />
                    Adicionar
                </Link>
            </div>

            <div class="mt-4 grid grid-cols-3 gap-3">
                <div class="rounded-xl bg-white/10 px-4 py-3 text-center">
                    <p class="text-2xl font-bold">{{ passageiros.length }}</p>
                    <p class="text-xs text-blue-200 mt-0.5">total</p>
                </div>
                <div class="rounded-xl bg-white/10 px-4 py-3 text-center">
                    <p class="text-2xl font-bold">{{ comVan }}</p>
                    <p class="text-xs text-blue-200 mt-0.5">com van</p>
                </div>
                <div class="rounded-xl bg-white/10 px-4 py-3 text-center">
                    <p class="text-2xl font-bold">{{ semVan }}</p>
                    <p class="text-xs text-blue-200 mt-0.5">sem van</p>
                </div>
            </div>
        </div>

        <!-- Alerta pendentes -->
        <div v-if="pendente > 0" class="flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            <div class="w-2 h-2 rounded-full bg-amber-400 animate-pulse shrink-0"></div>
            {{ pendente === 1 ? '1 solicitação aguarda' : `${pendente} solicitações aguardam` }} aprovação do motorista.
        </div>

        <!-- Lista -->
        <div class="space-y-3">
            <CardPassageiro
                v-for="p in passageiros"
                :key="p.id_passageiro"
                :passageiro="p"
                modo="lista"
                @buscar-van="emit('buscar-van')"
            />
        </div>

        <!-- Empty state -->
        <div v-if="!passageiros.length" class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-blue-200 bg-blue-50/50 py-14 px-6 text-center">
            <div class="w-12 h-12 rounded-2xl bg-blue-100 flex items-center justify-center mb-4">
                <UserPlusIcon class="w-6 h-6 text-blue-500" />
            </div>
            <p class="font-semibold text-slate-800" style="font-family:'Sora',sans-serif;">Nenhum passageiro cadastrado</p>
            <p class="mt-1 text-sm text-slate-500">Adicione passageiros para gerenciá-los aqui.</p>
            <Link
                :href="route('responsavel.passageiros.adicionar')"
                class="mt-5 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition shadow-sm"
                style="font-family:'Sora',sans-serif;"
            >
                <UserPlusIcon class="w-4 h-4" />
                Adicionar passageiro
            </Link>
        </div>

    </div>
</template>
