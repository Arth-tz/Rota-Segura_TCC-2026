<script setup>
import { ref, watch, onMounted, onUnmounted, nextTick } from 'vue'
import axios from 'axios'
import 'leaflet/dist/leaflet.css'
import L from 'leaflet'
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
    ExclamationTriangleIcon,
    XMarkIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
    trajetos: { type: Array, default: () => [] },
    ativa:    { type: Boolean, default: false },
})

// Quando a seção volta a ficar visível, corrige dimensões do mapa Leaflet
watch(() => props.ativa, async (visivel) => {
    if (!visivel) return
    await nextTick()
    // Inicializa mapas de rotas ativas que ainda não foram inicializados
    for (const t of listas.value) {
        if (t.rota_ativa && !mapas[t.id_disponibilidade]) {
            await inicializarMapa(t)
        }
    }
    // Corrige tamanho de mapas já existentes (container estava display:none)
    Object.values(mapas).forEach(m => m.mapa.invalidateSize())
})

const TURNOS = { manha: 'Manhã', tarde: 'Tarde', integral: 'Integral' }

function turnoIcon(t) {
    return t === 'manha' ? SunIcon : t === 'tarde' ? MoonIcon : ClockIcon
}

// Mesma janela heurística usada no lado do responsável (AcompanharController::estaAtrasado)
function trajetoAtrasado(trajeto) {
    if (trajeto.rota_ativa || !trajeto.passageiros.length) return false
    const agora = new Date().toTimeString().slice(0, 5)
    if (trajeto.turno === 'manha') return agora > '09:30'
    if (trajeto.turno === 'tarde') return agora > '15:00'
    return false
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
    gpsFalhasConsecutivas: 0,
    gpsAlerta: false,
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

const mapDivs = {}  // id_disponibilidade -> elemento DOM
const mapas   = {}  // id_disponibilidade -> { mapa, marcadorVan, marcadoresParadas: {} }

const GPS_FALHAS_PARA_ALERTA = 3

function iniciarGPS(trajeto) {
    if (!navigator.geolocation) {
        trajeto.gpsAlerta = true
        return
    }

    const rotaId = trajeto.rota_ativa?.id_rota
    if (!rotaId) return

    const id = navigator.geolocation.watchPosition(
        pos => enviarPosicao(trajeto, rotaId, pos),
        () => registrarFalhaGPS(trajeto),
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

function registrarFalhaGPS(trajeto) {
    trajeto.gpsFalhasConsecutivas++
    if (trajeto.gpsFalhasConsecutivas >= GPS_FALHAS_PARA_ALERTA) {
        trajeto.gpsAlerta = true
    }
}

async function enviarPosicao(trajeto, rotaId, pos) {
    try {
        await axios.post(route('motorista.rotas.posicao', rotaId), {
            latitude:  pos.coords.latitude,
            longitude: pos.coords.longitude,
            precisao:  pos.coords.accuracy,
        })
        trajeto.gpsFalhasConsecutivas = 0
        trajeto.gpsAlerta = false
        atualizarPosicaoMapa(trajeto, pos.coords.latitude, pos.coords.longitude)
    } catch {
        registrarFalhaGPS(trajeto)
    }
}

onUnmounted(() => {
    Object.keys(watchIds.value).forEach(did => pararGPS(did))
    Object.keys(mapas).forEach(did => destruirMapa(Number(did)))
    window.removeEventListener('online', atualizarOnline)
    window.removeEventListener('offline', atualizarOnline)
})

// ── Mapa (motorista) ──────────────────────────────────────────────────────────

function criarIconeVan() {
    return L.divIcon({
        html: `<div style="background:#d97706;border:3px solid #fff;border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,.3);"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="white" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/></svg></div>`,
        className: '',
        iconSize: [28, 28],
        iconAnchor: [14, 14],
    })
}

function criarIconeParada(tipo, feita, proxima = false) {
    const cor = feita ? '#94a3b8' : proxima ? '#f59e0b' : tipo === 'embarque' ? '#10b981' : '#3b82f6'
    const size = proxima && !feita ? 24 : 20
    const borda = proxima && !feita ? '3px solid #f59e0b' : '2px solid #fff'
    const svgPath = feita
        ? '<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>'
        : '<path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>'
    return L.divIcon({
        html: `<div style="background:${cor};border:${borda};border-radius:4px;width:${size}px;height:${size}px;display:flex;align-items:center;justify-content:center;box-shadow:0 1px 4px rgba(0,0,0,.25);"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="white" style="width:11px;height:11px;">${svgPath}</svg></div>`,
        className: '',
        iconSize: [size, size],
        iconAnchor: [size / 2, size / 2],
    })
}

async function inicializarMapa(trajeto) {
    await nextTick()
    const did = trajeto.id_disponibilidade
    if (!mapDivs[did] || mapas[did]) return
    // Adia se o container estiver oculto (seção com display:none)
    if (mapDivs[did].offsetHeight === 0) return

    const paradas = trajeto.rota_ativa?.paradas ?? []
    const coords = paradas
        .filter(p => p.endereco?.latitude && p.endereco?.longitude)
        .map(p => [p.endereco.latitude, p.endereco.longitude])

    const centro = coords.length > 0 ? coords[0] : [-30.03, -51.23]
    const mapa = L.map(mapDivs[did], { zoomControl: true, attributionControl: false })
        .setView(centro, 14)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(mapa)
    mapas[did] = { mapa, marcadorVan: null, marcadoresParadas: {} }

    if (coords.length > 1) {
        mapa.fitBounds(coords, { padding: [36, 36], maxZoom: 16 })
    } else if (coords.length === 1) {
        mapa.setView(coords[0], 15)
    }

    atualizarMarcadoresParadas(trajeto)
}

function atualizarPosicaoMapa(trajeto, lat, lng) {
    const did = trajeto.id_disponibilidade
    const m = mapas[did]
    if (!m) return
    const latlng = [lat, lng]
    if (m.marcadorVan) {
        m.marcadorVan.setLatLng(latlng)
    } else {
        m.marcadorVan = L.marker(latlng, { icon: criarIconeVan(), zIndexOffset: 200 })
            .addTo(m.mapa)
            .bindPopup('<b>Minha posição</b>')
    }
    if (!m.mapa.getBounds().contains(latlng)) {
        m.mapa.panTo(latlng, { animate: true, duration: 0.5 })
    }
}

function atualizarMarcadoresParadas(trajeto) {
    const did = trajeto.id_disponibilidade
    const m = mapas[did]
    if (!m || !trajeto.rota_ativa) return
    const proximaOrdem = proximaParadaOrdem(trajeto)
    trajeto.rota_ativa.paradas.forEach(parada => {
        if (!parada.endereco?.latitude || !parada.endereco?.longitude) return
        const feita = paradaCompleta(parada)
        const proxima = parada.ordem === proximaOrdem
        const latlng = [parada.endereco.latitude, parada.endereco.longitude]
        if (m.marcadoresParadas[parada.id_parada]) {
            m.marcadoresParadas[parada.id_parada]
                .setIcon(criarIconeParada(parada.tipo, feita, proxima))
                .setZIndexOffset(proxima ? 100 : 0)
        } else {
            const label = parada.tipo === 'embarque' ? 'Embarque' : 'Desembarque'
            const nomes = parada.passageiros.map(p => p.nome).join(', ')
            const e = parada.endereco
            m.marcadoresParadas[parada.id_parada] = L.marker(latlng, {
                icon: criarIconeParada(parada.tipo, feita, proxima),
                zIndexOffset: proxima ? 100 : 0,
            }).addTo(m.mapa)
              .bindPopup(`<b>${label} — parada ${parada.ordem}</b><br>${nomes}<br><small>${e.logradouro}${e.numero ? ', ' + e.numero : ''} — ${e.bairro}</small>`)
        }
    })
}

function destruirMapa(did) {
    if (mapas[did]) {
        mapas[did].mapa.remove()
        delete mapas[did]
    }
    delete mapDivs[did]
}

// ── Modal de confirmação de encerramento ──────────────────────────────────────

const modalEncerrar  = ref(false)
const trajetoAlvoIdx = ref(null)
const pendentesModal = ref(0)

function abrirModalEncerrar(idx, pendentes) {
    trajetoAlvoIdx.value = idx
    pendentesModal.value = pendentes
    modalEncerrar.value  = true
}

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
        inicializarMapa(trajeto)
    } catch {
        trajeto.erro = 'Não foi possível iniciar o trajeto.'
    } finally {
        trajeto.iniciando = false
    }
}

function progressoTrajeto(trajeto) {
    if (!trajeto.rota_ativa) return { concluidas: 0, total: 0 }
    const desembarques = trajeto.rota_ativa.paradas.filter(p => p.tipo === 'desembarque').flatMap(p => p.passageiros)
    return { concluidas: desembarques.filter(p => p.desembarque_em).length, total: desembarques.length }
}

function paradaCompleta(parada) {
    return parada.passageiros.every(p => parada.tipo === 'embarque' ? p.embarque_em : p.desembarque_em)
}

function proximaParadaOrdem(trajeto) {
    if (!trajeto.rota_ativa) return null
    return trajeto.rota_ativa.paradas.find(p => !paradaCompleta(p))?.ordem ?? null
}

async function encerrarTrajeto(trajetoIdx) {
    const trajeto = listas.value[trajetoIdx]
    if (!trajeto.rota_ativa) return

    const { concluidas, total } = progressoTrajeto(trajeto)
    const pendentes = total - concluidas
    if (pendentes > 0) {
        abrirModalEncerrar(trajetoIdx, pendentes)
        return
    }

    await executarEncerramento(trajetoIdx)
}

async function executarEncerramento(trajetoIdx) {
    modalEncerrar.value = false
    const trajeto = listas.value[trajetoIdx]
    trajeto.encerrando = true
    try {
        await axios.post(route('motorista.rotas.encerrar', trajeto.rota_ativa.id_rota))
        pararGPS(trajeto.id_disponibilidade)
        destruirMapa(trajeto.id_disponibilidade)
        trajeto.rota_ativa = null
    } catch {
        trajeto.erro = 'Erro ao encerrar o trajeto.'
    } finally {
        trajeto.encerrando = false
    }
}

// ── Conectividade ──────────────────────────────────────────────────────────

const online = ref(navigator.onLine)
function atualizarOnline() { online.value = navigator.onLine }

onMounted(() => {
    window.addEventListener('online', atualizarOnline)
    window.addEventListener('offline', atualizarOnline)
})

// ── Confirmar embarque / desembarque ─────────────────────────────────────────

const confirmando = ref({})       // key: `${rotaId}-${paradaId}-${passageiroId}`
const errosConfirmacao = ref({})  // mesma key → mensagem de erro

async function confirmar(trajeto, parada, passageiro, tipo) {
    const key = `${trajeto.rota_ativa.id_rota}-${parada.id_parada}-${passageiro.id_passageiro}`
    if (confirmando.value[key]) return
    confirmando.value[key] = true
    delete errosConfirmacao.value[key]

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
        atualizarMarcadoresParadas(trajeto)
    } catch {
        errosConfirmacao.value[key] = online.value
            ? 'Não confirmou. Toque para tentar de novo.'
            : 'Sem internet — toque para tentar quando a conexão voltar.'
    } finally {
        delete confirmando.value[key]
    }
}

// Inicia GPS e mapa para trajetos já ativos ao montar
listas.value.forEach((t) => {
    if (t.rota_ativa) {
        iniciarGPS(t)
        inicializarMapa(t)
    }
})
</script>

<template>
    <div class="space-y-4">

        <!-- Header -->
        <div class="rounded-2xl bg-gradient-to-br from-amber-700 to-amber-800 p-5 text-white shadow-lg">
            <h3 class="text-lg font-bold">Meus trajetos</h3>
            <p class="text-sm text-amber-100 mt-1">Gerencie a ordem de embarque e inicie os trajetos de hoje.</p>
        </div>

        <!-- Sem internet -->
        <div v-if="!online"
            class="flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 font-medium">
            <ExclamationTriangleIcon class="w-4 h-4 shrink-0" />
            Sem conexão com a internet. Embarques, desembarques e localização não estão sendo enviados.
        </div>

        <!-- Empty state -->
        <div v-if="!listas.length"
            class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-amber-200 bg-amber-50/40 py-14 px-6 text-center">
            <MapPinIcon class="w-10 h-10 text-amber-300 mb-3" />
            <p class="font-semibold text-slate-700">Nenhum trajeto ativo</p>
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
                <span v-if="trajeto.rota_ativa && trajeto.gpsAlerta"
                    class="flex items-center gap-1 text-xs text-red-700 font-semibold bg-red-50 border border-red-200 rounded-full px-2 py-0.5">
                    <ExclamationTriangleIcon class="w-3.5 h-3.5" />
                    Localização não está sendo enviada
                </span>
                <span v-else-if="trajeto.rota_ativa" class="flex items-center gap-1 text-xs text-amber-700 font-medium animate-pulse">
                    <SignalIcon class="w-3.5 h-3.5" />
                    Em andamento
                </span>
                <span v-else-if="trajetoAtrasado(trajeto)"
                    class="flex items-center gap-1 text-xs text-red-600 font-semibold bg-red-50 border border-red-200 rounded-full px-2 py-0.5">
                    <ExclamationTriangleIcon class="w-3.5 h-3.5" />
                    Ainda não iniciado
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
                                :class="pi === 0 ? 'text-slate-200 cursor-not-allowed' : 'text-slate-500 hover:bg-slate-100 hover:text-amber-800'">
                                <ChevronUpIcon class="w-3.5 h-3.5" />
                            </button>
                            <button @click="mover(ti, pi, +1)"
                                :disabled="pi === trajeto.passageiros.length - 1 || trajeto.salvando"
                                class="w-6 h-6 rounded-lg flex items-center justify-center transition"
                                :class="pi === trajeto.passageiros.length - 1 ? 'text-slate-200 cursor-not-allowed' : 'text-slate-500 hover:bg-slate-100 hover:text-amber-800'">
                                <ChevronDownIcon class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    </li>
                </ul>

                <!-- Botão iniciar -->
                <div class="px-5 py-4 border-t border-slate-100">
                    <button @click="iniciarTrajeto(ti)"
                        :disabled="trajeto.iniciando || !trajeto.passageiros.length"
                        class="w-full flex items-center justify-center gap-2 py-3 rounded-xl font-bold text-sm transition disabled:opacity-50"
                        :class="trajeto.passageiros.length ? 'bg-amber-700 hover:bg-amber-800 text-white shadow-lg shadow-amber-900/20' : 'bg-amber-100 text-amber-600 cursor-not-allowed'">
                        <PlayIcon class="w-4 h-4" />
                        {{ trajeto.iniciando ? 'Iniciando…' : 'Iniciar trajeto' }}
                    </button>
                </div>
            </template>

            <!-- Modo trajeto ativo — paradas com confirmação -->
            <template v-else>

                <!-- Sem paradas (passageiros sem endereço cadastrado) -->
                <div v-if="!trajeto.rota_ativa.paradas.length"
                    class="px-5 py-8 text-center space-y-2">
                    <MapPinIcon class="w-8 h-8 text-amber-300 mx-auto" />
                    <p class="text-sm font-semibold text-slate-600">Nenhuma parada gerada</p>
                    <p class="text-xs text-slate-400 max-w-xs mx-auto">
                        Os passageiros deste trajeto não têm endereço de embarque cadastrado.
                        Peça aos responsáveis que adicionem os endereços antes de iniciar.
                    </p>
                </div>

                <template v-else>

                <!-- Progresso do trajeto -->
                <div class="px-5 pt-4 pb-1">
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-500 mb-1.5">
                        <span>Progresso</span>
                        <span>{{ progressoTrajeto(trajeto).concluidas }} de {{ progressoTrajeto(trajeto).total }} desembarcados</span>
                    </div>
                    <div class="h-1.5 rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full bg-amber-600 transition-all duration-300 ease-out"
                            :style="{ width: (progressoTrajeto(trajeto).total ? progressoTrajeto(trajeto).concluidas / progressoTrajeto(trajeto).total * 100 : 0) + '%' }">
                        </div>
                    </div>
                </div>

                <!-- Mapa em tempo real -->
                <div class="border-t border-amber-100">
                    <div :ref="el => { if (el) mapDivs[trajeto.id_disponibilidade] = el }"
                        style="height: 260px; position: relative; z-index: 0;"></div>
                    <div class="px-4 py-2 bg-white border-b border-amber-100 flex flex-wrap items-center gap-x-4 gap-y-1 text-[11px] text-slate-500">
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span> Minha van</span>
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded bg-emerald-500 shrink-0"></span> Embarque</span>
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded bg-blue-500 shrink-0"></span> Desembarque</span>
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded bg-slate-400 shrink-0"></span> Concluído</span>
                    </div>
                </div>

                <div class="divide-y divide-amber-100">
                    <div v-for="parada in trajeto.rota_ativa.paradas" :key="parada.id_parada"
                        class="px-5 py-4 transition-colors"
                        :class="{
                            'bg-amber-50/70': parada.ordem === proximaParadaOrdem(trajeto),
                            'opacity-55': paradaCompleta(parada) && parada.ordem !== proximaParadaOrdem(trajeto),
                        }">

                        <!-- Cabeçalho da parada -->
                        <div class="flex items-start gap-2 mb-3">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold shrink-0 mt-0.5"
                                :class="paradaCompleta(parada)
                                    ? 'bg-slate-100 text-slate-400'
                                    : parada.tipo === 'embarque' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700'">
                                <CheckIcon v-if="paradaCompleta(parada)" class="w-3.5 h-3.5" />
                                <template v-else>{{ parada.ordem }}</template>
                            </span>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-xs font-semibold uppercase tracking-wide"
                                        :class="parada.tipo === 'embarque' ? 'text-emerald-600' : 'text-blue-600'">
                                        {{ parada.tipo === 'embarque' ? 'Embarque' : 'Desembarque' }}
                                    </span>
                                    <span v-if="parada.ordem === proximaParadaOrdem(trajeto)"
                                        class="text-xs font-bold text-amber-700 bg-amber-100 rounded-full px-2 py-0.5">
                                        Próxima parada
                                    </span>
                                    <span v-else-if="parada.horario_real" class="text-xs text-slate-400">
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
                            <li v-for="pas in parada.passageiros" :key="pas.id_passageiro" class="space-y-1">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0 text-xs font-bold overflow-hidden"
                                        :class="parada.tipo === 'embarque' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700'">
                                        <img v-if="pas.foto_url" :src="pas.foto_url" class="w-full h-full object-cover" :alt="pas.nome" />
                                        <template v-else>{{ pas.nome?.charAt(0)?.toUpperCase() }}</template>
                                    </div>
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
                                        class="text-xs font-semibold px-3 py-2 rounded-lg transition disabled:opacity-50 shadow-sm"
                                        :class="errosConfirmacao[`${trajeto.rota_ativa.id_rota}-${parada.id_parada}-${pas.id_passageiro}`]
                                            ? 'bg-red-500 hover:bg-red-600 text-white'
                                            : parada.tipo === 'embarque'
                                                ? 'bg-emerald-500 hover:bg-emerald-600 text-white'
                                                : 'bg-blue-500 hover:bg-blue-600 text-white'">
                                        {{ confirmando[`${trajeto.rota_ativa.id_rota}-${parada.id_parada}-${pas.id_passageiro}`]
                                            ? '…'
                                            : errosConfirmacao[`${trajeto.rota_ativa.id_rota}-${parada.id_parada}-${pas.id_passageiro}`]
                                                ? 'Tentar de novo'
                                                : (parada.tipo === 'embarque' ? 'Embarcou' : 'Chegou') }}
                                    </button>
                                </div>
                                <p v-if="errosConfirmacao[`${trajeto.rota_ativa.id_rota}-${parada.id_parada}-${pas.id_passageiro}`]"
                                    class="text-xs text-red-600 pl-9">
                                    {{ errosConfirmacao[`${trajeto.rota_ativa.id_rota}-${parada.id_parada}-${pas.id_passageiro}`] }}
                                </p>
                            </li>
                        </ul>
                    </div>
                </div>

                </template>

                <!-- Botão encerrar -->
                <div class="px-5 py-4 border-t border-amber-100 bg-amber-50/40">
                    <button @click="encerrarTrajeto(ti)"
                        :disabled="trajeto.encerrando"
                        class="w-full flex items-center justify-center gap-2 py-3 rounded-xl font-bold text-sm bg-slate-700 hover:bg-slate-800 text-white transition disabled:opacity-50">
                        <StopIcon class="w-4 h-4" />
                        {{ trajeto.encerrando ? 'Encerrando…' : 'Encerrar trajeto' }}
                    </button>
                </div>
            </template>

        </div>

    </div>

    <!-- Modal: confirmar encerramento com passageiros pendentes -->
    <Teleport to="body">
        <Transition name="fade">
            <div v-if="modalEncerrar"
                class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
                @click.self="modalEncerrar = false">
                <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl border border-slate-100">

                    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <ExclamationTriangleIcon class="w-5 h-5 text-amber-600 shrink-0" />
                            <h3 class="text-base font-bold text-slate-800">Encerrar trajeto?</h3>
                        </div>
                        <button @click="modalEncerrar = false"
                            class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition">
                            <XMarkIcon class="w-4 h-4 text-slate-500" />
                        </button>
                    </div>

                    <div class="px-5 py-4">
                        <p class="text-sm text-slate-600">
                            Ainda há
                            <span class="font-bold text-slate-800">{{ pendentesModal }} passageiro{{ pendentesModal > 1 ? 's' : '' }}</span>
                            sem confirmação de desembarque.
                        </p>
                        <p class="text-sm text-slate-500 mt-1">
                            Ao encerrar, eles serão marcados automaticamente pelo sistema.
                        </p>
                    </div>

                    <div class="px-5 pb-5 flex gap-3">
                        <button @click="modalEncerrar = false"
                            class="flex-1 py-3 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                            Cancelar
                        </button>
                        <button @click="executarEncerramento(trajetoAlvoIdx)"
                            class="flex-1 py-3 rounded-xl bg-slate-700 hover:bg-slate-800 text-white text-sm font-bold transition">
                            Encerrar mesmo assim
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>

</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
