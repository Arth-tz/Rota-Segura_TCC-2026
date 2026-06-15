<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

// ─── FORM PRINCIPAL ──────────────────────────────────────────────────────────
const form = useForm({
    // Dados pessoais
    nome:            '',
    cpf:             '',
    data_nascimento: '',
    obs_medica:      '',

    // Endereços e destinos — enviados como arrays
    embarques:    [],
    desembarques: [],
    residencia:   null,
    destinos:     [],
})

// ─── ENDEREÇOS DINÂMICOS ─────────────────────────────────────────────────────
const embarques    = ref([])
const desembarques = ref([])
const residencia   = ref(null)
const destinos     = ref([])

function enderecoVazio() {
    return { logradouro: '', numero: '', complemento: '', bairro: '', cidade: '', estado: '', cep: '', latitude: '', longitude: '' }
}

function adicionarEmbarque()    { embarques.value.push(enderecoVazio()) }
function removerEmbarque(i)     { embarques.value.splice(i, 1) }
function adicionarDesembarque() { desembarques.value.push(enderecoVazio()) }
function removerDesembarque(i)  { desembarques.value.splice(i, 1) }
function ativarResidencia()     { residencia.value = enderecoVazio() }
function removerResidencia()    { residencia.value = null }
function adicionarDestino()     { destinos.value.push({ nome: '', tipo: 'escola', ...enderecoVazio() }) }
function removerDestino(i)      { destinos.value.splice(i, 1) }

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
    'Sergipe':'SE','Tocantins':'TO'
}

async function buscarEndereco(chave, query) {
    if (!query || query.length < 4) { sugestoes.value[chave] = []; return }
    loadings.value[chave] = true
    clearTimeout(timers[chave])
    timers[chave] = setTimeout(async () => {
        try {
            const url = `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query)}&format=json&addressdetails=1&limit=5&countrycodes=br`
            const res = await fetch(url, { headers: { 'Accept-Language': 'pt-BR' } })
            sugestoes.value[chave] = await res.json()
        } catch { sugestoes.value[chave] = [] }
        finally { loadings.value[chave] = false }
    }, 500)
}

function selecionarSugestao(chave, item, destino) {
    const addr = item.address || {}
    destino.logradouro = addr.road || addr.pedestrian || addr.footway || ''
    destino.bairro     = addr.suburb || addr.neighbourhood || ''
    destino.cidade     = addr.city || addr.town || addr.village || ''
    destino.estado     = addr.state_code || estadosUF[addr.state] || ''
    destino.cep        = (addr.postcode || '').replace('-', '')
    destino.latitude   = item.lat || ''
    destino.longitude  = item.lon || ''
    sugestoes.value[chave] = []
}

function fecharSugestoes(chave) {
    setTimeout(() => { sugestoes.value[chave] = [] }, 200)
}

function maskCep(obj, event) {
    let v = event.target.value.replace(/\D/g, '').slice(0, 8)
    if (v.length > 5) v = v.replace(/^(\d{5})(\d+)$/, '$1-$2')
    obj.cep = v
}

function maskCpf(event) {
    let v = event.target.value.replace(/\D/g, '').slice(0, 11)
    if (v.length > 9) v = v.replace(/^(\d{3})(\d{3})(\d{3})(\d+)$/, '$1.$2.$3-$4')
    else if (v.length > 6) v = v.replace(/^(\d{3})(\d{3})(\d+)$/, '$1.$2.$3')
    else if (v.length > 3) v = v.replace(/^(\d{3})(\d+)$/, '$1.$2')
    form.cpf = v
}

// ─── SUBMIT ──────────────────────────────────────────────────────────────────
function submit() {
    form.transform(() => ({
        nome:            form.nome,
        cpf:             form.cpf.replace(/\D/g, ''),
        data_nascimento: form.data_nascimento,
        obs_medica:      form.obs_medica || null,
        embarques:       embarques.value,
        desembarques:    desembarques.value,
        residencia:      residencia.value,
        destinos:        destinos.value,
    })).post(route('responsavel.passageiros.adicionar.store'))
}

// ─── CLASSES DE INPUT ────────────────────────────────────────────────────────
const inputClass = 'w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition'
const inputErrorClass = 'border-red-400 focus:ring-red-400'
</script>

<template>
    <Head title="Adicionar Passageiro" />

    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Nunito:wght@300;400;500;600&display=swap" rel="stylesheet" />

    <div class="min-h-screen bg-slate-50">
        <div class="max-w-2xl mx-auto px-4 py-8">

            <!-- Header -->
            <div class="flex items-center gap-3 mb-6">
                <Link
                    :href="route('responsavel.dashboard')"
                    class="text-sm text-slate-400 hover:text-slate-600 transition"
                >← Voltar</Link>
                <span class="text-slate-300">/</span>
                <h1 class="text-xl font-bold text-slate-900" style="font-family:'Sora',sans-serif;">
                    Adicionar passageiro
                </h1>
            </div>

            <form @submit.prevent="submit" class="space-y-5">

                <!-- ── DADOS PESSOAIS ──────────────────────────────────────── -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                    <h2 class="text-base font-semibold text-slate-800" style="font-family:'Sora',sans-serif;">
                        👤 Dados pessoais
                    </h2>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Nome completo</label>
                        <input
                            v-model="form.nome"
                            type="text"
                            placeholder="João da Silva"
                            required
                            :class="[inputClass, form.errors.nome ? inputErrorClass : '']"
                        />
                        <p v-if="form.errors.nome" class="text-red-500 text-xs mt-1">{{ form.errors.nome }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">CPF</label>
                            <input
                                v-model="form.cpf"
                                type="text"
                                placeholder="000.000.000-00"
                                maxlength="14"
                                required
                                @input="maskCpf"
                                :class="[inputClass, form.errors.cpf ? inputErrorClass : '']"
                            />
                            <p v-if="form.errors.cpf" class="text-red-500 text-xs mt-1">{{ form.errors.cpf }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Data de nascimento</label>
                            <input
                                v-model="form.data_nascimento"
                                type="date"
                                required
                                :class="[inputClass, form.errors.data_nascimento ? inputErrorClass : '']"
                            />
                            <p v-if="form.errors.data_nascimento" class="text-red-500 text-xs mt-1">{{ form.errors.data_nascimento }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">
                            Observações médicas
                            <span class="text-slate-400 normal-case font-normal">(opcional)</span>
                        </label>
                        <textarea
                            v-model="form.obs_medica"
                            rows="3"
                            placeholder="Alergias, medicamentos, necessidades especiais..."
                            :class="[inputClass, 'resize-none']"
                        ></textarea>
                    </div>
                </div>

                <!-- ── EMBARQUES ───────────────────────────────────────────── -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-semibold text-slate-800" style="font-family:'Sora',sans-serif;">Embarques</h2>
                            <p class="text-xs text-slate-400 mt-0.5">Onde o motorista busca o passageiro</p>
                        </div>
                        <button type="button" @click="adicionarEmbarque" class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition px-3 py-1.5 border border-blue-200 rounded-lg">
                            + Adicionar
                        </button>
                    </div>

                    <div v-if="embarques.length === 0" class="text-sm text-slate-400 text-center py-4 border border-dashed border-slate-200 rounded-xl">
                        Nenhum endereço de embarque. <button type="button" @click="adicionarEmbarque" class="text-blue-500 hover:underline">Adicionar agora</button>
                    </div>

                    <div v-for="(emb, i) in embarques" :key="i" class="pt-4 border-t border-slate-100 first:border-0 first:pt-0">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                                Embarque {{ i + 1 }}{{ i === 0 ? ' — Principal' : '' }}
                            </span>
                            <button type="button" @click="removerEmbarque(i)" class="text-xs text-red-400 hover:text-red-600 transition">Remover</button>
                        </div>

                        <!-- Autocomplete -->
                        <div class="relative mb-3">
                            <input
                                type="text"
                                placeholder="Buscar endereço..."
                                :class="[inputClass, 'bg-slate-50']"
                                @input="buscarEndereco(`emb_${i}`, $event.target.value)"
                                @blur="fecharSugestoes(`emb_${i}`)"
                            />
                            <div v-if="loadings[`emb_${i}`]" class="absolute right-3 top-3">
                                <svg class="animate-spin h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                            </div>
                            <ul v-if="sugestoes[`emb_${i}`]?.length" class="absolute z-20 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden">
                                <li v-for="s in sugestoes[`emb_${i}`]" :key="s.place_id" @mousedown="selecionarSugestao(`emb_${i}`, s, emb)" class="px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 cursor-pointer border-b border-slate-100 last:border-0">📍 {{ s.display_name }}</li>
                            </ul>
                        </div>

                        <div class="grid grid-cols-3 gap-2">
                            <input v-model="emb.logradouro" placeholder="Logradouro" :class="[inputClass, 'col-span-2']" />
                            <input v-model="emb.numero" placeholder="Número" :class="inputClass" />
                            <input v-model="emb.complemento" placeholder="Complemento" :class="[inputClass, 'col-span-2']" />
                            <input v-model="emb.bairro" placeholder="Bairro" :class="inputClass" />
                            <input v-model="emb.cidade" placeholder="Cidade" :class="inputClass" />
                            <input v-model="emb.estado" placeholder="UF" maxlength="2" :class="[inputClass, 'uppercase']" />
                            <input v-model="emb.cep" @input="maskCep(emb, $event)" placeholder="CEP" :class="inputClass" />
                        </div>
                    </div>
                </div>

                <!-- ── DESEMBARQUES ─────────────────────────────────────────── -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-semibold text-slate-800" style="font-family:'Sora',sans-serif;">🏁 Desembarques</h2>
                            <p class="text-xs text-slate-400 mt-0.5">Onde o motorista deixa o passageiro</p>
                        </div>
                        <button type="button" @click="adicionarDesembarque" class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition px-3 py-1.5 border border-blue-200 rounded-lg">
                            + Adicionar
                        </button>
                    </div>

                    <div v-if="desembarques.length === 0" class="text-sm text-slate-400 text-center py-4 border border-dashed border-slate-200 rounded-xl">
                        Nenhum endereço de desembarque. <button type="button" @click="adicionarDesembarque" class="text-blue-500 hover:underline">Adicionar agora</button>
                    </div>

                    <div v-for="(des, i) in desembarques" :key="i" class="pt-4 border-t border-slate-100 first:border-0 first:pt-0">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                                Desembarque {{ i + 1 }}{{ i === 0 ? ' — Principal' : '' }}
                            </span>
                            <button type="button" @click="removerDesembarque(i)" class="text-xs text-red-400 hover:text-red-600 transition">Remover</button>
                        </div>

                        <div class="relative mb-3">
                            <input type="text" placeholder="Buscar endereço..." :class="[inputClass, 'bg-slate-50']" @input="buscarEndereco(`des_${i}`, $event.target.value)" @blur="fecharSugestoes(`des_${i}`)" />
                            <ul v-if="sugestoes[`des_${i}`]?.length" class="absolute z-20 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden">
                                <li v-for="s in sugestoes[`des_${i}`]" :key="s.place_id" @mousedown="selecionarSugestao(`des_${i}`, s, des)" class="px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 cursor-pointer border-b border-slate-100 last:border-0">📍 {{ s.display_name }}</li>
                            </ul>
                        </div>

                        <div class="grid grid-cols-3 gap-2">
                            <input v-model="des.logradouro" placeholder="Logradouro" :class="[inputClass, 'col-span-2']" />
                            <input v-model="des.numero" placeholder="Número" :class="inputClass" />
                            <input v-model="des.complemento" placeholder="Complemento" :class="[inputClass, 'col-span-2']" />
                            <input v-model="des.bairro" placeholder="Bairro" :class="inputClass" />
                            <input v-model="des.cidade" placeholder="Cidade" :class="inputClass" />
                            <input v-model="des.estado" placeholder="UF" maxlength="2" :class="[inputClass, 'uppercase']" />
                            <input v-model="des.cep" @input="maskCep(des, $event)" placeholder="CEP" :class="inputClass" />
                        </div>
                    </div>
                </div>

                <!-- ── RESIDÊNCIA ───────────────────────────────────────────── -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-semibold text-slate-800" style="font-family:'Sora',sans-serif;">🏠 Residência</h2>
                            <p class="text-xs text-slate-400 mt-0.5">Endereço onde o passageiro mora</p>
                        </div>
                        <button v-if="!residencia" type="button" @click="ativarResidencia" class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition px-3 py-1.5 border border-blue-200 rounded-lg">
                            + Adicionar
                        </button>
                        <button v-else type="button" @click="removerResidencia" class="text-xs text-red-400 hover:text-red-600 transition">
                            Remover
                        </button>
                    </div>

                    <div v-if="!residencia" class="text-sm text-slate-400 text-center py-4 border border-dashed border-slate-200 rounded-xl">
                        Nenhuma residência cadastrada. <button type="button" @click="ativarResidencia" class="text-blue-500 hover:underline">Adicionar agora</button>
                    </div>

                    <div v-else>
                        <div class="relative mb-3">
                            <input type="text" placeholder="Buscar endereço..." :class="[inputClass, 'bg-slate-50']" @input="buscarEndereco('res', $event.target.value)" @blur="fecharSugestoes('res')" />
                            <ul v-if="sugestoes['res']?.length" class="absolute z-20 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden">
                                <li v-for="s in sugestoes['res']" :key="s.place_id" @mousedown="selecionarSugestao('res', s, residencia)" class="px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 cursor-pointer border-b border-slate-100 last:border-0">📍 {{ s.display_name }}</li>
                            </ul>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <input v-model="residencia.logradouro" placeholder="Logradouro" :class="[inputClass, 'col-span-2']" />
                            <input v-model="residencia.numero" placeholder="Número" :class="inputClass" />
                            <input v-model="residencia.complemento" placeholder="Complemento" :class="[inputClass, 'col-span-2']" />
                            <input v-model="residencia.bairro" placeholder="Bairro" :class="inputClass" />
                            <input v-model="residencia.cidade" placeholder="Cidade" :class="inputClass" />
                            <input v-model="residencia.estado" placeholder="UF" maxlength="2" :class="[inputClass, 'uppercase']" />
                            <input v-model="residencia.cep" @input="maskCep(residencia, $event)" placeholder="CEP" :class="inputClass" />
                        </div>
                    </div>
                </div>

                <!-- ── DESTINOS ─────────────────────────────────────────────── -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-semibold text-slate-800" style="font-family:'Sora',sans-serif;">🎯 Destinos</h2>
                            <p class="text-xs text-slate-400 mt-0.5">Escola, academia, etc.</p>
                        </div>
                        <button type="button" @click="adicionarDestino" class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition px-3 py-1.5 border border-blue-200 rounded-lg">
                            + Adicionar
                        </button>
                    </div>

                    <div v-if="destinos.length === 0" class="text-sm text-slate-400 text-center py-4 border border-dashed border-slate-200 rounded-xl">
                        Nenhum destino cadastrado. <button type="button" @click="adicionarDestino" class="text-blue-500 hover:underline">Adicionar agora</button>
                    </div>

                    <div v-for="(dest, i) in destinos" :key="i" class="pt-4 border-t border-slate-100 first:border-0 first:pt-0">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Destino {{ i + 1 }}</span>
                            <button type="button" @click="removerDestino(i)" class="text-xs text-red-400 hover:text-red-600 transition">Remover</button>
                        </div>

                        <div class="grid grid-cols-3 gap-2 mb-3">
                            <input v-model="dest.nome" placeholder="Nome do destino" :class="[inputClass, 'col-span-2']" />
                            <select v-model="dest.tipo" :class="inputClass">
                                <option value="escola">Escola</option>
                                <option value="atividade">Atividade</option>
                                <option value="outro">Outro</option>
                            </select>
                        </div>

                        <div class="relative mb-3">
                            <input type="text" placeholder="Buscar endereço do destino..." :class="[inputClass, 'bg-slate-50']" @input="buscarEndereco(`dest_${i}`, $event.target.value)" @blur="fecharSugestoes(`dest_${i}`)" />
                            <ul v-if="sugestoes[`dest_${i}`]?.length" class="absolute z-20 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden">
                                <li v-for="s in sugestoes[`dest_${i}`]" :key="s.place_id" @mousedown="selecionarSugestao(`dest_${i}`, s, dest)" class="px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 cursor-pointer border-b border-slate-100 last:border-0">📍 {{ s.display_name }}</li>
                            </ul>
                        </div>

                        <div class="grid grid-cols-3 gap-2">
                            <input v-model="dest.logradouro" placeholder="Logradouro" :class="[inputClass, 'col-span-2']" />
                            <input v-model="dest.numero" placeholder="Número" :class="inputClass" />
                            <input v-model="dest.bairro" placeholder="Bairro" :class="inputClass" />
                            <input v-model="dest.cidade" placeholder="Cidade" :class="inputClass" />
                            <input v-model="dest.estado" placeholder="UF" maxlength="2" :class="[inputClass, 'uppercase']" />
                            <input v-model="dest.cep" @input="maskCep(dest, $event)" placeholder="CEP" :class="inputClass" />
                        </div>
                    </div>
                </div>

                <!-- Erro geral -->
                <div v-if="form.errors.geral" class="px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
                    {{ form.errors.geral }}
                </div>

                <!-- Ações -->
                <div class="flex items-center justify-between pb-8">
                    <Link :href="route('responsavel.dashboard')" class="text-sm text-slate-400 hover:text-slate-600 transition">
                        ← Cancelar
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-blue-600 hover:bg-blue-700 disabled:opacity-60 disabled:cursor-not-allowed text-white font-semibold px-8 py-3 rounded-xl transition shadow-lg shadow-blue-200 text-sm"
                        style="font-family:'Sora',sans-serif;"
                    >
                        {{ form.processing ? 'Salvando...' : 'Cadastrar passageiro' }}
                    </button>
                </div>

            </form>
        </div>
    </div>
</template>
