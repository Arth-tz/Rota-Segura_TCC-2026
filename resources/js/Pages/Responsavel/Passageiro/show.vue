<script setup>
import { ref, computed } from 'vue'
import { Head, Link, useForm, router } from '@inertiajs/vue3'

const props = defineProps({
    passageiro: { type: Object, required: true },
})

// ─── SEÇÃO ATIVA ────────────────────────────────────────────────────────────
const secaoAberta = ref('dados') // 'dados' | 'enderecos' | 'destinos' | 'vinculos'

// ─── FORM DADOS PESSOAIS ─────────────────────────────────────────────────────
const formDados = useForm({
    nome:            props.passageiro.nome,
    data_nascimento: props.passageiro.data_nascimento,
    telefone:        props.passageiro.telefone ?? '',
    obs_medica:      props.passageiro.observacoes_medicas ?? '',
})

function salvarDados() {
    formDados.put(route('responsavel.passageiros.update', props.passageiro.id_passageiro))
}

// ─── ENDEREÇOS ───────────────────────────────────────────────────────────────
const embarques    = ref(enderecosPorTipo('embarque'))
const desembarques = ref(enderecosPorTipo('desembarque'))
const residencia   = ref(enderecosPorTipo('residencia')[0] ?? enderecoVazio())

function enderecosPorTipo(tipo) {
    return props.passageiro.enderecos
        .filter(e => e.tipo === tipo)
        .map(e => ({ ...e.endereco, id_passageiro_endereco: e.id_passageiro_endereco, principal: e.principal }))
}

function enderecoVazio() {
    return { logradouro: '', numero: '', complemento: '', bairro: '', cidade: '', estado: '', cep: '', latitude: '', longitude: '' }
}

function adicionarEmbarque() {
    embarques.value.push(enderecoVazio())
}

function removerEmbarque(index) {
    embarques.value.splice(index, 1)
}

function adicionarDesembarque() {
    desembarques.value.push(enderecoVazio())
}

function removerDesembarque(index) {
    desembarques.value.splice(index, 1)
}

const formEnderecos = useForm({})

function salvarEnderecos() {
    formEnderecos.transform(() => ({
        embarques:    embarques.value,
        desembarques: desembarques.value,
        residencia:   residencia.value,
        destinos:     destinos.value,
    })).put(route('responsavel.passageiros.enderecos.update', props.passageiro.id_passageiro))
}

// ─── DESTINOS ────────────────────────────────────────────────────────────────
const destinos = ref(props.passageiro.destinos?.map(d => ({
    nome:        d.nome,
    tipo:        d.tipo,
    logradouro:  d.endereco?.logradouro ?? '',
    numero:      d.endereco?.numero ?? '',
    complemento: d.endereco?.complemento ?? '',
    bairro:      d.endereco?.bairro ?? '',
    cidade:      d.endereco?.cidade ?? '',
    estado:      d.endereco?.estado ?? '',
    cep:         d.endereco?.cep ?? '',
    latitude:    d.endereco?.latitude ?? '',
    longitude:   d.endereco?.longitude ?? '',
})) ?? [])

function adicionarDestino() {
    destinos.value.push({ nome: '', tipo: 'escola', logradouro: '', numero: '', complemento: '', bairro: '', cidade: '', estado: '', cep: '', latitude: '', longitude: '' })
}

function removerDestino(index) {
    destinos.value.splice(index, 1)
}

// ─── AUTOCOMPLETE NOMINATIM ──────────────────────────────────────────────────
const sugestoes = ref({})
const loadings  = ref({})
let timers = {}

async function buscarEndereco(chave, query) {
    if (!query || query.length < 4) { sugestoes.value[chave] = []; return }
    loadings.value[chave] = true
    clearTimeout(timers[chave])
    timers[chave] = setTimeout(async () => {
        try {
            const url = `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query)}&format=json&addressdetails=1&limit=5&countrycodes=br`
            const res  = await fetch(url, { headers: { 'Accept-Language': 'pt-BR' } })
            sugestoes.value[chave] = await res.json()
        } catch { sugestoes.value[chave] = [] }
        finally { loadings.value[chave] = false }
    }, 500)
}

const estadosUF = {
    'Acre':'AC','Alagoas':'AL','Amapá':'AP','Amazonas':'AM','Bahia':'BA',
    'Ceará':'CE','Distrito Federal':'DF','Espírito Santo':'ES','Goiás':'GO',
    'Maranhão':'MA','Mato Grosso':'MT','Mato Grosso do Sul':'MS','Minas Gerais':'MG',
    'Pará':'PA','Paraíba':'PB','Paraná':'PR','Pernambuco':'PE','Piauí':'PI',
    'Rio de Janeiro':'RJ','Rio Grande do Norte':'RN','Rio Grande do Sul':'RS',
    'Rondônia':'RO','Roraima':'RR','Santa Catarina':'SC','São Paulo':'SP',
    'Sergipe':'SE','Tocantins':'TO'
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

// ─── DESATIVAR PASSAGEIRO ────────────────────────────────────────────────────
const confirmandoDesativar = ref(false)

function desativar() {
    router.put(route('responsavel.passageiros.desativar', props.passageiro.id_passageiro))
}

// ─── STATUS VÍNCULO ──────────────────────────────────────────────────────────
const vinculoAtivo = computed(() => props.passageiro.vinculos?.find(v => v.status === 'ativo'))
</script>

<template>
    <Head :title="`${passageiro.nome} — Passageiro`" />

    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Nunito:wght@300;400;500;600&display=swap" rel="stylesheet" />

    <div class="min-h-screen bg-slate-50">
        <div class="max-w-3xl mx-auto px-4 py-8">

            <!-- Header -->
            <div class="flex items-center gap-3 mb-6">
                <Link
                    :href="route('responsavel.dashboard')"
                    class="text-sm text-slate-400 hover:text-slate-600 transition"
                >← Voltar</Link>
                <span class="text-slate-300">/</span>
                <h1 class="text-xl font-bold text-slate-900" style="font-family:'Sora',sans-serif;">
                    {{ passageiro.nome }}
                </h1>
                <span
                    class="ml-auto text-xs px-2.5 py-1 rounded-full border font-medium"
                    :class="passageiro.ativo ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-600 border-red-200'"
                >
                    {{ passageiro.ativo ? 'Ativo' : 'Inativo' }}
                </span>
            </div>

            <!-- Tabs de navegação -->
            <div class="flex gap-1 bg-white border border-slate-200 rounded-xl p-1 mb-6 shadow-sm">
                <button
                    v-for="tab in [
                        { key: 'dados',    label: 'Dados' },
                        { key: 'enderecos', label: 'Endereços' },
                        { key: 'destinos', label: 'Destinos' },
                        { key: 'vinculos', label: 'Vínculo' },
                    ]"
                    :key="tab.key"
                    @click="secaoAberta = tab.key"
                    class="flex-1 py-2 px-3 rounded-lg text-sm font-medium transition-all"
                    :class="secaoAberta === tab.key
                        ? 'bg-blue-600 text-white shadow-sm'
                        : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
                >
                    {{ tab.label }}
                </button>
            </div>

            <!-- ── SEÇÃO: DADOS PESSOAIS ──────────────────────────────────── -->
            <div v-if="secaoAberta === 'dados'" class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <h2 class="text-base font-semibold text-slate-800 mb-5" style="font-family:'Sora',sans-serif;">Dados pessoais</h2>

                <form @submit.prevent="salvarDados" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Nome completo</label>
                        <input
                            v-model="formDados.nome"
                            type="text"
                            required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            :class="{ 'border-red-400': formDados.errors.nome }"
                        />
                        <p v-if="formDados.errors.nome" class="text-red-500 text-xs mt-1">{{ formDados.errors.nome }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Data de nascimento</label>
                            <input
                                v-model="formDados.data_nascimento"
                                type="date"
                                required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                :class="{ 'border-red-400': formDados.errors.data_nascimento }"
                            />
                            <p v-if="formDados.errors.data_nascimento" class="text-red-500 text-xs mt-1">{{ formDados.errors.data_nascimento }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Telefone</label>
                            <input
                                v-model="formDados.telefone"
                                type="text"
                                placeholder="(00) 00000-0000"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            />
                        </div>
                    </div>

                    <!-- CPF somente leitura -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">CPF</label>
                        <input
                            :value="passageiro.cpf"
                            type="text"
                            disabled
                            class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50 text-sm text-slate-400 cursor-not-allowed"
                        />
                        <p class="text-xs text-slate-400 mt-1">O CPF não pode ser alterado.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">
                            Observações médicas
                            <span class="text-slate-400 normal-case font-normal">(opcional)</span>
                        </label>
                        <textarea
                            v-model="formDados.obs_medica"
                            rows="3"
                            placeholder="Alergias, medicamentos, necessidades especiais..."
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"
                        ></textarea>
                    </div>

                    <div v-if="formDados.errors.geral" class="px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
                        {{ formDados.errors.geral }}
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <!-- Desativar passageiro -->
                        <div>
                            <button
                                v-if="!confirmandoDesativar && passageiro.ativo"
                                type="button"
                                @click="confirmandoDesativar = true"
                                class="text-sm text-red-500 hover:text-red-600 transition"
                            >
                                Desativar passageiro
                            </button>
                            <div v-if="confirmandoDesativar" class="flex items-center gap-2">
                                <span class="text-sm text-slate-600">Tem certeza?</span>
                                <button type="button" @click="desativar" class="text-sm font-semibold text-red-600 hover:text-red-700">Sim, desativar</button>
                                <button type="button" @click="confirmandoDesativar = false" class="text-sm text-slate-500">Cancelar</button>
                            </div>
                        </div>

                        <button
                            type="submit"
                            :disabled="formDados.processing"
                            class="bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition shadow-sm shadow-blue-200"
                            style="font-family:'Sora',sans-serif;"
                        >
                            {{ formDados.processing ? 'Salvando...' : 'Salvar dados' }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- ── SEÇÃO: ENDEREÇOS ───────────────────────────────────────── -->
            <div v-else-if="secaoAberta === 'enderecos'" class="space-y-4">

                <!-- Bloco de embarques -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-base font-semibold text-slate-800" style="font-family:'Sora',sans-serif;">Embarques</h2>
                        <button @click="adicionarEmbarque" class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition">+ Adicionar</button>
                    </div>

                    <div v-if="embarques.length === 0" class="text-sm text-slate-400 text-center py-4">
                        Nenhum endereço de embarque cadastrado.
                    </div>

                    <div v-for="(emb, index) in embarques" :key="index" class="mb-6 pb-6 border-b border-slate-100 last:border-0 last:mb-0 last:pb-0">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                                Embarque {{ index + 1 }} {{ index === 0 ? '— Principal' : '' }}
                            </span>
                            <button v-if="embarques.length > 1" @click="removerEmbarque(index)" class="text-xs text-red-400 hover:text-red-600 transition">Remover</button>
                        </div>

                        <!-- Autocomplete -->
                        <div class="relative mb-3">
                            <input
                                type="text"
                                placeholder="Buscar endereço..."
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                @input="buscarEndereco(`emb_${index}`, $event.target.value)"
                                @blur="fecharSugestoes(`emb_${index}`)"
                            />
                            <ul v-if="sugestoes[`emb_${index}`]?.length" class="absolute z-20 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden">
                                <li
                                    v-for="s in sugestoes[`emb_${index}`]" :key="s.place_id"
                                    @mousedown="selecionarSugestao(`emb_${index}`, s, emb)"
                                    class="px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 cursor-pointer border-b border-slate-100 last:border-0"
                                >📍 {{ s.display_name }}</li>
                            </ul>
                        </div>

                        <div class="grid grid-cols-3 gap-2">
                            <input v-model="emb.logradouro" placeholder="Logradouro" class="col-span-2 px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            <input v-model="emb.numero" placeholder="Número" class="px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            <input v-model="emb.complemento" placeholder="Complemento" class="col-span-2 px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            <input v-model="emb.bairro" placeholder="Bairro" class="px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            <input v-model="emb.cidade" placeholder="Cidade" class="px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            <input v-model="emb.estado" placeholder="UF" maxlength="2" class="px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition uppercase" />
                            <input v-model="emb.cep" @input="maskCep(emb, $event)" placeholder="CEP" class="px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                        </div>
                    </div>
                </div>

                <!-- Bloco de desembarques -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-base font-semibold text-slate-800" style="font-family:'Sora',sans-serif;">🏁 Desembarques</h2>
                        <button @click="adicionarDesembarque" class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition">+ Adicionar</button>
                    </div>

                    <div v-if="desembarques.length === 0" class="text-sm text-slate-400 text-center py-4">
                        Nenhum endereço de desembarque cadastrado.
                    </div>

                    <div v-for="(des, index) in desembarques" :key="index" class="mb-6 pb-6 border-b border-slate-100 last:border-0 last:mb-0 last:pb-0">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                                Desembarque {{ index + 1 }} {{ index === 0 ? '— Principal' : '' }}
                            </span>
                            <button v-if="desembarques.length > 1" @click="removerDesembarque(index)" class="text-xs text-red-400 hover:text-red-600 transition">Remover</button>
                        </div>

                        <div class="relative mb-3">
                            <input
                                type="text"
                                placeholder="Buscar endereço..."
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                @input="buscarEndereco(`des_${index}`, $event.target.value)"
                                @blur="fecharSugestoes(`des_${index}`)"
                            />
                            <ul v-if="sugestoes[`des_${index}`]?.length" class="absolute z-20 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden">
                                <li
                                    v-for="s in sugestoes[`des_${index}`]" :key="s.place_id"
                                    @mousedown="selecionarSugestao(`des_${index}`, s, des)"
                                    class="px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 cursor-pointer border-b border-slate-100 last:border-0"
                                >📍 {{ s.display_name }}</li>
                            </ul>
                        </div>

                        <div class="grid grid-cols-3 gap-2">
                            <input v-model="des.logradouro" placeholder="Logradouro" class="col-span-2 px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            <input v-model="des.numero" placeholder="Número" class="px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            <input v-model="des.complemento" placeholder="Complemento" class="col-span-2 px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            <input v-model="des.bairro" placeholder="Bairro" class="px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            <input v-model="des.cidade" placeholder="Cidade" class="px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            <input v-model="des.estado" placeholder="UF" maxlength="2" class="px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition uppercase" />
                            <input v-model="des.cep" @input="maskCep(des, $event)" placeholder="CEP" class="px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                        </div>
                    </div>
                </div>

                <!-- Bloco de residência -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <h2 class="text-base font-semibold text-slate-800 mb-4" style="font-family:'Sora',sans-serif;">🏠 Residência</h2>

                    <div class="relative mb-3">
                        <input
                            type="text"
                            placeholder="Buscar endereço..."
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            @input="buscarEndereco('residencia', $event.target.value)"
                            @blur="fecharSugestoes('residencia')"
                        />
                        <ul v-if="sugestoes['residencia']?.length" class="absolute z-20 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden">
                            <li
                                v-for="s in sugestoes['residencia']" :key="s.place_id"
                                @mousedown="selecionarSugestao('residencia', s, residencia)"
                                class="px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 cursor-pointer border-b border-slate-100 last:border-0"
                            >📍 {{ s.display_name }}</li>
                        </ul>
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <input v-model="residencia.logradouro" placeholder="Logradouro" class="col-span-2 px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                        <input v-model="residencia.numero" placeholder="Número" class="px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                        <input v-model="residencia.complemento" placeholder="Complemento" class="col-span-2 px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                        <input v-model="residencia.bairro" placeholder="Bairro" class="px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                        <input v-model="residencia.cidade" placeholder="Cidade" class="px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                        <input v-model="residencia.estado" placeholder="UF" maxlength="2" class="px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition uppercase" />
                        <input v-model="residencia.cep" @input="maskCep(residencia, $event)" placeholder="CEP" class="px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                    </div>
                </div>

                <!-- Botão salvar endereços -->
                <div class="flex justify-end">
                    <button
                        @click="salvarEnderecos"
                        :disabled="formEnderecos.processing"
                        class="bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition shadow-sm shadow-blue-200"
                        style="font-family:'Sora',sans-serif;"
                    >
                        {{ formEnderecos.processing ? 'Salvando...' : 'Salvar endereços' }}
                    </button>
                </div>
            </div>

            <!-- ── SEÇÃO: DESTINOS ────────────────────────────────────────── -->
            <div v-else-if="secaoAberta === 'destinos'" class="space-y-4">
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-base font-semibold text-slate-800" style="font-family:'Sora',sans-serif;">🎯 Destinos</h2>
                        <button @click="adicionarDestino" class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition">+ Adicionar destino</button>
                    </div>

                    <div v-if="destinos.length === 0" class="text-sm text-slate-400 text-center py-6">
                        Nenhum destino cadastrado. Adicione uma escola ou atividade.
                    </div>

                    <div v-for="(dest, index) in destinos" :key="index" class="mb-6 pb-6 border-b border-slate-100 last:border-0 last:mb-0 last:pb-0">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Destino {{ index + 1 }}</span>
                            <button @click="removerDestino(index)" class="text-xs text-red-400 hover:text-red-600 transition">Remover</button>
                        </div>

                        <div class="grid grid-cols-3 gap-2 mb-3">
                            <input v-model="dest.nome" placeholder="Nome do destino" class="col-span-2 px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            <select v-model="dest.tipo" class="px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                <option value="escola">Escola</option>
                                <option value="atividade">Atividade</option>
                                <option value="outro">Outro</option>
                            </select>
                        </div>

                        <div class="relative mb-3">
                            <input
                                type="text"
                                placeholder="Buscar endereço do destino..."
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                @input="buscarEndereco(`dest_${index}`, $event.target.value)"
                                @blur="fecharSugestoes(`dest_${index}`)"
                            />
                            <ul v-if="sugestoes[`dest_${index}`]?.length" class="absolute z-20 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden">
                                <li
                                    v-for="s in sugestoes[`dest_${index}`]" :key="s.place_id"
                                    @mousedown="selecionarSugestao(`dest_${index}`, s, dest)"
                                    class="px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 cursor-pointer border-b border-slate-100 last:border-0"
                                >📍 {{ s.display_name }}</li>
                            </ul>
                        </div>

                        <div class="grid grid-cols-3 gap-2">
                            <input v-model="dest.logradouro" placeholder="Logradouro" class="col-span-2 px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            <input v-model="dest.numero" placeholder="Número" class="px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            <input v-model="dest.bairro" placeholder="Bairro" class="px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            <input v-model="dest.cidade" placeholder="Cidade" class="px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            <input v-model="dest.estado" placeholder="UF" maxlength="2" class="px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition uppercase" />
                            <input v-model="dest.cep" @input="maskCep(dest, $event)" placeholder="CEP" class="px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button
                        @click="salvarEnderecos"
                        :disabled="formEnderecos.processing"
                        class="bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition shadow-sm shadow-blue-200"
                        style="font-family:'Sora',sans-serif;"
                    >
                        {{ formEnderecos.processing ? 'Salvando...' : 'Salvar destinos' }}
                    </button>
                </div>
            </div>

            <!-- ── SEÇÃO: VÍNCULO ─────────────────────────────────────────── -->
            <div v-else-if="secaoAberta === 'vinculos'" class="space-y-4">

                <!-- Vínculo ativo -->
                <div v-if="vinculoAtivo" class="bg-white border border-emerald-200 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <h2 class="text-base font-semibold text-slate-800" style="font-family:'Sora',sans-serif;">Vínculo ativo</h2>
                    </div>

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between py-2 border-b border-slate-100">
                            <span class="text-slate-500">Van</span>
                            <span class="font-medium text-slate-800">{{ vinculoAtivo.van?.placa }} — {{ vinculoAtivo.van?.modelo }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-100">
                            <span class="text-slate-500">Motorista</span>
                            <span class="font-medium text-slate-800">{{ vinculoAtivo.van?.nome_motorista }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-100">
                            <span class="text-slate-500">Valor mensal</span>
                            <span class="font-medium text-slate-800">R$ {{ vinculoAtivo.preco_total }}</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-slate-500">Desde</span>
                            <span class="font-medium text-slate-800">{{ vinculoAtivo.data_inicio }}</span>
                        </div>
                    </div>
                </div>

                <!-- Sem vínculo -->
                <div v-else class="bg-white border border-slate-200 border-dashed rounded-2xl p-10 text-center shadow-sm">
                    <div class="mx-auto mb-4 h-12 w-12 rounded-2xl bg-blue-50 ring-1 ring-blue-100"></div>
                    <p class="font-semibold text-slate-700" style="font-family:'Sora',sans-serif;">Sem van vinculada</p>
                    <p class="text-sm text-slate-400 mt-1 mb-4">Busque uma van disponível para vincular este passageiro.</p>
                    <Link
                        :href="route('responsavel.dashboard')"
                        class="inline-flex bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition shadow-sm shadow-blue-200"
                        style="font-family:'Sora',sans-serif;"
                    >
                        Buscar van no dashboard
                    </Link>
                </div>

                <!-- Histórico de vínculos -->
                <div v-if="passageiro.vinculos?.length > 0" class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-slate-700 mb-3">Histórico de vínculos</h3>
                    <div class="space-y-2">
                        <div
                            v-for="v in passageiro.vinculos" :key="v.id_vinculo"
                            class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0 text-sm"
                        >
                            <span class="text-slate-700">{{ v.van?.placa }} — {{ v.van?.nome_motorista }}</span>
                            <span
                                class="text-xs px-2 py-0.5 rounded-full border"
                                :class="{
                                    'bg-emerald-50 text-emerald-700 border-emerald-200': v.status === 'ativo',
                                    'bg-amber-50 text-amber-700 border-amber-200': v.status === 'suspenso',
                                    'bg-slate-50 text-slate-500 border-slate-200': v.status === 'encerrado',
                                }"
                            >{{ v.status }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>
