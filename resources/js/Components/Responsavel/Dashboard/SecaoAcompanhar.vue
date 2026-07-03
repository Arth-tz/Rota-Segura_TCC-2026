<script setup>
import { ref, onMounted, onUnmounted, watch, nextTick } from 'vue'
import axios from 'axios'
import {
    MapPinIcon,
    TruckIcon,
    UserIcon,
    CheckCircleIcon,
    ClockIcon,
    SignalIcon,
    ArrowPathIcon,
} from '@heroicons/vue/24/outline'
import 'leaflet/dist/leaflet.css'
import L from 'leaflet'

// Fix Leaflet ícones padrão (bug com webpack/vite)
delete L.Icon.Default.prototype._getIconUrl
L.Icon.Default.mergeOptions({
    iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
    iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
    shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
})

// ── Estado ────────────────────────────────────────────────────────────────────

const passageiros = ref([])
const carregando  = ref(true)
const erro        = ref(null)
const atualizado  = ref(null)

let mapa       = null
const mapDiv   = ref(null)
let pollingId  = null

// Marcadores por passageiro (van + embarque + desembarque)
const marcadoresVan  = {}
const marcadoresEmb  = {}
const marcadoresDesemb = {}

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

function iconEmbarque(cor = '#10b981') {
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

async function buscarDados() {
    try {
        const { data } = await axios.get(route('responsavel.acompanhar'))
        passageiros.value = data.passageiros
        atualizado.value  = new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit', second: '2-digit' })
        atualizarMapa()
        erro.value = null
    } catch {
        erro.value = 'Erro ao atualizar posições.'
    } finally {
        carregando.value = false
    }
}

function iniciarPolling() {
    buscarDados()
    pollingId = setInterval(buscarDados, 5000)
}

function pararPolling() {
    if (pollingId) clearInterval(pollingId)
}

// ── Mapa ──────────────────────────────────────────────────────────────────────

function iniciarMapa() {
    if (!mapDiv.value || mapa) return

    mapa = L.map(mapDiv.value, { zoomControl: true }).setView([-29.9, -51.17], 13)

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19,
    }).addTo(mapa)
}

function atualizarMapa() {
    if (!mapa) return

    const bounds = []

    passageiros.value.forEach(p => {
        const id = p.id_passageiro

        // Van
        if (p.posicao_van?.latitude && p.posicao_van?.longitude) {
            const latlng = [p.posicao_van.latitude, p.posicao_van.longitude]
            bounds.push(latlng)

            if (marcadoresVan[id]) {
                marcadoresVan[id].setLatLng(latlng)
            } else {
                marcadoresVan[id] = L.marker(latlng, { icon: iconVan })
                    .addTo(mapa)
                    .bindPopup(`<b>Van</b><br>${p.nome}`)
            }
        } else if (marcadoresVan[id]) {
            mapa.removeLayer(marcadoresVan[id])
            delete marcadoresVan[id]
        }

        // Embarque
        if (p.embarque?.latitude && p.embarque?.longitude) {
            const latlng = [p.embarque.latitude, p.embarque.longitude]
            bounds.push(latlng)

            if (!marcadoresEmb[id]) {
                marcadoresEmb[id] = L.marker(latlng, { icon: iconEmbarque('#10b981') })
                    .addTo(mapa)
                    .bindPopup(`<b>Embarque — ${p.nome}</b><br>${p.embarque.logradouro}, ${p.embarque.bairro}`)
            }
        }

        // Desembarque
        if (p.desembarque?.latitude && p.desembarque?.longitude) {
            const latlng = [p.desembarque.latitude, p.desembarque.longitude]
            bounds.push(latlng)

            if (!marcadoresDesemb[id]) {
                const nome = p.desembarque.nome ? `${p.desembarque.nome} — ` : ''
                marcadoresDesemb[id] = L.marker(latlng, { icon: iconEmbarque('#3b82f6') })
                    .addTo(mapa)
                    .bindPopup(`<b>${nome}${p.nome}</b><br>${p.desembarque.logradouro}, ${p.desembarque.bairro}`)
            }
        }
    })

    if (bounds.length) {
        mapa.fitBounds(bounds, { padding: [40, 40], maxZoom: 15 })
    }
}

// ── Lifecycle ─────────────────────────────────────────────────────────────────

onMounted(async () => {
    await nextTick()
    iniciarMapa()
    iniciarPolling()
})

onUnmounted(() => {
    pararPolling()
    if (mapa) { mapa.remove(); mapa = null }
})

// ── Helpers ───────────────────────────────────────────────────────────────────

const STATUS_LABEL = {
    sem_rota:   'Sem rota hoje',
    aguardando: 'Aguardando van',
    a_bordo:    'A bordo',
    chegou:     'Chegou',
}
const STATUS_COR = {
    sem_rota:   'text-slate-400 bg-slate-100',
    aguardando: 'text-amber-700 bg-amber-100',
    a_bordo:    'text-blue-700 bg-blue-100',
    chegou:     'text-emerald-700 bg-emerald-100',
}
</script>

<template>
    <div class="space-y-4">

        <!-- Header -->
        <div class="rounded-2xl bg-gradient-to-br from-blue-600 to-blue-700 p-5 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold" style="font-family:'Sora',sans-serif;">Acompanhar trajeto</h3>
                    <p class="text-sm text-blue-200 mt-1">Posição da van em tempo real.</p>
                </div>
                <div v-if="atualizado" class="flex items-center gap-1 text-xs text-blue-200">
                    <ArrowPathIcon class="w-3.5 h-3.5 animate-spin" />
                    {{ atualizado }}
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

            <!-- Cards de status dos passageiros -->
            <div v-if="passageiros.length" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div v-for="p in passageiros" :key="p.id_passageiro"
                    class="rounded-2xl border border-slate-200 bg-white p-4 flex items-center gap-3 shadow-sm">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                        <UserIcon class="w-5 h-5 text-blue-600" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 truncate">{{ p.nome }}</p>
                        <div v-if="p.posicao_van" class="flex items-center gap-1 mt-0.5">
                            <SignalIcon class="w-3 h-3 text-amber-500" />
                            <p class="text-xs text-slate-400">Van a caminho</p>
                        </div>
                    </div>
                    <span class="shrink-0 text-xs font-semibold px-2 py-1 rounded-full"
                        :class="STATUS_COR[p.status] ?? 'text-slate-400 bg-slate-100'">
                        {{ STATUS_LABEL[p.status] ?? p.status }}
                    </span>
                </div>
            </div>

            <!-- Empty state — nenhum passageiro -->
            <div v-else
                class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-blue-200 bg-blue-50/40 py-14 px-6 text-center">
                <TruckIcon class="w-10 h-10 text-blue-300 mb-3" />
                <p class="font-semibold text-slate-700" style="font-family:'Sora',sans-serif;">Nenhum passageiro cadastrado</p>
                <p class="mt-1 text-sm text-slate-400">Adicione passageiros para acompanhar os trajetos aqui.</p>
            </div>

            <!-- Mapa Leaflet -->
            <div v-if="passageiros.some(p => p.posicao_van || p.embarque || p.desembarque)"
                class="rounded-2xl overflow-hidden border border-slate-200 shadow-sm">
                <div ref="mapDiv" style="height: 400px;"></div>
                <div class="px-4 py-2 bg-white border-t border-slate-100 flex gap-4 text-xs text-slate-500">
                    <span class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-amber-400 inline-block"></span> Van
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded bg-emerald-500 inline-block"></span> Embarque
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded bg-blue-500 inline-block"></span> Desembarque
                    </span>
                </div>
            </div>

            <!-- Aviso: sem rota ativa -->
            <div v-else-if="passageiros.length"
                class="rounded-2xl border border-slate-200 bg-slate-50 px-5 py-6 text-center">
                <MapPinIcon class="w-8 h-8 text-slate-300 mx-auto mb-2" />
                <p class="text-sm font-medium text-slate-500">Nenhuma van em rota no momento.</p>
                <p class="text-xs text-slate-400 mt-1">O mapa aparece quando o motorista iniciar o trajeto.</p>
            </div>

        </template>

    </div>
</template>
