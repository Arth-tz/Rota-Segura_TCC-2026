<script setup>
import { ref, onUnmounted } from 'vue'
import axios from 'axios'
import {
    MapPinIcon,
    ChevronUpIcon,
    ChevronDownIcon,
    CheckCircleIcon,
    XCircleIcon,
    ClockIcon,
    SunIcon,
    MoonIcon,
    PlayIcon,
    StopIcon,
    SignalIcon,
    CheckIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
    trajetos: { type: Array, default: () => [] },
})

const TURNOS = { manha: 'Manhã', tarde: 'Tarde', integral: 'Integral' }

function turnoIcon(t) {
    return t === 'manha' ? SunIcon : t === 'tarde' ? MoonIcon : ClockIcon
}

// Cópia local mutável — inclui rota_ativa por disponibilidade
const listas = ref(props.trajetos.map(t => ({
    ...t,
    passageiros: [...t.passageiros],
    rota_ativa: t.rota_ativa ?? null,
    salvando: false,
    iniciando: false,
    encerrando: false,
    erro: null,
})))

// ── Reordenar ─────────────────────────────────────────────────────────────────

function mover(trajetoIdx, pasIdx, direcao) {
    const lista = listas.value[trajetoIdx]
    if (lista.rota_ativa) return // não permite reordenar com rota ativa
    const arr = lista.passageiros
    const novoIdx = pasIdx + direcao
    if (novoIdx < 0 || novoIdx >= arr.length) return
    const tmp = arr[pasIdx]
    arr[pasIdx] = arr[novoIdx]
    arr[novoIdx] = tmp
    salvar(trajetoIdx)
}

async function salvar(trajetoIdx) {
    const trajeto = listas.value[trajetoIdx]
    trajeto.salvando = true
    trajeto.erro     = null
    try {
        await axios.post(
            route('motorista.disponibilidades.reordenar', trajeto.id_disponibilidade),
            { ordem: trajeto.passageiros.map(p => p.id_vinculo) },
        )
    } catch {
        trajeto.erro = 'Erro ao salvar. Tente novamente.'
    } finally {
        trajeto.salvando = false
    }
}

// ── GPS ───────────────────────────────────────────────────────────────────────

const watchIds = ref({}) // disponibilidadeId → watchId do navigator

function iniciarGPS(trajeto) {
    if (!navigator.geolocation) return

    const rotaId = trajeto.rota_ativa?.id_rota
    if (!rotaId) return

    const id = navigator.geolocation.watchPosition(
        pos => enviarPosicao(rotaId, pos),
        null,
        { enableHighAccuracy: true, maximumAge: 5000, timeout: 10000 },
    )
    watchIds.value[trajeto.id_disponibilidade] = id
}

function pararGPS(disponibilidadeId) {
    const id = watchIds.value[disponibilidadeId]
    if (id !== undefined) {
        navigator.geolocation.clearWatch(id)
        delete watchIds.value[disponibilidadeId]
    }
}

async function enviarPosicao(rotaId, pos) {
    try {
        await axios.post(route('motorista.rotas.posicao', rotaId), {
            latitude:  pos.coords.latitude,
            longitude: pos.coords.longitude,
            precisao:  pos.coords.accuracy,
        })
    } catch { /* ignora falhas silenciosamente */ }
}

onUnmounted(() => {
    Object.keys(watchIds.value).forEach(did => pararGPS(did))
})

// ── Iniciar / Encerrar ────────────────────────────────────────────────────────

async function iniciarTrajeto(trajetoIdx) {
    const trajeto = listas.value[trajetoIdx]
    trajeto.iniciando = true
    trajeto.erro = null
    try {
        const { data } = await axios.post(
            route('motorista.rotas.iniciar', trajeto.id_disponibilidade)
        )
        trajeto.rota_ativa = data.rota
        iniciarGPS(trajeto)
    } catch {
        trajeto.erro = 'Não foi possível iniciar o trajeto.'
    } finally {
        trajeto.iniciando = false
    }
}

async function encerrarTrajeto(trajetoIdx) {
    const trajeto = listas.value[trajetoIdx]
    if (!trajeto.rota_ativa) return
    trajeto.encerrando = true
    try {
        await axios.post(route('motorista.rotas.encerrar', trajeto.rota_ativa.id_rota))
        pararGPS(trajeto.id_disponibilidade)
        trajeto.rota_ativa = null
    } catch {
        trajeto.erro = 'Erro ao encerrar o trajeto.'
    } finally {
        trajeto.encerrando = false
    }
}

// ── Confirmar embarque / desembarque ─────────────────────────────────────────

const confirmando = ref({}) // key: `${rotaId}-${paradaId}-${passageiroId}`

async function confirmar(trajeto, parada, passageiro, tipo) {
    const key = `${trajeto.rota_ativa.id_rota}-${parada.id_parada}-${passageiro.id_passageiro}`
    if (confirmando.value[key]) return
    confirmando.value[key] = true

    try {
        const { data } = await axios.post(
            route('motorista.rotas.confirmar', {
                rotaId: trajeto.rota_ativa.id_rota,
                paradaId: parada.id_parada,
            }),
            { id_passageiro: passageiro.id_passageiro, tipo }
        )
        // Atualiza estado local
        const campo = tipo === 'embarque' ? 'embarque_em' : 'desembarque_em'
        passageiro[campo] = data.hora
        if (!parada.horario_real) parada.horario_real = data.hora
    } catch {
        // ignora — botão volta ao estado normal
    } finally {
        delete confirmando.value[key]
    }
}

// Inicia GPS para trajetos já ativos ao montar
listas.value.forEach((t) => {
    if (t.rota_ativa) iniciarGPS(t)
})
</script>

<template>
    <div class="space-y-4">

        <!-- Header -->
        <div class="rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 p-5 text-white shadow-lg">
            <h3 class="text-lg font-bold" style="font-family:'Sora',sans-serif;">Meus trajetos</h3>
            <p class="text-sm text-amber-100 mt-1">Gerencie a ordem de embarque e inicie os trajetos de hoje.</p>
        </div>

        <!-- Empty state -->
        <div v-if="!listas.length"
            class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-amber-200 bg-amber-50/40 py-14 px-6 text-center">
            <MapPinIcon class="w-10 h-10 text-amber-300 mb-3" />
            <p class="font-semibold text-slate-700" style="font-family:'Sora',sans-serif;">Nenhum trajeto ativo</p>
            <p class="mt-1 text-sm text-slate-400">Crie disponibilidades e aceite passageiros para ver os trajetos aqui.</p>
        </div>

        <!-- Um bloco por disponibilidade -->
        <div v-for="(trajeto, ti) in listas" :key="trajeto.id_disponibilidade"
            class="rounded-2xl border shadow-sm overflow-hidden"
            :class="trajeto.rota_ativa ? 'border-amber-300 bg-amber-50/20' : 'border-slate-200 bg-white'">

            <!-- Cabeçalho do trajeto -->
            <div class="flex items-center gap-3 px-5 py-3 border-b"
                :class="trajeto.rota_ativa ? 'bg-amber-50 border-amber-200' : 'bg-slate-50 border-slate-100'">
                <component :is="turnoIcon(trajeto.turno)"
                    class="w-4 h-4 shrink-0"
                    :class="trajeto.turno === 'manha' ? 'text-amber-500' : trajeto.turno === 'tarde' ? 'text-indigo-500' : 'text-slate-400'" />
                <p class="font-semibold text-slate-800 text-sm flex-1">{{ trajeto.nome }}</p>
                <span v-if="trajeto.rota_ativa" class="flex items-center gap-1 text-xs text-amber-700 font-medium animate-pulse">
                    <SignalIcon class="w-3.5 h-3.5" />
                    Em andamento
                </span>
                <span v-else class="text-xs text-slate-400">{{ trajeto.passageiros.length }} passageiro{{ trajeto.passageiros.length !== 1 ? 's' : '' }} hoje</span>
                <span v-if="trajeto.salvando" class="text-xs text-amber-600 animate-pulse">Salvando…</span>
                <span v-if="trajeto.erro" class="text-xs text-red-600">{{ trajeto.erro }}</span>
            </div>

            <!-- Lista de passageiros (modo ordem — sem rota ativa) -->
            <template v-if="!trajeto.rota_ativa">
                <div v-if="!trajeto.passageiros.length"
                    class="px-5 py-8 text-center text-sm text-slate-400">
                    Nenhum passageiro vai hoje neste trajeto.
                </div>

                <ul v-else class="divide-y divide-slate-100">
                    <li v-for="(p, pi) in trajeto.passageiros" :key="p.id_vinculo"
                        class="flex items-center gap-3 px-5 py-3 transition-colors"
                        :class="p.falta_hoje ? 'bg-red-50/60' : 'hover:bg-slate-50'">

                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold shrink-0"
                            :class="p.falta_hoje ? 'bg-red-100 text-red-400' : 'bg-amber-100 text-amber-700'">
                            {{ pi + 1 }}
                        </span>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-semibold text-slate-800 truncate">{{ p.nome }}</p>
                                <span class="shrink-0 flex items-center gap-1 text-xs px-1.5 py-0.5 rounded-full font-medium"
                                    :class="p.falta_hoje ? 'bg-red-100 text-red-600' : 'bg-emerald-100 text-emerald-700'">
                                    <component :is="p.falta_hoje ? XCircleIcon : CheckCircleIcon" class="w-3 h-3" />
                                    {{ p.falta_hoje ? 'Falta' : 'Vai' }}
                                </span>
                            </div>
                            <div v-if="p.embarque" class="flex items-center gap-1 mt-0.5">
                                <MapPinIcon class="w-3 h-3 text-emerald-500 shrink-0" />
                                <p class="text-xs text-slate-400 truncate">{{ p.embarque }}</p>
                            </div>
                        </div>

                        <!-- Botões ↑↓ -->
                        <div class="flex flex-col gap-0.5 shrink-0">
                            <button @click="mover(ti, pi, -1)"
                                :disabled="pi === 0 || trajeto.salvando"
                                class="w-6 h-6 rounded-lg flex items-center justify-center transition"
                                :class="pi === 0 ? 'text-slate-200 cursor-not-allowed' : 'text-slate-500 hover:bg-amber-100 hover:text-amber-700'">
                                <ChevronUpIcon class="w-3.5 h-3.5" />
                            </button>
                            <button @click="mover(ti, pi, +1)"
                                :disabled="pi === trajeto.passageiros.length - 1 || trajeto.salvando"
                                class="w-6 h-6 rounded-lg flex items-center justify-center transition"
                                :class="pi === trajeto.passageiros.length - 1 ? 'text-slate-200 cursor-not-allowed' : 'text-slate-500 hover:bg-amber-100 hover:text-amber-700'">
                                <ChevronDownIcon class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    </li>
                </ul>

                <!-- Botão iniciar -->
                <div class="px-5 py-4 border-t border-slate-100">
                    <button @click="iniciarTrajeto(ti)"
                        :disabled="trajeto.iniciando || !trajeto.passageiros.length"
                        class="w-full flex items-center justify-center gap-2 py-3 rounded-xl font-bold text-sm transition shadow-lg shadow-amber-200 disabled:opacity-50"
                        :class="trajeto.passageiros.length ? 'bg-amber-500 hover:bg-amber-600 text-white' : 'bg-slate-100 text-slate-400 cursor-not-allowed'"
                        style="font-family:'Sora',sans-serif;">
                        <PlayIcon class="w-4 h-4" />
                        {{ trajeto.iniciando ? 'Iniciando…' : 'Iniciar trajeto' }}
                    </button>
                </div>
            </template>

            <!-- Modo trajeto ativo — paradas com confirmação -->
            <template v-else>
                <div class="divide-y divide-amber-100">
                    <div v-for="parada in trajeto.rota_ativa.paradas" :key="parada.id_parada"
                        class="px-5 py-4">

                        <!-- Cabeçalho da parada -->
                        <div class="flex items-start gap-2 mb-3">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold shrink-0 mt-0.5"
                                :class="parada.tipo === 'embarque' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700'">
                                {{ parada.ordem }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-semibold uppercase tracking-wide"
                                        :class="parada.tipo === 'embarque' ? 'text-emerald-600' : 'text-blue-600'">
                                        {{ parada.tipo === 'embarque' ? 'Embarque' : 'Desembarque' }}
                                    </span>
                                    <span v-if="parada.horario_real" class="text-xs text-slate-400">
                                        {{ parada.horario_real }}
                                    </span>
                                </div>
                                <p v-if="parada.endereco" class="text-xs text-slate-500 truncate mt-0.5">
                                    {{ parada.endereco.logradouro }}{{ parada.endereco.numero ? ', ' + parada.endereco.numero : '' }}
                                    — {{ parada.endereco.bairro }}
                                </p>
                            </div>
                        </div>

                        <!-- Passageiros desta parada -->
                        <ul class="space-y-2 pl-8">
                            <li v-for="pas in parada.passageiros" :key="pas.id_passageiro"
                                class="flex items-center gap-2">
                                <p class="flex-1 text-sm font-medium text-slate-700 truncate">{{ pas.nome }}</p>

                                <!-- Confirmado -->
                                <span v-if="parada.tipo === 'embarque' ? pas.embarque_em : pas.desembarque_em"
                                    class="flex items-center gap-1 text-xs font-semibold text-emerald-600 px-2 py-1 bg-emerald-50 rounded-lg">
                                    <CheckIcon class="w-3.5 h-3.5" />
                                    {{ parada.tipo === 'embarque' ? pas.embarque_em : pas.desembarque_em }}
                                </span>

                                <!-- Botão confirmar -->
                                <button v-else
                                    @click="confirmar(trajeto, parada, pas, parada.tipo)"
                                    :disabled="confirmando[`${trajeto.rota_ativa.id_rota}-${parada.id_parada}-${pas.id_passageiro}`]"
                                    class="text-xs font-semibold px-3 py-1.5 rounded-lg transition disabled:opacity-50"
                                    :class="parada.tipo === 'embarque'
                                        ? 'bg-emerald-500 hover:bg-emerald-600 text-white'
                                        : 'bg-blue-500 hover:bg-blue-600 text-white'">
                                    {{ parada.tipo === 'embarque' ? 'Embarcou' : 'Chegou' }}
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Botão encerrar -->
                <div class="px-5 py-4 border-t border-amber-100 bg-amber-50/40">
                    <button @click="encerrarTrajeto(ti)"
                        :disabled="trajeto.encerrando"
                        class="w-full flex items-center justify-center gap-2 py-3 rounded-xl font-bold text-sm bg-slate-700 hover:bg-slate-800 text-white transition disabled:opacity-50"
                        style="font-family:'Sora',sans-serif;">
                        <StopIcon class="w-4 h-4" />
                        {{ trajeto.encerrando ? 'Encerrando…' : 'Encerrar trajeto' }}
                    </button>
                </div>
            </template>

        </div>

    </div>
</template>
