<script setup>
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import CardPassageiro from '@/Components/Responsavel/Dashboard/CardPassageiro.vue'
import { UserPlusIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    passageiros: { type: Array, default: () => [] },
})

const emit = defineEmits(['buscar-van'])

const comVan   = computed(() => props.passageiros.filter(p => p.status === 'vinculo_ativo').length)
const semVan   = computed(() => props.passageiros.filter(p => p.status !== 'vinculo_ativo').length)
const pendente = computed(() => props.passageiros.filter(p => p.status === 'solicitacao_pendente').length)
</script>

<template>
    <div class="space-y-5">

        <!-- Hero -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-600 to-blue-700 p-6 text-white shadow-lg">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_rgba(255,255,255,0.12),_transparent_60%)] pointer-events-none"></div>
            <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
                <div>
                    <p class="text-4xl font-bold" style="font-family:'Sora',sans-serif;">{{ passageiros.length }}</p>
                    <p class="mt-1 text-sm text-blue-200">{{ passageiros.length === 1 ? 'passageiro cadastrado' : 'passageiros cadastrados' }}</p>
                </div>

                <!-- Stats inline -->
                <div v-if="passageiros.length" class="flex gap-4 text-sm">
                    <div class="text-center">
                        <p class="text-2xl font-bold">{{ comVan }}</p>
                        <p class="text-blue-200 text-xs mt-0.5">com van</p>
                    </div>
                    <div class="w-px bg-white/20"></div>
                    <div class="text-center">
                        <p class="text-2xl font-bold">{{ semVan }}</p>
                        <p class="text-blue-200 text-xs mt-0.5">sem van</p>
                    </div>
                    <template v-if="pendente > 0">
                        <div class="w-px bg-white/20"></div>
                        <div class="text-center">
                            <p class="text-2xl font-bold">{{ pendente }}</p>
                            <p class="text-blue-200 text-xs mt-0.5">pendente{{ pendente > 1 ? 's' : '' }}</p>
                        </div>
                    </template>
                </div>

                <!-- CTAs -->
                <div class="flex flex-wrap gap-2">
                    <Link
                        :href="route('responsavel.passageiros.adicionar')"
                        class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-blue-700 hover:bg-blue-50 transition shadow-sm"
                        style="font-family:'Sora',sans-serif;"
                    >
                        <UserPlusIcon class="w-4 h-4" />
                        Novo passageiro
                    </Link>
                    <Link
                        :href="route('responsavel.marketplace')"
                        class="inline-flex items-center gap-2 rounded-xl border border-white/25 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white hover:bg-white/20 transition"
                        style="font-family:'Sora',sans-serif;"
                    >
                        <MagnifyingGlassIcon class="w-4 h-4" />
                        Buscar van
                    </Link>
                </div>
            </div>
        </div>

        <!-- Alerta de pendentes -->
        <div v-if="pendente > 0" class="flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            <div class="w-2 h-2 rounded-full bg-amber-400 animate-pulse shrink-0"></div>
            <span>
                {{ pendente === 1 ? '1 solicitação aguarda' : `${pendente} solicitações aguardam` }} aprovação do motorista.
            </span>
        </div>

        <!-- Lista de passageiros -->
        <div v-if="passageiros.length" class="grid gap-3 lg:grid-cols-2">
            <CardPassageiro
                v-for="p in passageiros"
                :key="p.id_passageiro"
                :passageiro="p"
                modo="inicio"
                @buscar-van="emit('buscar-van')"
            />
        </div>

        <!-- Empty state -->
        <div v-else class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-blue-200 bg-blue-50/50 py-14 px-6 text-center">
            <div class="w-12 h-12 rounded-2xl bg-blue-100 flex items-center justify-center mb-4">
                <UserPlusIcon class="w-6 h-6 text-blue-500" />
            </div>
            <p class="font-semibold text-slate-800" style="font-family:'Sora',sans-serif;">Nenhum passageiro ainda</p>
            <p class="mt-1 text-sm text-slate-500 max-w-xs">Cadastre o primeiro passageiro para começar a organizar o transporte.</p>
            <Link
                :href="route('responsavel.passageiros.adicionar')"
                class="mt-5 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition shadow-sm"
                style="font-family:'Sora',sans-serif;"
            >
                <UserPlusIcon class="w-4 h-4" />
                Cadastrar passageiro
            </Link>
        </div>

    </div>
</template>
