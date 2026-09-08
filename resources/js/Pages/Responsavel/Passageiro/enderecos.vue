<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { MapPinIcon, LightBulbIcon, CameraIcon } from '@heroicons/vue/24/outline'

// ─── FORM ────────────────────────────────────────────────────────────────────
const form = useForm({
    // Origem — de onde o passageiro sai
    embarque_logradouro:  '', embarque_numero: '', embarque_complemento: '',
    embarque_bairro:      '', embarque_cidade: '', embarque_estado: '',
    embarque_cep:         '', embarque_latitude: '', embarque_longitude: '',

    // Destino — para onde o passageiro vai
    desembarque_nome:        '',
    desembarque_logradouro:  '', desembarque_numero: '', desembarque_complemento: '',
    desembarque_bairro:      '', desembarque_cidade: '', desembarque_estado: '',
    desembarque_cep:         '', desembarque_latitude: '', desembarque_longitude: '',

    // Foto
    foto: null,
})

// ─── FOTO ────────────────────────────────────────────────────────────────────
const fotoPreview = ref(null)
const fileInput   = ref(null)

function handleFoto(e) {
    const file = e.target.files[0]
    if (!file) return
    fotoPreview.value = URL.createObjectURL(file)
    form.foto = file
}

// ─── AUTOCOMPLETE NOMINATIM ──────────────────────────────────────────────────
const sugestoes = ref({})
const loadings  = ref({})
let timers = {}

const estadosUF = {
    'Acre':'AC','Alagoas':'AL','Amapá':'AP','Amazonas':'AM','Bahia':'BA',
    'Ceará':'CE','Distrito Federal':'DF','Espírito Santo':'ES','Goiás':'GO',
    'Maranhão':'MA','Mato Grosso':'MT','Mato Grosso do Sul':'MS','Minas Gerais':'MG',
    'Pará':'PA','Paraíba':'PB','Paraná':'PR','Pernambuco':'PE','Piauí':'PI',
    'Rio de Janeiro':'RJ','Rio Grande do Norte':'RN','Rio Grande do Sul':'RS',
    'Rondônia':'RO','Roraima':'RR','Santa Catarina':'SC','São Paulo':'SP',
    'Sergipe':'SE','Tocantins':'TO',
}

async function buscarEndereco(prefixo, query) {
    if (!query || query.length < 4) { sugestoes.value[prefixo] = []; return }
    loadings.value[prefixo] = true
    clearTimeout(timers[prefixo])
    timers[prefixo] = setTimeout(async () => {
        try {
            const url = `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query)}&format=json&addressdetails=1&limit=5&countrycodes=br`
            const res = await fetch(url, { headers: { 'Accept-Language': 'pt-BR' } })
            sugestoes.value[prefixo] = await res.json()
        } catch { sugestoes.value[prefixo] = [] }
        finally { loadings.value[prefixo] = false }
    }, 500)
}

function selecionarSugestao(prefixo, item) {
    const addr = item.address || {}
    form[`${prefixo}_logradouro`] = addr.road || addr.pedestrian || addr.footway || ''
    form[`${prefixo}_bairro`]     = addr.suburb || addr.neighbourhood || addr.city_district || ''
    form[`${prefixo}_cidade`]     = addr.city || addr.town || addr.village || ''
    form[`${prefixo}_estado`]     = addr.state_code || estadosUF[addr.state] || ''
    form[`${prefixo}_cep`]        = (addr.postcode || '').replace('-', '')
    form[`${prefixo}_latitude`]   = item.lat || ''
    form[`${prefixo}_longitude`]  = item.lon || ''
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

function submit() {
    form.transform(data => ({
        ...data,
        residencia_logradouro:  data.embarque_logradouro,
        residencia_numero:      data.embarque_numero,
        residencia_complemento: data.embarque_complemento,
        residencia_bairro:      data.embarque_bairro,
        residencia_cidade:      data.embarque_cidade,
        residencia_estado:      data.embarque_estado,
        residencia_cep:         data.embarque_cep,
        residencia_latitude:    data.embarque_latitude,
        residencia_longitude:   data.embarque_longitude,
    })).post(route('responsavel.passageiros.store'), { forceFormData: true })
}

// Classe padrão para inputs de endereço (sem label acima, placeholder como label)
const addrInput = (ring = 'emerald') =>
    `px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400
     outline-none transition focus:ring-2 focus:border-transparent focus:ring-${ring}-300`
</script>

<template>
    <Head title="Endereços do passageiro" />

    <div class="min-h-screen bg-slate-50 flex">

        <!-- ── ASIDE ─────────────────────────────────────────────────────── -->
        <aside class="hidden lg:flex lg:w-4/12 flex-col justify-between p-12 sticky top-0 h-screen overflow-y-auto z-10
                       bg-[radial-gradient(circle_at_top_left,_#3b82f6,_#1d4ed8_60%)]
                       shadow-[10px_0_50px_-15px_rgba(0,0,0,0.3)]">

            <Link :href="route('home')" class="flex items-center gap-2 hover:opacity-80 transition">
                <img src="/rota-segura/public/images/Logo_rota-segura_branco.png" alt="Logo Rota Segura" class="h-12 w-12" />
                <span class="text-white font-bold text-lg tracking-tight">Rota Segura</span>
            </Link>

            <div>
                <!-- Stepper -->
                <div class="flex items-center gap-3 mb-8">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-white/30 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="text-blue-200 text-sm line-through">Dados pessoais</span>
                    </div>
                    <div class="h-px w-6 bg-blue-400"></div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shrink-0">
                            <span class="text-blue-700 text-sm font-bold">2</span>
                        </div>
                        <span class="text-white text-sm font-semibold">Endereços</span>
                    </div>
                </div>

                <h2 class="text-4xl font-extrabold text-white leading-tight mb-4">
                    Onde fica o passageiro?
                </h2>
                <p class="text-blue-100 text-sm leading-relaxed mb-8">
                    Precisamos saber de onde o motorista busca e para onde leva o passageiro.
                </p>

                <!-- A → B visual -->
                <div class="bg-white/10 rounded-2xl p-4 space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-emerald-400 rounded-full flex items-center justify-center shrink-0 text-white font-bold text-sm">A</div>
                        <div>
                            <p class="text-white text-sm font-medium">De onde sai</p>
                            <p class="text-blue-200 text-xs">Casa, condomínio, ponto de encontro</p>
                        </div>
                    </div>
                    <div class="ml-4 border-l-2 border-blue-400 border-dashed h-4"></div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-red-400 rounded-full flex items-center justify-center shrink-0 text-white font-bold text-sm">B</div>
                        <div>
                            <p class="text-white text-sm font-medium">Para onde vai</p>
                            <p class="text-blue-200 text-xs">Escola, academia, atividade</p>
                        </div>
                    </div>
                </div>

                <p class="text-blue-200 text-xs mt-6 flex items-start gap-1.5">
                    <LightBulbIcon class="w-3.5 h-3.5 mt-0.5 shrink-0" />
                    Você pode adicionar mais rotas a qualquer momento pelo painel.
                </p>
            </div>

            <p class="text-blue-200 text-xs">© 2026 Rota Segura</p>
        </aside>

        <!-- ── FORMULÁRIO ─────────────────────────────────────────────────── -->
        <main class="flex-1 flex items-start justify-center px-4 py-10 overflow-y-auto">
            <div class="w-full max-w-xl">

                <!-- Logo mobile -->
                <div class="lg:hidden flex items-center gap-2 mb-8">
                    <img src="/rota-segura/public/images/Logo_rota_segura-azul.png" alt="Rota Segura" class="h-10 w-10">
                    <span class="text-slate-900 font-bold">Rota Segura</span>
                </div>

                <h1 class="text-3xl font-bold text-slate-900 mb-1">Endereços</h1>
                <p class="text-slate-500 text-sm mb-8">
                    De onde o passageiro sai e para onde vai.
                    <span class="text-slate-400"> Você pode adicionar mais rotas depois.</span>
                </p>

                <form @submit.prevent="submit" class="space-y-5">

                    <!-- ── A: DE ONDE SAI ─────────────────────────────────── -->
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                        <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50">
                            <div class="w-7 h-7 bg-emerald-500 rounded-full flex items-center justify-center shrink-0">
                                <span class="text-white text-xs font-bold">A</span>
                            </div>
                            <div>
                                <h2 class="text-base font-semibold text-slate-800">De onde sai</h2>
                                <p class="text-xs text-slate-400">Onde o motorista vai buscar o passageiro</p>
                            </div>
                        </div>

                        <div class="p-6 space-y-4">
                            <div class="relative">
                                <input type="text" placeholder="Buscar endereço…"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-sm placeholder-slate-400 outline-none transition focus:ring-2 focus:ring-emerald-400 focus:border-transparent"
                                    @input="buscarEndereco('embarque', $event.target.value)"
                                    @blur="fecharSugestoes('embarque')" />
                                <div v-if="loadings['embarque']" class="absolute right-3 top-3.5">
                                    <svg class="animate-spin h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                    </svg>
                                </div>
                                <ul v-if="sugestoes['embarque']?.length"
                                    class="absolute z-20 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden">
                                    <li v-for="s in sugestoes['embarque']" :key="s.place_id"
                                        @mousedown="selecionarSugestao('embarque', s)"
                                        class="px-4 py-3 text-sm text-slate-800 hover:bg-slate-50 cursor-pointer border-b border-slate-100 last:border-0 flex items-center gap-2">
                                        <MapPinIcon class="w-3.5 h-3.5 text-blue-400 shrink-0" />
                                        <span class="truncate">{{ s.display_name }}</span>
                                    </li>
                                </ul>
                            </div>

                            <div class="space-y-2">
                                <div class="grid grid-cols-[1fr_5rem] gap-2">
                                    <input v-model="form.embarque_logradouro" placeholder="Logradouro" required
                                        :class="[addrInput('emerald'), form.errors.embarque_logradouro ? 'border-red-400' : '']" />
                                    <input v-model="form.embarque_numero" placeholder="Nº" :class="addrInput('emerald')" />
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <input v-model="form.embarque_complemento" placeholder="Complemento" :class="addrInput('emerald')" />
                                    <input v-model="form.embarque_bairro" placeholder="Bairro" required :class="addrInput('emerald')" />
                                </div>
                                <div class="grid grid-cols-[1fr_3.5rem_1fr] gap-2">
                                    <input v-model="form.embarque_cidade" placeholder="Cidade" required :class="addrInput('emerald')" />
                                    <input v-model="form.embarque_estado" placeholder="UF" maxlength="2" required
                                        :class="addrInput('emerald') + ' uppercase'" />
                                    <input :value="form.embarque_cep" @input="maskCep('embarque_cep', $event)"
                                        placeholder="CEP" required :class="addrInput('emerald')" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Separador A → B -->
                    <div class="flex items-center gap-3">
                        <div class="h-px flex-1 bg-slate-200"></div>
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-500 text-sm font-bold">↓</div>
                        <div class="h-px flex-1 bg-slate-200"></div>
                    </div>

                    <!-- ── B: PARA ONDE VAI ───────────────────────────────── -->
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                        <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50">
                            <div class="w-7 h-7 bg-red-500 rounded-full flex items-center justify-center shrink-0">
                                <span class="text-white text-xs font-bold">B</span>
                            </div>
                            <div>
                                <h2 class="text-base font-semibold text-slate-800">Para onde vai</h2>
                                <p class="text-xs text-slate-400">Escola, academia ou outra atividade</p>
                            </div>
                        </div>

                        <div class="p-6 space-y-4">
                            <input v-model="form.desembarque_nome" placeholder="Nome do local (ex: Escola Municipal ABC)"
                                :class="addrInput('red') + ' w-full'" />

                            <div class="relative">
                                <input type="text" placeholder="Buscar endereço do destino…"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-sm placeholder-slate-400 outline-none transition focus:ring-2 focus:ring-red-300 focus:border-transparent"
                                    @input="buscarEndereco('desembarque', $event.target.value)"
                                    @blur="fecharSugestoes('desembarque')" />
                                <div v-if="loadings['desembarque']" class="absolute right-3 top-3.5">
                                    <svg class="animate-spin h-4 w-4 text-red-400" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                    </svg>
                                </div>
                                <ul v-if="sugestoes['desembarque']?.length"
                                    class="absolute z-20 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden">
                                    <li v-for="s in sugestoes['desembarque']" :key="s.place_id"
                                        @mousedown="selecionarSugestao('desembarque', s)"
                                        class="px-4 py-3 text-sm text-slate-800 hover:bg-slate-50 cursor-pointer border-b border-slate-100 last:border-0 flex items-center gap-2">
                                        <MapPinIcon class="w-3.5 h-3.5 text-blue-400 shrink-0" />
                                        <span class="truncate">{{ s.display_name }}</span>
                                    </li>
                                </ul>
                            </div>

                            <div class="space-y-2">
                                <div class="grid grid-cols-[1fr_5rem] gap-2">
                                    <input v-model="form.desembarque_logradouro" placeholder="Logradouro" required
                                        :class="[addrInput('red'), form.errors.desembarque_logradouro ? 'border-red-400' : '']" />
                                    <input v-model="form.desembarque_numero" placeholder="Nº" :class="addrInput('red')" />
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <input v-model="form.desembarque_complemento" placeholder="Complemento" :class="addrInput('red')" />
                                    <input v-model="form.desembarque_bairro" placeholder="Bairro" required :class="addrInput('red')" />
                                </div>
                                <div class="grid grid-cols-[1fr_3.5rem_1fr] gap-2">
                                    <input v-model="form.desembarque_cidade" placeholder="Cidade" required :class="addrInput('red')" />
                                    <input v-model="form.desembarque_estado" placeholder="UF" maxlength="2" required
                                        :class="addrInput('red') + ' uppercase'" />
                                    <input :value="form.desembarque_cep" @input="maskCep('desembarque_cep', $event)"
                                        placeholder="CEP" required :class="addrInput('red')" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ── FOTO DO PASSAGEIRO ─────────────────────────────── -->
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                        <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50">
                            <CameraIcon class="w-5 h-5 text-slate-400 shrink-0" />
                            <div>
                                <h2 class="text-base font-semibold text-slate-800">Foto do passageiro</h2>
                                <p class="text-xs text-slate-400">Opcional — ajuda o motorista a identificar</p>
                            </div>
                        </div>

                        <div class="px-6 py-5 flex items-center gap-5">
                            <button type="button" @click="fileInput.click()"
                                class="relative group shrink-0 rounded-full focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400">
                                <div class="w-16 h-16 rounded-full overflow-hidden bg-blue-100 border-2 border-blue-200 flex items-center justify-center">
                                    <img v-if="fotoPreview" :src="fotoPreview" class="w-full h-full object-cover" alt="Foto" />
                                    <span v-else class="text-blue-400 font-bold text-lg">?</span>
                                </div>
                                <div class="absolute inset-0 rounded-full bg-black/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                    <CameraIcon class="w-5 h-5 text-white" />
                                </div>
                            </button>
                            <div>
                                <button type="button" @click="fileInput.click()"
                                    class="text-sm font-semibold text-blue-600 hover:text-blue-700 transition">
                                    {{ fotoPreview ? 'Alterar foto' : 'Adicionar foto' }}
                                </button>
                                <p class="text-xs text-slate-400 mt-0.5">JPG, PNG ou WebP · máx. 2 MB</p>
                                <p v-if="form.errors.foto" class="text-xs text-red-500 mt-1">{{ form.errors.foto }}</p>
                            </div>
                            <input ref="fileInput" type="file" accept="image/jpeg,image/png,image/webp"
                                class="hidden" @change="handleFoto" />
                        </div>
                    </div>

                    <!-- Erro geral -->
                    <div v-if="form.errors.geral"
                        class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                        {{ form.errors.geral }}
                    </div>

                    <!-- Ações -->
                    <div class="flex items-center justify-between gap-3 pb-8">
                        <div class="flex flex-col gap-1">
                            <Link :href="route('responsavel.passageiros.create')"
                                class="text-sm text-slate-500 hover:text-slate-700 transition">← Voltar</Link>
                            <Link :href="route('responsavel.dashboard')"
                                class="text-xs text-slate-400 hover:text-slate-600 transition">Preencher depois</Link>
                        </div>
                        <button type="submit" :disabled="form.processing"
                            class="bg-blue-600 hover:bg-blue-700 disabled:opacity-60 disabled:cursor-not-allowed
                                   text-white font-semibold px-8 py-3 rounded-xl transition shadow-sm shadow-blue-200 text-sm">
                            {{ form.processing ? 'Salvando…' : 'Concluir cadastro →' }}
                        </button>
                    </div>

                </form>
            </div>
        </main>
    </div>
</template>
