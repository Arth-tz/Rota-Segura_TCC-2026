<script setup>
import { ref, computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { TruckIcon, ArrowLeftIcon, CameraIcon, PencilSquareIcon } from '@heroicons/vue/24/outline'
import FlashMessage from '@/Components/UI/FlashMessage.vue'

const props = defineProps({
    van: { type: Object, required: true },
})

const form = useForm({
    nome_servico:            props.van.nome_servico   ?? '',
    marca:                   props.van.marca           ?? '',
    modelo:                  props.van.modelo          ?? '',
    ano_fabricacao:          props.van.ano_fabricacao  ?? '',
    cor:                     props.van.cor             ?? '',
    capacidade_passageiros:  props.van.capacidade_passageiros ?? '',
    foto:                    null,
})

const fotoPreview = ref(null)
const fileInput   = ref(null)

const fotoAtual = computed(() => fotoPreview.value ?? props.van.foto_url ?? null)

function handleFotoChange(event) {
    const file = event.target.files[0]
    if (!file) return
    fotoPreview.value = URL.createObjectURL(file)
    form.foto = file
}

function submit() {
    form.transform(data => data).put(route('motorista.van.update'), {
        forceFormData: true,
        onSuccess: () => { fotoPreview.value = null },
    })
}

const marcasComuns = ['Mercedes-Benz', 'Volkswagen', 'Fiat', 'Iveco', 'Renault', 'Toyota', 'Outro']
const coresComuns  = ['Branco', 'Prata', 'Preto', 'Cinza', 'Azul', 'Vermelho', 'Amarelo', 'Outro']
</script>

<template>
    <Head title="Editar Van — Motorista" />
    <FlashMessage />

    <div class="min-h-screen bg-slate-50">

        <!-- Topbar -->
        <header class="bg-gradient-to-b from-amber-500 to-amber-600 shadow-sm">
            <div class="max-w-2xl mx-auto px-4 py-4 flex items-center gap-3">
                <Link :href="route('motorista.dashboard')"
                    class="w-9 h-9 rounded-xl bg-white/20 hover:bg-white/30 flex items-center justify-center transition shrink-0">
                    <ArrowLeftIcon class="w-5 h-5 text-white" />
                </Link>
                <div>
                    <p class="text-xs font-semibold text-amber-200 uppercase tracking-widest">Motorista</p>
                    <h1 class="text-lg font-bold text-white">Editar van</h1>
                </div>
                <div class="ml-auto">
                    <span class="text-xs px-2.5 py-1 rounded-full border font-semibold"
                        :class="{
                            'bg-amber-100 text-amber-800 border-amber-200': van.status_aprovacao === 'pendente',
                            'bg-emerald-100 text-emerald-700 border-emerald-200': van.status_aprovacao === 'aprovado',
                            'bg-red-100 text-red-700 border-red-200': van.status_aprovacao === 'rejeitado',
                        }">
                        {{ van.status_aprovacao === 'pendente' ? 'Aguardando aprovação'
                         : van.status_aprovacao === 'aprovado' ? 'Aprovada'
                         : 'Rejeitada' }}
                    </span>
                </div>
            </div>
        </header>

        <main class="max-w-2xl mx-auto px-4 py-6 space-y-4">

            <!-- Erro geral -->
            <div v-if="form.errors.geral"
                class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ form.errors.geral }}
            </div>

            <!-- Foto da van -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-amber-100 bg-amber-50/60">
                    <div class="w-8 h-8 rounded-xl bg-amber-500 flex items-center justify-center shrink-0">
                        <CameraIcon class="w-4 h-4 text-white" />
                    </div>
                    <h2 class="text-sm font-bold text-slate-800">Foto da van</h2>
                </div>

                <div class="px-5 py-5">
                    <div class="flex items-center gap-5">
                        <!-- Preview -->
                        <button type="button" @click="fileInput.click()"
                            class="relative group shrink-0 rounded-2xl focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400">
                            <div class="w-24 h-24 rounded-2xl overflow-hidden bg-amber-100 flex items-center justify-center">
                                <img v-if="fotoAtual" :src="fotoAtual" class="w-full h-full object-cover" alt="Foto da van" />
                                <TruckIcon v-else class="w-10 h-10 text-amber-300" />
                            </div>
                            <div class="absolute inset-0 rounded-2xl bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                <CameraIcon class="w-6 h-6 text-white" />
                            </div>
                        </button>

                        <div>
                            <p class="text-sm font-semibold text-slate-700">{{ van.nome_servico || van.placa }}</p>
                            <button type="button" @click="fileInput.click()"
                                class="text-xs text-amber-600 hover:text-amber-700 transition mt-0.5">
                                {{ fotoAtual ? 'Alterar foto' : 'Adicionar foto' }}
                            </button>
                            <p v-if="form.errors.foto" class="text-xs text-red-500 mt-1">{{ form.errors.foto }}</p>
                            <p class="text-xs text-slate-400 mt-1">JPG, PNG ou WebP · máx. 2 MB</p>
                        </div>

                        <input ref="fileInput" type="file" accept="image/jpeg,image/png,image/webp"
                            class="hidden" @change="handleFotoChange" />
                    </div>
                </div>
            </div>

            <!-- Dados do veículo -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-amber-100 bg-amber-50/60">
                    <div class="w-8 h-8 rounded-xl bg-amber-500 flex items-center justify-center shrink-0">
                        <TruckIcon class="w-4 h-4 text-white" />
                    </div>
                    <h2 class="text-sm font-bold text-slate-800">Dados do veículo</h2>
                </div>
                <div class="px-5 py-4 space-y-4">

                    <!-- Placa (somente leitura) -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Placa</label>
                        <input :value="van.placa" type="text" disabled
                            class="w-full rounded-xl border border-slate-100 bg-slate-50 px-4 py-2.5 text-sm font-mono uppercase tracking-widest text-slate-400 cursor-not-allowed" />
                        <p class="mt-1 text-xs text-slate-400">A placa não pode ser alterada.</p>
                    </div>

                    <!-- Nome do serviço -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">
                            Nome do serviço <span class="text-slate-400 normal-case">(opcional)</span>
                        </label>
                        <input v-model="form.nome_servico" type="text" maxlength="150"
                            placeholder="Ex: Van do Tio Marquinhos"
                            class="w-full rounded-xl border px-4 py-2.5 text-sm outline-none transition"
                            :class="form.errors.nome_servico ? 'border-red-300 bg-red-50' : 'border-slate-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-100'" />
                        <p v-if="form.errors.nome_servico" class="mt-1 text-xs text-red-600">{{ form.errors.nome_servico }}</p>
                    </div>

                    <!-- Marca + Modelo -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">
                                Marca <span class="text-red-400">*</span>
                            </label>
                            <select v-model="form.marca"
                                class="w-full rounded-xl border px-4 py-2.5 text-sm bg-white outline-none transition"
                                :class="form.errors.marca ? 'border-red-300 bg-red-50' : 'border-slate-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-100'">
                                <option value="">Selecione…</option>
                                <option v-for="m in marcasComuns" :key="m" :value="m">{{ m }}</option>
                            </select>
                            <p v-if="form.errors.marca" class="mt-1 text-xs text-red-600">{{ form.errors.marca }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">
                                Modelo <span class="text-red-400">*</span>
                            </label>
                            <input v-model="form.modelo" type="text" placeholder="Ex: Sprinter 415"
                                class="w-full rounded-xl border px-4 py-2.5 text-sm outline-none transition"
                                :class="form.errors.modelo ? 'border-red-300 bg-red-50' : 'border-slate-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-100'" />
                            <p v-if="form.errors.modelo" class="mt-1 text-xs text-red-600">{{ form.errors.modelo }}</p>
                        </div>
                    </div>

                    <!-- Ano + Cor -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">
                                Ano de fabricação <span class="text-red-400">*</span>
                            </label>
                            <input v-model="form.ano_fabricacao" type="number" placeholder="2020"
                                min="1990" :max="new Date().getFullYear() + 1"
                                class="w-full rounded-xl border px-4 py-2.5 text-sm outline-none transition"
                                :class="form.errors.ano_fabricacao ? 'border-red-300 bg-red-50' : 'border-slate-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-100'" />
                            <p v-if="form.errors.ano_fabricacao" class="mt-1 text-xs text-red-600">{{ form.errors.ano_fabricacao }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">
                                Cor <span class="text-red-400">*</span>
                            </label>
                            <select v-model="form.cor"
                                class="w-full rounded-xl border px-4 py-2.5 text-sm bg-white outline-none transition"
                                :class="form.errors.cor ? 'border-red-300 bg-red-50' : 'border-slate-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-100'">
                                <option value="">Selecione…</option>
                                <option v-for="c in coresComuns" :key="c" :value="c">{{ c }}</option>
                            </select>
                            <p v-if="form.errors.cor" class="mt-1 text-xs text-red-600">{{ form.errors.cor }}</p>
                        </div>
                    </div>

                    <!-- Capacidade -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">
                            Capacidade de passageiros <span class="text-red-400">*</span>
                        </label>
                        <input v-model="form.capacidade_passageiros" type="number"
                            placeholder="Ex: 15" min="1" max="30"
                            class="w-full rounded-xl border px-4 py-2.5 text-sm outline-none transition"
                            :class="form.errors.capacidade_passageiros ? 'border-red-300 bg-red-50' : 'border-slate-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-100'" />
                        <p v-if="form.errors.capacidade_passageiros" class="mt-1 text-xs text-red-600">{{ form.errors.capacidade_passageiros }}</p>
                    </div>
                </div>
            </div>

            <!-- Botões -->
            <div class="flex gap-3 pb-6">
                <Link :href="route('motorista.dashboard')"
                    class="flex-1 flex items-center justify-center rounded-xl border border-slate-200 bg-white py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                    Cancelar
                </Link>
                <button @click="submit" :disabled="form.processing"
                    class="flex-1 flex items-center justify-center rounded-xl bg-amber-500 hover:bg-amber-600 disabled:opacity-60 py-3 text-sm font-bold text-white transition shadow-sm">
                    {{ form.processing ? 'Salvando…' : 'Salvar alterações' }}
                </button>
            </div>

        </main>
    </div>
</template>
