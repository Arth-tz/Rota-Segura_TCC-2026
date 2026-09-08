<script setup>
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import CardPassageiro from '@/Components/Responsavel/Dashboard/CardPassageiro.vue'
import { UserPlusIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    passageiros: { type: Array, default: () => [] },
    usuario:     { type: Object, default: null },
})

const emit = defineEmits(['buscar-van'])

const passageirosAtivos = computed(() => props.passageiros.filter(p => p.status === 'vinculo_ativo'))
const passageirosAguard = computed(() => props.passageiros.filter(p => p.status === 'solicitacao_pendente'))
const passageirosSemVan = computed(() => props.passageiros.filter(
    p => p.status !== 'vinculo_ativo' && p.status !== 'solicitacao_pendente'
))

const statusGlobal = computed(() => {
    if (!props.passageiros.length) return 'vazio'
    if (passageirosSemVan.value.length === 0 && passageirosAguard.value.length === 0) return 'ok'
    if (passageirosAtivos.value.length === 0 && passageirosSemVan.value.length === 0) return 'aguardando'
    if (passageirosAtivos.value.length === 0) return 'atencao'
    return 'parcial'
})

const saudacao = computed(() => {
    const h = new Date().getHours()
    if (h < 12) return 'Bom dia'
    if (h < 18) return 'Boa tarde'
    return 'Boa noite'
})
</script>

<template>
    <div class="space-y-5">

        <!-- Hero: status-first, não métrica -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-600 to-blue-700 p-6 text-white shadow-lg">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_rgba(255,255,255,0.12),_transparent_60%)] pointer-events-none"></div>
            <div class="relative">
                <p class="text-sm text-blue-200 mb-3">{{ saudacao }}, {{ usuario?.nome?.split(' ')[0] ?? 'Responsável' }}</p>

                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <!-- "Tudo certo" -->
                        <template v-if="statusGlobal === 'ok'">
                            <p class="text-2xl font-bold">Tudo certo por aqui.</p>
                            <p class="text-sm text-blue-100 mt-1.5">
                                {{ passageiros.length === 1 ? 'Seu passageiro tem' : 'Todos os passageiros têm' }} transporte ativo.
                            </p>
                        </template>

                        <!-- Sem van nenhuma -->
                        <template v-else-if="statusGlobal === 'atencao'">
                            <p class="text-2xl font-bold">
                                {{ passageiros.length === 1 ? 'Seu passageiro está' : `${passageiros.length} passageiros estão` }} sem van.
                            </p>
                            <p class="text-sm text-blue-100 mt-1.5">Busque motoristas disponíveis no marketplace.</p>
                        </template>

                        <!-- Todos aguardando -->
                        <template v-else-if="statusGlobal === 'aguardando'">
                            <p class="text-2xl font-bold">Aguardando confirmação.</p>
                            <p class="text-sm text-blue-100 mt-1.5">
                                {{ passageiros.length === 1 ? 'Sua solicitação está sendo' : `${passageiros.length} solicitações estão sendo` }} analisada{{ passageiros.length > 1 ? 's' : '' }} pelo motorista.
                            </p>
                        </template>

                        <!-- Parcial -->
                        <template v-else-if="statusGlobal === 'parcial'">
                            <p class="text-2xl font-bold">{{ passageirosAtivos.length }} de {{ passageiros.length }} com transporte ativo.</p>
                            <div class="flex flex-wrap items-center gap-x-2 mt-1.5 text-sm text-blue-100">
                                <span v-if="passageirosAguard.length" class="text-amber-300 font-medium">
                                    {{ passageirosAguard.length }} aguardando confirmação
                                </span>
                                <span v-if="passageirosAguard.length && passageirosSemVan.length" class="opacity-40">·</span>
                                <span v-if="passageirosSemVan.length">{{ passageirosSemVan.length }} sem van</span>
                            </div>
                        </template>

                        <!-- Vazio -->
                        <template v-else>
                            <p class="text-2xl font-bold">Comece por aqui.</p>
                            <p class="text-sm text-blue-100 mt-1.5">Cadastre o primeiro passageiro para organizar o transporte.</p>
                        </template>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <Link
                            :href="route('responsavel.passageiros.adicionar')"
                            class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-50 transition shadow-sm"
                        >
                            <UserPlusIcon class="w-4 h-4" />
                            Novo passageiro
                        </Link>
                        <Link
                            :href="route('responsavel.marketplace')"
                            class="inline-flex items-center gap-2 rounded-xl border border-white/25 bg-white/10 px-4 py-2 text-sm font-semibold text-white hover:bg-white/20 transition"
                        >
                            <MagnifyingGlassIcon class="w-4 h-4" />
                            Buscar van
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Passageiros: agrupados por status -->
        <template v-if="passageiros.length">

            <!-- Com van ativa -->
            <div v-if="passageirosAtivos.length">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                    <p class="text-xs font-semibold text-slate-500">
                        Com van ativa
                        <span class="font-normal text-slate-400 ml-1">{{ passageirosAtivos.length }}</span>
                    </p>
                </div>
                <div class="grid gap-3 lg:grid-cols-2">
                    <CardPassageiro
                        v-for="p in passageirosAtivos"
                        :key="p.id_passageiro"
                        :passageiro="p"
                        modo="inicio"
                        @buscar-van="emit('buscar-van')"
                    />
                </div>
            </div>

            <!-- Aguardando confirmação -->
            <div v-if="passageirosAguard.length" :class="passageirosAtivos.length ? 'mt-6' : ''">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse shrink-0"></span>
                    <p class="text-xs font-semibold text-slate-500">
                        Aguardando confirmação
                        <span class="font-normal text-slate-400 ml-1">{{ passageirosAguard.length }}</span>
                    </p>
                </div>
                <div class="grid gap-3 lg:grid-cols-2">
                    <CardPassageiro
                        v-for="p in passageirosAguard"
                        :key="p.id_passageiro"
                        :passageiro="p"
                        modo="inicio"
                        @buscar-van="emit('buscar-van')"
                    />
                </div>
            </div>

            <!-- Sem van -->
            <div v-if="passageirosSemVan.length" :class="(passageirosAtivos.length || passageirosAguard.length) ? 'mt-6' : ''">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-2 h-2 rounded-full bg-slate-300 shrink-0"></span>
                    <p class="text-xs font-semibold text-slate-500">
                        Sem van
                        <span class="font-normal text-slate-400 ml-1">{{ passageirosSemVan.length }}</span>
                    </p>
                </div>
                <div class="grid gap-3 lg:grid-cols-2">
                    <CardPassageiro
                        v-for="p in passageirosSemVan"
                        :key="p.id_passageiro"
                        :passageiro="p"
                        modo="inicio"
                        @buscar-van="emit('buscar-van')"
                    />
                </div>
            </div>

        </template>

        <!-- Empty state -->
        <div v-else class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-blue-200 bg-blue-50/50 py-14 px-6 text-center">
            <div class="w-12 h-12 rounded-2xl bg-blue-100 flex items-center justify-center mb-4">
                <UserPlusIcon class="w-6 h-6 text-blue-500" />
            </div>
            <p class="font-semibold text-slate-800">Nenhum passageiro ainda</p>
            <p class="mt-1 text-sm text-slate-500 max-w-xs">Cadastre o primeiro passageiro para começar a organizar o transporte.</p>
            <Link
                :href="route('responsavel.passageiros.adicionar')"
                class="mt-5 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition shadow-sm"
            >
                <UserPlusIcon class="w-4 h-4" />
                Cadastrar passageiro
            </Link>
        </div>

    </div>
</template>
