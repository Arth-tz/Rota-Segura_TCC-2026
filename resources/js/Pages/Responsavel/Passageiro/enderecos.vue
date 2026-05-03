<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

// ─── Form ───────────────────────────────────────────────────────────────────
const form = useForm({
    residencia_logradouro: '', residencia_numero: '', residencia_complemento: '',
    residencia_bairro: '', residencia_cidade: '', residencia_estado: '', residencia_cep: '',
    residencia_latitude: '', residencia_longitude: '',

    embarque_logradouro: '', embarque_numero: '', embarque_complemento: '',
    embarque_bairro: '', embarque_cidade: '', embarque_estado: '', embarque_cep: '',
    embarque_latitude: '', embarque_longitude: '',

    desembarque_logradouro: '', desembarque_numero: '', desembarque_complemento: '',
    desembarque_bairro: '', desembarque_cidade: '', desembarque_estado: '', desembarque_cep: '',
    desembarque_latitude: '', desembarque_longitude: '',

    destino_nome: '', destino_tipo: 'escola',
    destino_logradouro: '', destino_numero: '', destino_complemento: '',
    destino_bairro: '', destino_cidade: '', destino_estado: '', destino_cep: '',
    destino_latitude: '', destino_longitude: '',
})

// ─── Checkbox "usar residência como embarque" ────────────────────────────────
const embarqueIgualResidencia = ref(false)

watch(embarqueIgualResidencia, (val) => {
    if (val) {
        const prefixos = ['logradouro','numero','complemento','bairro','cidade','estado','cep','latitude','longitude']
        prefixos.forEach(p => form[`embarque_${p}`] = form[`residencia_${p}`])
    }
})

// ─── Autocomplete (Nominatim) ────────────────────────────────────────────────
const sugestoes = ref({})
const loadings  = ref({})
let debounceTimers = {}

async function buscarEndereco(prefixo, query) {
    if (!query || query.length < 4) { sugestoes.value[prefixo] = []; return }

    loadings.value[prefixo] = true
    clearTimeout(debounceTimers[prefixo])

    debounceTimers[prefixo] = setTimeout(async () => {
        try {
            const url = `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query)}&format=json&addressdetails=1&limit=5&countrycodes=br`
            const res  = await fetch(url, { headers: { 'Accept-Language': 'pt-BR' } })
            const data = await res.json()
            sugestoes.value[prefixo] = data
        } catch {
            sugestoes.value[prefixo] = []
        } finally {
            loadings.value[prefixo] = false
        }
    }, 500)
}

function selecionarSugestao(prefixo, item) {
    const addr = item.address || {}
    form[`${prefixo}_logradouro`]   = addr.road || addr.pedestrian || addr.footway || ''
    form[`${prefixo}_bairro`]       = addr.suburb || addr.neighbourhood || addr.city_district || ''
    form[`${prefixo}_cidade`]       = addr.city || addr.town || addr.village || ''
    form[`${prefixo}_estado`]       = addr.state_code || estadoParaUF(addr.state) || ''
    form[`${prefixo}_cep`]          = (addr.postcode || '').replace('-', '')
    form[`${prefixo}_latitude`]     = item.lat || ''
    form[`${prefixo}_longitude`]    = item.lon || ''
    sugestoes.value[prefixo] = []
}

function fecharSugestoes(prefixo) {
    setTimeout(() => { sugestoes.value[prefixo] = [] }, 200)
}

function maskCep(field, event) {
    let v = event.target.value.replace(/\D/g, '').slice(0, 8)
    if (v.length > 5) v = v.replace(/^(\d{5})(\d+)$/, '$1-$2')
    form[field] = v
}

const estadosUF = {
    'Acre': 'AC', 'Alagoas': 'AL', 'Amapá': 'AP', 'Amazonas': 'AM',
    'Bahia': 'BA', 'Ceará': 'CE', 'Distrito Federal': 'DF',
    'Espírito Santo': 'ES', 'Goiás': 'GO', 'Maranhão': 'MA',
    'Mato Grosso': 'MT', 'Mato Grosso do Sul': 'MS', 'Minas Gerais': 'MG',
    'Pará': 'PA', 'Paraíba': 'PB', 'Paraná': 'PR', 'Pernambuco': 'PE',
    'Piauí': 'PI', 'Rio de Janeiro': 'RJ', 'Rio Grande do Norte': 'RN',
    'Rio Grande do Sul': 'RS', 'Rondônia': 'RO', 'Roraima': 'RR',
    'Santa Catarina': 'SC', 'São Paulo': 'SP', 'Sergipe': 'SE',
    'Tocantins': 'TO'
}

function estadoParaUF(estado) {
    if (!estado) return ''
    return estadosUF[estado] || estado.substring(0, 2).toUpperCase()
}

function submit() {
    form.post(route('responsavel.passageiros.store'))
}
</script>

<template>
    <Head title="Endereços do Passageiro" />

    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Nunito:wght@300;400;500;600&display=swap" rel="stylesheet" />

    <div class="min-h-screen bg-slate-50 flex">

        <!-- Lado esquerdo -->
        <aside class="hidden lg:flex lg:w-4/12 bg-blue-700 flex-col justify-between p-12 rounded-tr-3xl rounded-br-3xl overflow-hidden bg-[radial-gradient(circle_at_top_left,_#3b82f6,_#1d4ed8_60%)] z-10 shadow-[10px_0_50px_-15px_rgba(0,0,0,0.3)] sticky top-0 h-screen">
            <Link href="/" class="flex items-center gap-2 hover:opacity-80 transition-all duration-300">
                <img src="/rota-segura/public/images/Logo_rota-segura_branco.png" alt="Rota Segura" class="h-12 w-12">
                <span class="text-white font-bold text-lg tracking-tight" style="font-family:'Sora',sans-serif;">Rota Segura</span>
            </Link>

            <div class="relative z-10">
                <!-- Indicador de etapas -->
                <div class="flex items-center gap-2 mb-6">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-white/30 flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-blue-200 text-sm line-through">Dados pessoais</span>
                    </div>
                    <div class="h-px w-6 bg-blue-400"></div>
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-white flex items-center justify-center">
                            <span class="text-blue-700 text-xs font-bold">2</span>
                        </div>
                        <span class="text-white text-sm font-semibold">Endereços</span>
                    </div>
                </div>

                <h2 class="text-4xl font-extrabold text-white leading-tight mb-4" style="font-family:'Sora',sans-serif;">
                    Última etapa!
                </h2>
                <p class="text-blue-100 text-sm leading-relaxed">
                    Informe os endereços do passageiro para que o motorista saiba exatamente onde buscar e deixar.
                </p>

                <div class="mt-8 space-y-3">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">📍</div>
                        <p class="text-blue-100 text-sm">Use a busca de endereço para preencher automaticamente</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">🔄</div>
                        <p class="text-blue-100 text-sm">Se o embarque for na residência, use o atalho de copiar</p>
                    </div>
                </div>
            </div>

            <p class="text-blue-200 text-xs">© 2026 Rota Segura.</p>
        </aside>

        <!-- Lado direito -->
        <main class="flex-1 flex items-start justify-center px-4 py-10 overflow-y-auto">
            <div class="w-full max-w-2xl">

                <!-- Header mobile -->
                <div class="lg:hidden flex items-center gap-2 mb-8">
                    <img src="/rota-segura/public/images/Logo_rota-segura_azul.png" alt="Rota Segura" class="h-10 w-10">
                    <span class="text-slate-900 font-bold" style="font-family:'Sora',sans-serif;">Rota Segura</span>
                </div>

                <h1 class="text-3xl font-bold text-slate-900 mb-1" style="font-family:'Sora',sans-serif;">Endereços</h1>
                <p class="text-slate-400 text-sm mb-8">Informe os endereços de referência do passageiro.</p>

                <form @submit.prevent="submit" class="space-y-6">

                    <!-- ── RESIDÊNCIA ─────────────────────────────────────── -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-lg"></span>
                            <h2 class="text-base font-semibold text-slate-800" style="font-family:'Sora',sans-serif;">Residência</h2>
                        </div>

                        <!-- Busca autocomplete -->
                        <div class="relative">
                            <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Buscar endereço</label>
                            <input
                                type="text"
                                placeholder="Digite rua, bairro ou cidade..."
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                @input="buscarEndereco('residencia', $event.target.value)"
                                @blur="fecharSugestoes('residencia')"
                            />
                            <div v-if="loadings['residencia']" class="absolute right-3 top-10">
                                <svg class="animate-spin h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                            </div>
                            <ul v-if="sugestoes['residencia']?.length" class="absolute z-20 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden">
                                <li
                                    v-for="s in sugestoes['residencia']" :key="s.place_id"
                                    @mousedown="selecionarSugestao('residencia', s)"
                                    class="px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 cursor-pointer border-b border-slate-100 last:border-0 transition"
                                >
                                    📍 {{ s.display_name }}
                                </li>
                            </ul>
                        </div>

                        <!-- Campos detalhados -->
                        <div class="grid grid-cols-3 gap-3">
                            <input v-model="form.residencia_logradouro" placeholder="Logradouro" required class="col-span-2 px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" :class="{'border-red-400': form.errors.residencia_logradouro}" />
                            <input v-model="form.residencia_numero" placeholder="Número" class="px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            <input v-model="form.residencia_complemento" placeholder="Complemento" class="col-span-2 px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            <input v-model="form.residencia_bairro" placeholder="Bairro" required class="px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            <input v-model="form.residencia_cidade" placeholder="Cidade" required class="px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            <input v-model="form.residencia_estado" placeholder="UF" maxlength="2" required class="px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition uppercase" />
                            <input v-model="form.residencia_cep" @input="maskCep('residencia_cep', $event)" placeholder="CEP" required class="px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                        </div>
                    </div>

                    <!-- ── EMBARQUE ────────────────────────────────────────── -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                        <div class="flex items-center justify-between mb-1">
                            <div class="flex items-center gap-2">
                                <span class="text-lg"></span>
                                <h2 class="text-base font-semibold text-slate-800" style="font-family:'Sora',sans-serif;">Embarque</h2>
                            </div>
                            <!-- Checkbox copiar residência -->
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" v-model="embarqueIgualResidencia" class="w-4 h-4 rounded accent-blue-600" />
                                <span class="text-xs text-slate-500">Igual à residência</span>
                            </label>
                        </div>

                        <div class="relative">
                            <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Buscar endereço</label>
                            <input
                                type="text"
                                placeholder="Digite rua, bairro ou cidade..."
                                :disabled="embarqueIgualResidencia"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition disabled:opacity-50 disabled:cursor-not-allowed"
                                @input="buscarEndereco('embarque', $event.target.value)"
                                @blur="fecharSugestoes('embarque')"
                            />
                            <div v-if="loadings['embarque']" class="absolute right-3 top-10">
                                <svg class="animate-spin h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                            </div>
                            <ul v-if="sugestoes['embarque']?.length" class="absolute z-20 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden">
                                <li
                                    v-for="s in sugestoes['embarque']" :key="s.place_id"
                                    @mousedown="selecionarSugestao('embarque', s)"
                                    class="px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 cursor-pointer border-b border-slate-100 last:border-0 transition"
                                >
                                    📍 {{ s.display_name }}
                                </li>
                            </ul>
                        </div>

                        <div class="grid grid-cols-3 gap-3" :class="{'opacity-50 pointer-events-none': embarqueIgualResidencia}">
                            <input v-model="form.embarque_logradouro" placeholder="Logradouro" required class="col-span-2 px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            <input v-model="form.embarque_numero" placeholder="Número" class="px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            <input v-model="form.embarque_complemento" placeholder="Complemento" class="col-span-2 px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            <input v-model="form.embarque_bairro" placeholder="Bairro" required class="px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            <input v-model="form.embarque_cidade" placeholder="Cidade" required class="px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            <input v-model="form.embarque_estado" placeholder="UF" maxlength="2" required class="px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition uppercase" />
                            <input v-model="form.embarque_cep" @input="maskCep('embarque_cep', $event)" placeholder="CEP" required class="px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                        </div>
                    </div>

                    <!-- ── DESEMBARQUE ─────────────────────────────────────── -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-lg"></span>
                            <h2 class="text-base font-semibold text-slate-800" style="font-family:'Sora',sans-serif;">Desembarque</h2>
                        </div>

                        <div class="relative">
                            <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Buscar endereço</label>
                            <input
                                type="text"
                                placeholder="Digite rua, bairro ou cidade..."
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                @input="buscarEndereco('desembarque', $event.target.value)"
                                @blur="fecharSugestoes('desembarque')"
                            />
                            <div v-if="loadings['desembarque']" class="absolute right-3 top-10">
                                <svg class="animate-spin h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                            </div>
                            <ul v-if="sugestoes['desembarque']?.length" class="absolute z-20 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden">
                                <li
                                    v-for="s in sugestoes['desembarque']" :key="s.place_id"
                                    @mousedown="selecionarSugestao('desembarque', s)"
                                    class="px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 cursor-pointer border-b border-slate-100 last:border-0 transition"
                                >
                                    📍 {{ s.display_name }}
                                </li>
                            </ul>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <input v-model="form.desembarque_logradouro" placeholder="Logradouro" required class="col-span-2 px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            <input v-model="form.desembarque_numero" placeholder="Número" class="px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            <input v-model="form.desembarque_complemento" placeholder="Complemento" class="col-span-2 px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            <input v-model="form.desembarque_bairro" placeholder="Bairro" required class="px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            <input v-model="form.desembarque_cidade" placeholder="Cidade" required class="px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            <input v-model="form.desembarque_estado" placeholder="UF" maxlength="2" required class="px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition uppercase" />
                            <input v-model="form.desembarque_cep" @input="maskCep('desembarque_cep', $event)" placeholder="CEP" required class="px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                        </div>
                    </div>

                    <!-- ── DESTINO PRINCIPAL ───────────────────────────────── -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-lg"></span>
                            <h2 class="text-base font-semibold text-slate-800" style="font-family:'Sora',sans-serif;">Destino principal</h2>
                            <span class="text-xs text-slate-400 ml-1">(escola, academia, etc.)</span>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <input v-model="form.destino_nome" placeholder="Nome do destino" required class="col-span-2 px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            <select v-model="form.destino_tipo" required class="px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                <option value="escola">Escola</option>
                                <option value="atividade">Atividade</option>
                                <option value="outro">Outro</option>
                            </select>
                        </div>

                        <div class="relative">
                            <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Buscar endereço do destino</label>
                            <input
                                type="text"
                                placeholder="Digite o nome ou endereço do destino..."
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                @input="buscarEndereco('destino', $event.target.value)"
                                @blur="fecharSugestoes('destino')"
                            />
                            <div v-if="loadings['destino']" class="absolute right-3 top-10">
                                <svg class="animate-spin h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                            </div>
                            <ul v-if="sugestoes['destino']?.length" class="absolute z-20 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden">
                                <li
                                    v-for="s in sugestoes['destino']" :key="s.place_id"
                                    @mousedown="selecionarSugestao('destino', s)"
                                    class="px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 cursor-pointer border-b border-slate-100 last:border-0 transition"
                                >
                                    📍 {{ s.display_name }}
                                </li>
                            </ul>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <input v-model="form.destino_logradouro" placeholder="Logradouro" required class="col-span-2 px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            <input v-model="form.destino_numero" placeholder="Número" class="px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            <input v-model="form.destino_complemento" placeholder="Complemento" class="col-span-2 px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            <input v-model="form.destino_bairro" placeholder="Bairro" required class="px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            <input v-model="form.destino_cidade" placeholder="Cidade" required class="px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            <input v-model="form.destino_estado" placeholder="UF" maxlength="2" required class="px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition uppercase" />
                            <input v-model="form.destino_cep" @input="maskCep('destino_cep', $event)" placeholder="CEP" required class="px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                        </div>
                    </div>

                    <!-- Erro geral -->
                    <div v-if="form.errors.geral" class="px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
                        {{ form.errors.geral }}
                    </div>

                    <!-- Ações -->
                    <div class="flex items-center justify-between gap-3 pb-8">
                        <Link :href="route('responsavel.passageiros.create')" class="flex items-center gap-1 text-sm text-slate-400 hover:text-slate-600 transition">
                            ← Voltar
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="bg-blue-600 hover:bg-blue-700 disabled:opacity-60 disabled:cursor-not-allowed text-white font-semibold px-8 py-3.5 rounded-xl transition-all text-sm shadow-lg shadow-blue-200"
                            style="font-family:'Sora',sans-serif;"
                        >
                            {{ form.processing ? 'Salvando...' : 'Concluir cadastro' }}
                        </button>
                    </div>

                </form>
            </div>
        </main>
    </div>
</template>
