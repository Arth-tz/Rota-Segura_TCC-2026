<script setup>
import { ref, reactive, onMounted, onUnmounted, nextTick } from 'vue'
import { Link } from '@inertiajs/vue3'
import axios from 'axios'
import {
    MapPinIcon,
    TruckIcon,
    UsersIcon,
    SignalIcon,
    ArrowPathIcon,
    ChatBubbleLeftRightIcon,
    ClockIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline'
import 'leaflet/dist/leaflet.css'
import L from 'leaflet'

delete L.Icon.Default.prototype._getIconUrl
L.Icon.Default.mergeOptions({
    iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
    iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
    shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
})

// ── Estado ────────────────────────────────────────────────────────────────────

const grupos     = ref([])
const semVan      = ref([])
const carregando = ref(true)
const erro        = ref(null)

let pollingId = null

// Um mapa Leaflet por van (não reativo — instâncias do Leaflet vivem fora do Vue)
const mapDivs = {}          // id_van -> elemento DOM
const mapas   = {}          // id_van -> { mapa, marcadorVan, marcadoresEmb: {}, marcadoresDesemb: {} }

// ── Ícones personalizados ─────────────────────────────────────────────────────

const iconVan = L.divIcon({
    html: `<div style="background:#f59e0b;border:3px solid #fff;border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,.3);">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="white" style="width:15px;height:15px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
            </svg>
           </div>`,
    className: '',
    iconSize: [28, 28],
    iconAnchor: [14, 14],
})

function iconParada(cor) {
    return L.divIcon({
        html: `<div style="background:${cor};border:2px solid #fff;border-radius:4px;width:20px;height:20px;display:flex;align-items:center;justify-content:center;box-shadow:0 1px 4px rgba(0,0,0,.25);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="white" style="width:11px;height:11px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                </svg>
               </div>`,
        className: '',
        iconSize: [20, 20],
        iconAnchor: [10, 10],
    })
}

// ── Polling ───────────────────────────────────────────────────────────────────

const POSICAO_DESATUALIZADA_MS = 30_000

function formatarHora(iso) {
    return new Date(iso).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })
}

function posicaoDesatualizada(grupo) {
    if (!grupo.posicao_van?.capturada_em) return false
    return Date.now() - new Date(grupo.posicao_van.capturada_em).getTime() > POSICAO_DESATUALIZADA_MS
}

async function buscarDados() {
    try {
        const { data } = await axios.get(route('responsavel.acompanhar'))
        grupos.value = data.grupos
        semVan.value = data.sem_van
        erro.value = null
    } catch {
        erro.value = 'Erro ao atualizar posições.'
    } finally {
        carregando.value = false
        await nextTick()
        grupos.value.forEach(atualizarMapaGrupo)
        limparMapasOrfaos()
    }
}

function iniciarPolling() {
    buscarDados()
    pollingId = setInterval(buscarDados, 5000)
}

function pararPolling() {
    if (pollingId) clearInterval(pollingId)
}

// ── Mapa (um por van ativa) ────────────────────────────────────────────────────

function atualizarMapaGrupo(grupo) {
    const idVan = grupo.van.id_van
    if (!grupo.posicao_van || !mapDivs[idVan]) return

    if (!mapas[idVan]) {
        const mapa = L.map(mapDivs[idVan], { zoomControl: true })
            .setView([grupo.posicao_van.latitude, grupo.posicao_van.longitude], 14)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19,
        }).addTo(mapa)
        mapas[idVan] = { mapa, marcadorVan: null, marcadoresEmb: {}, marcadoresDesemb: {} }
    }

    const m = mapas[idVan]
    const bounds = []

    const latlngVan = [grupo.posicao_van.latitude, grupo.posicao_van.longitude]
    bounds.push(latlngVan)
    if (m.marcadorVan) {
        m.marcadorVan.setLatLng(latlngVan)
    } else {
        m.marcadorVan = L.marker(latlngVan, { icon: iconVan }).addTo(m.mapa).bindPopup(`<b>${grupo.van.nome_servico || grupo.van.placa}</b>`)
    }

    grupo.passageiros.forEach(p => {
        if (p.embarque?.latitude && p.embarque?.longitude && !m.marcadoresEmb[p.id_passageiro] && ['aguardando'].includes(p.status)) {
            const latlng = [p.embarque.latitude, p.embarque.longitude]
            bounds.push(latlng)
            m.marcadoresEmb[p.id_passageiro] = L.marker(latlng, { icon: iconParada('#10b981') })
                .addTo(m.mapa).bindPopup(`<b>Embarque — ${p.nome}</b><br>${p.embarque.logradouro}, ${p.embarque.bairro}`)
        }
        if (p.desembarque?.latitude && p.desembarque?.longitude && !m.marcadoresDesemb[p.id_passageiro] && ['aguardando', 'a_bordo'].includes(p.status)) {
            const latlng = [p.desembarque.latitude, p.desembarque.longitude]
            bounds.push(latlng)
            const nome = p.desembarque.nome ? `${p.desembarque.nome} — ` : ''
            m.marcadoresDesemb[p.id_passageiro] = L.marker(latlng, { icon: iconParada('#3b82f6') })
                .addTo(m.mapa).bindPopup(`<b>${nome}${p.nome}</b><br>${p.desembarque.logradouro}, ${p.desembarque.bairro}`)
        }
    })

    if (bounds.length > 1) {
        m.mapa.fitBounds(bounds, { padding: [36, 36], maxZoom: 15 })
    }
}

// Remove instâncias de mapa de vans que saíram da lista (ex: rota foi encerrada)
function limparMapasOrfaos() {
    const idsAtivos = new Set(grupos.value.filter(g => g.posicao_van).map(g => g.van.id_van))
    Object.keys(mapas).forEach(id => {
        if (!idsAtivos.has(Number(id))) {
            mapas[id].mapa.remove()
            delete mapas[id]
        }
    })
}

// ── Lifecycle ─────────────────────────────────────────────────────────────────

onMounted(() => iniciarPolling())
onUnmounted(() => {
    pararPolling()
    Object.values(mapas).forEach(m => m.mapa.remove())
})

// ── Helpers de exibição ────────────────────────────────────────────────────────

function whatsappUrl(telefone) {
    const num = (telefone ?? '').replace(/\D/g, '')
    const msg = encodeURIComponent('Olá! Estou acompanhando o trajeto pelo Rota Segura e gostaria de falar com você.')
    return `https://wa.me/55${num}?text=${msg}`
}

const STATUS_GRUPO = {
    em_andamento:              { label: 'Em andamento',            classes: 'bg-amber-100 text-amber-800 border-amber-200', pulse: true },
    aguardando_motorista:      { label: 'Aguardando início',        classes: 'bg-slate-100 text-slate-500 border-slate-200' },
    atrasado:                  { label: 'Ainda não iniciado',       classes: 'bg-red-50 text-red-600 border-red-200' },
    concluido:                 { label: 'Concluído hoje',           classes: 'bg-emerald-100 text-emerald-700 border-emerald-200' },
    concluido_sem_confirmacao: { label: 'Encerrado sem confirmação', classes: 'bg-red-100 text-red-700 border-red-200' },
    sem_horario_hoje:          { label: 'Sem aula hoje',            classes: 'bg-slate-100 text-slate-400 border-slate-200' },
}

const STATUS_PASSAGEIRO = {
    falta_hoje:                { label: 'Falta registrada',        classes: 'text-slate-500 bg-slate-100' },
    sem_horario_hoje:          { label: 'Sem aula hoje',            classes: 'text-slate-400 bg-slate-100' },
    aguardando_motorista:      { label: 'Aguardando início',        classes: 'text-slate-500 bg-slate-100' },
    atrasado:                  { label: 'Ainda não iniciado',       classes: 'text-red-600 bg-red-50' },
    aguardando:                { label: 'Van a caminho',            classes: 'text-amber-700 bg-amber-100' },
    a_bordo:                   { label: 'A bordo',                  classes: 'text-blue-700 bg-blue-100' },
    chegou:                     { label: 'Chegou',                   classes: 'text-emerald-700 bg-emerald-100' },
    concluido:                  { label: 'Concluído',                classes: 'text-emerald-700 bg-emerald-100' },
    concluido_sem_confirmacao:  { label: 'Sem confirmação de chegada', classes: 'text-red-700 bg-red-100' },
}
</script>

<template>
    <div class="space-y-4">

        <!-- Header -->
        <div class="rounded-2xl bg-gradient-to-br from-blue-600 to-blue-700 p-5 text-white shadow-lg">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-lg font-bold">Acompanhar trajeto</h3>
                    <p class="text-sm text-blue-200 mt-1">Posição da van em tempo real, por veículo.</p>
                </div>
                <div v-if="grupos.some(g => g.status === 'em_andamento')"
                    class="flex items-center gap-1.5 text-xs text-blue-100 bg-white/10 rounded-full px-2.5 py-1 shrink-0">
                    <ArrowPathIcon class="w-3.5 h-3.5 animate-spin" />
                    Ao vivo
                </div>
            </div>
        </div>

        <!-- Erro -->
        <div v-if="erro" class="px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
            {{ erro }}
        </div>

        <!-- Carregando -->
        <div v-if="carregando" class="flex justify-center py-12">
            <div class="w-8 h-8 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
        </div>

        <template v-else>

            <!-- Empty state — nenhum passageiro cadastrado -->
            <div v-if="!grupos.length && !semVan.length"
                class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-blue-200 bg-blue-50/40 py-14 px-6 text-center">
                <TruckIcon class="w-10 h-10 text-blue-300 mb-3" />
                <p class="font-semibold text-slate-700">Nenhum passageiro cadastrado</p>
                <p class="mt-1 text-sm text-slate-400">Adicione passageiros para acompanhar os trajetos aqui.</p>
            </div>

            <!-- Um card por van -->
            <div v-for="grupo in grupos" :key="grupo.chave"
                class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden transition-all">

                <!-- Cabeçalho: van + motorista + status + whatsapp -->
                <div class="flex items-start justify-between gap-3 px-4 pt-4 pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center shrink-0">
                            <TruckIcon class="w-5 h-5 text-amber-600" />
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-900 text-sm truncate">{{ grupo.van.nome_servico || grupo.van.placa }}</p>
                            <p class="text-xs text-slate-400 truncate">{{ grupo.motorista.nome }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a v-if="grupo.motorista.telefone" :href="whatsappUrl(grupo.motorista.telefone)"
                            target="_blank" rel="noopener noreferrer"
                            class="w-8 h-8 rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 flex items-center justify-center transition"
                            title="Falar no WhatsApp">
                            <ChatBubbleLeftRightIcon class="w-4 h-4" />
                        </a>
                        <span class="flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full border"
                            :class="STATUS_GRUPO[grupo.status]?.classes">
                            <span v-if="STATUS_GRUPO[grupo.status]?.pulse" class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            {{ STATUS_GRUPO[grupo.status]?.label ?? grupo.status }}
                        </span>
                    </div>
                </div>

                <!-- Lista de passageiros nesta van -->
                <ul class="divide-y divide-slate-100">
                    <li v-for="p in grupo.passageiros" :key="p.id_passageiro" class="flex items-center gap-3 px-4 py-3">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center shrink-0 overflow-hidden">
                            <img v-if="p.foto_url" :src="p.foto_url" class="w-full h-full object-cover" :alt="p.nome" />
                            <span v-else class="text-blue-600 font-bold text-xs">{{ p.nome?.charAt(0)?.toUpperCase() }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-800 truncate">{{ p.nome }}</p>
                            <p v-if="p.status === 'aguardando' && p.paradas_restantes" class="text-xs text-slate-400 mt-0.5">
                                Faltam {{ p.paradas_restantes }} parada{{ p.paradas_restantes > 1 ? 's' : '' }} até o embarque
                            </p>
                            <p v-else-if="p.status === 'a_bordo' && p.paradas_restantes" class="text-xs text-slate-400 mt-0.5">
                                Faltam {{ p.paradas_restantes }} parada{{ p.paradas_restantes > 1 ? 's' : '' }} até o desembarque
                            </p>
                            <p v-else-if="p.status === 'falta_hoje'" class="text-xs text-slate-400 mt-0.5">
                                {{ p.motivo_falta ? `Motivo: ${p.motivo_falta}` : 'Sem detalhes informados' }}
                            </p>
                            <p v-else-if="p.status === 'concluido_sem_confirmacao'" class="text-xs text-red-500 mt-0.5">
                                O motorista encerrou o trajeto sem confirmar a chegada — vale confirmar direto com ele.
                            </p>
                        </div>
                        <span class="shrink-0 text-xs font-semibold px-2 py-1 rounded-full"
                            :class="STATUS_PASSAGEIRO[p.status]?.classes ?? 'text-slate-400 bg-slate-100'">
                            {{ STATUS_PASSAGEIRO[p.status]?.label ?? p.status }}
                        </span>
                    </li>
                </ul>

                <!-- Mapa — só quando há sinal de GPS -->
                <div v-if="grupo.posicao_van" class="border-t border-slate-100">
                    <div v-if="posicaoDesatualizada(grupo)"
                        class="flex items-center gap-1.5 px-4 py-2 bg-red-50 text-red-600 text-xs font-medium">
                        <SignalIcon class="w-3.5 h-3.5" />
                        Sinal desatualizado — última posição às {{ formatarHora(grupo.posicao_van.capturada_em) }}
                    </div>
                    <div :ref="el => mapDivs[grupo.van.id_van] = el" style="height: 300px; position: relative; z-index: 0;"></div>
                    <div class="px-4 py-2 bg-white flex items-center justify-between text-xs text-slate-500">
                        <div class="flex gap-4">
                            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-amber-400 inline-block"></span> Van</span>
                            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-emerald-500 inline-block"></span> Embarque</span>
                            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-blue-500 inline-block"></span> Desembarque</span>
                        </div>
                        <span v-if="!posicaoDesatualizada(grupo)">Atualizado às {{ formatarHora(grupo.posicao_van.capturada_em) }}</span>
                    </div>
                </div>

                <!-- Trajeto ativo mas sem sinal de GPS ainda -->
                <div v-else-if="grupo.status === 'em_andamento'"
                    class="flex items-center gap-2 px-4 py-3 border-t border-slate-100 bg-amber-50/60 text-amber-700 text-xs">
                    <ExclamationTriangleIcon class="w-4 h-4 shrink-0" />
                    O motorista iniciou o trajeto, mas o mapa ainda não recebeu a localização da van.
                </div>

            </div>

            <!-- Passageiros sem van vinculada -->
            <div v-if="semVan.length"
                class="rounded-2xl border border-dashed border-slate-300 bg-slate-50/60 px-4 py-4">
                <div class="flex items-center gap-2 mb-2">
                    <UsersIcon class="w-4 h-4 text-slate-400" />
                    <p class="text-sm font-semibold text-slate-600">Sem van vinculada</p>
                </div>
                <ul class="space-y-1.5 mb-3">
                    <li v-for="p in semVan" :key="p.id_passageiro" class="text-sm text-slate-500 pl-6">{{ p.nome }}</li>
                </ul>
                <Link :href="route('responsavel.marketplace')"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-700 border border-blue-200 hover:bg-blue-50 px-3 py-1.5 rounded-lg transition">
                    <MapPinIcon class="w-3.5 h-3.5" /> Buscar van no marketplace
                </Link>
            </div>

        </template>

    </div>
</template>
