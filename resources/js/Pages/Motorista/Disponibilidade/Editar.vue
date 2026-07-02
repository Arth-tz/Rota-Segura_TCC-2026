<script setup>
import { useForm, Head, Link, router } from '@inertiajs/vue3'
import { ArrowLeftIcon, MapIcon, ClockIcon, CalendarDaysIcon, CurrencyDollarIcon, UsersIcon, TrashIcon, HomeModernIcon, AcademicCapIcon } from '@heroicons/vue/24/outline'
import { ref } from 'vue'
import TagInput from '@/Components/UI/TagInput.vue'

const props = defineProps({
    disponibilidade: { type: Object, required: true },
    capacidade_van:  { type: Number, default: null },
})

const DIAS = [
    { value: 'seg', label: 'Segunda' },
    { value: 'ter', label: 'Terça'   },
    { value: 'qua', label: 'Quarta'  },
    { value: 'qui', label: 'Quinta'  },
    { value: 'sex', label: 'Sexta'   },
    { value: 'sab', label: 'Sábado'  },
    { value: 'dom', label: 'Domingo' },
]

const TURNOS = [
    { value: 'manha', label: 'Manhã'  },
    { value: 'tarde', label: 'Tarde'  },
    { value: 'noite', label: 'Noite'  },
]

const form = useForm({
    nome:              props.disponibilidade.nome,
    turno:             props.disponibilidade.turno,
    dias:              [...props.disponibilidade.dias],
    preco_mensal:      props.disponibilidade.preco_mensal,
    capacidade_total:  props.disponibilidade.capacidade_total,
    ativa:             props.disponibilidade.ativa,
    bairros_atendidos: [...(props.disponibilidade.bairros_atendidos ?? [])],
    escolas_atendidas: [...(props.disponibilidade.escolas_atendidas ?? [])],
})

const confirmDelete = ref(false)

function submit() {
    form.put(route('motorista.disponibilidades.update', props.disponibilidade.id_disponibilidade))
}

function excluir() {
    router.delete(route('motorista.disponibilidades.destroy', props.disponibilidade.id_disponibilidade))
}
</script>

<template>
    <Head title="Editar trajeto" />

    <div class="min-h-screen bg-slate-50">

        <!-- Topbar -->
        <header class="bg-gradient-to-b from-amber-500 to-amber-600 shadow-sm">
            <div class="max-w-2xl mx-auto px-4 py-4 flex items-center gap-3">
                <Link :href="route('motorista.dashboard')"
                    class="w-9 h-9 rounded-xl bg-white/20 hover:bg-white/30 flex items-center justify-center transition shrink-0">
                    <ArrowLeftIcon class="w-5 h-5 text-white" />
                </Link>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-amber-200 uppercase tracking-widest">Motorista</p>
                    <h1 class="text-lg font-bold text-white truncate" style="font-family:'Sora',sans-serif;">
                        {{ disponibilidade.nome }}
                    </h1>
                </div>
                <button @click="confirmDelete = true"
                    class="w-9 h-9 rounded-xl bg-white/20 hover:bg-red-500/80 flex items-center justify-center transition shrink-0"
                    title="Excluir trajeto">
                    <TrashIcon class="w-5 h-5 text-white" />
                </button>
            </div>
        </header>

        <main class="max-w-2xl mx-auto px-4 py-6 space-y-4">

            <!-- Erro geral -->
            <div v-if="form.errors.geral"
                class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ form.errors.geral }}
            </div>

            <!-- Ativo toggle -->
            <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white shadow-sm px-5 py-4">
                <div>
                    <p class="text-sm font-bold text-slate-800" style="font-family:'Sora',sans-serif;">Trajeto ativo</p>
                    <p class="text-xs text-slate-500 mt-0.5">Aparecer nas buscas dos responsáveis</p>
                </div>
                <button type="button" @click="form.ativa = !form.ativa"
                    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200"
                    :class="form.ativa ? 'bg-amber-500' : 'bg-slate-200'">
                    <span class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow-md transition-transform duration-200"
                        :class="form.ativa ? 'translate-x-5' : 'translate-x-0'" />
                </button>
            </div>

            <!-- Nome -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-amber-100 bg-amber-50/60">
                    <div class="w-8 h-8 rounded-xl bg-amber-500 flex items-center justify-center shrink-0">
                        <MapIcon class="w-4 h-4 text-white" />
                    </div>
                    <h2 class="text-sm font-bold text-slate-800" style="font-family:'Sora',sans-serif;">Identificação</h2>
                </div>
                <div class="px-5 py-4">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Nome do trajeto</label>
                    <input v-model="form.nome" type="text" maxlength="100"
                        class="w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 outline-none transition"
                        :class="form.errors.nome ? 'border-red-300 bg-red-50' : 'border-slate-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-100'" />
                    <p v-if="form.errors.nome" class="mt-1 text-xs text-red-600">{{ form.errors.nome }}</p>
                </div>
            </div>

            <!-- Turno -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-amber-100 bg-amber-50/60">
                    <div class="w-8 h-8 rounded-xl bg-amber-500 flex items-center justify-center shrink-0">
                        <ClockIcon class="w-4 h-4 text-white" />
                    </div>
                    <h2 class="text-sm font-bold text-slate-800" style="font-family:'Sora',sans-serif;">Turno</h2>
                </div>
                <div class="px-5 py-4">
                    <div class="grid grid-cols-3 gap-3">
                        <button v-for="t in TURNOS" :key="t.value" type="button"
                            @click="form.turno = t.value"
                            class="rounded-xl border py-3 text-sm font-semibold transition"
                            :class="form.turno === t.value
                                ? 'border-amber-400 bg-amber-50 text-amber-700 shadow-sm'
                                : 'border-slate-200 text-slate-600 hover:border-amber-200 hover:bg-amber-50/50'">
                            {{ t.label }}
                        </button>
                    </div>
                    <p v-if="form.errors.turno" class="mt-2 text-xs text-red-600">{{ form.errors.turno }}</p>
                </div>
            </div>

            <!-- Dias -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-amber-100 bg-amber-50/60">
                    <div class="w-8 h-8 rounded-xl bg-amber-500 flex items-center justify-center shrink-0">
                        <CalendarDaysIcon class="w-4 h-4 text-white" />
                    </div>
                    <h2 class="text-sm font-bold text-slate-800" style="font-family:'Sora',sans-serif;">Dias da semana</h2>
                </div>
                <div class="px-5 py-4">
                    <div class="grid grid-cols-4 sm:grid-cols-7 gap-2">
                        <button v-for="d in DIAS" :key="d.value" type="button"
                            @click="form.dias.includes(d.value) ? form.dias = form.dias.filter(x => x !== d.value) : form.dias.push(d.value)"
                            class="rounded-xl border py-2.5 text-xs font-semibold transition"
                            :class="form.dias.includes(d.value)
                                ? 'border-amber-400 bg-amber-500 text-white shadow-sm'
                                : 'border-slate-200 text-slate-600 hover:border-amber-200 hover:bg-amber-50/50'">
                            {{ d.label.slice(0, 3) }}
                        </button>
                    </div>
                    <p v-if="form.errors.dias" class="mt-2 text-xs text-red-600">{{ form.errors.dias }}</p>
                </div>
            </div>

            <!-- Preço e capacidade -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-amber-100 bg-amber-50/60">
                    <div class="w-8 h-8 rounded-xl bg-amber-500 flex items-center justify-center shrink-0">
                        <CurrencyDollarIcon class="w-4 h-4 text-white" />
                    </div>
                    <h2 class="text-sm font-bold text-slate-800" style="font-family:'Sora',sans-serif;">Preço e vagas</h2>
                </div>
                <div class="px-5 py-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Preço mensal (R$)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400 pointer-events-none">R$</span>
                            <input v-model="form.preco_mensal" type="number" min="0" max="9999.99" step="0.01"
                                class="w-full rounded-xl border pl-9 pr-4 py-2.5 text-sm text-slate-900 outline-none transition"
                                :class="form.errors.preco_mensal ? 'border-red-300 bg-red-50' : 'border-slate-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-100'" />
                        </div>
                        <p v-if="form.errors.preco_mensal" class="mt-1 text-xs text-red-600">{{ form.errors.preco_mensal }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">
                            Capacidade
                            <span v-if="capacidade_van" class="normal-case text-slate-400 font-normal">(máx. {{ capacidade_van }})</span>
                        </label>
                        <div class="relative">
                            <UsersIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                            <input v-model="form.capacidade_total" type="number" min="1" :max="capacidade_van ?? 99"
                                class="w-full rounded-xl border pl-9 pr-4 py-2.5 text-sm text-slate-900 outline-none transition"
                                :class="form.errors.capacidade_total ? 'border-red-300 bg-red-50' : 'border-slate-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-100'" />
                        </div>
                        <p v-if="form.errors.capacidade_total" class="mt-1 text-xs text-red-600">{{ form.errors.capacidade_total }}</p>
                    </div>
                </div>
            </div>

            <!-- Bairros e escolas -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-amber-100 bg-amber-50/60">
                    <div class="w-8 h-8 rounded-xl bg-amber-500 flex items-center justify-center shrink-0">
                        <HomeModernIcon class="w-4 h-4 text-white" />
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800" style="font-family:'Sora',sans-serif;">Área de atuação</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Ajuda responsáveis a te encontrarem no marketplace</p>
                    </div>
                </div>
                <div class="px-5 py-4 space-y-4">
                    <div>
                        <label class="flex items-center gap-1.5 text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">
                            <HomeModernIcon class="w-3.5 h-3.5" /> Bairros atendidos
                        </label>
                        <TagInput v-model="form.bairros_atendidos" placeholder="Ex: Igara · pressione Enter para adicionar" />
                        <p v-if="form.errors.bairros_atendidos" class="mt-1 text-xs text-red-600">{{ form.errors.bairros_atendidos }}</p>
                    </div>
                    <div>
                        <label class="flex items-center gap-1.5 text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">
                            <AcademicCapIcon class="w-3.5 h-3.5" /> Escolas atendidas
                        </label>
                        <TagInput v-model="form.escolas_atendidas" placeholder="Ex: E.M. João da Silva · pressione Enter" />
                        <p v-if="form.errors.escolas_atendidas" class="mt-1 text-xs text-red-600">{{ form.errors.escolas_atendidas }}</p>
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
                    class="flex-1 flex items-center justify-center rounded-xl bg-amber-500 hover:bg-amber-600 disabled:opacity-60 py-3 text-sm font-bold text-white transition shadow-sm"
                    style="font-family:'Sora',sans-serif;">
                    {{ form.processing ? 'Salvando…' : 'Salvar alterações' }}
                </button>
            </div>

        </main>
    </div>

    <!-- Modal confirmação exclusão -->
    <Teleport to="body">
        <div v-if="confirmDelete"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
            @click.self="confirmDelete = false">
            <div class="w-full max-w-sm rounded-2xl bg-white shadow-xl p-6">
                <h3 class="font-bold text-slate-900 text-lg mb-2" style="font-family:'Sora',sans-serif;">Excluir trajeto?</h3>
                <p class="text-sm text-slate-500 mb-6">Esta ação não pode ser desfeita. O trajeto será removido permanentemente.</p>
                <div class="flex gap-3">
                    <button @click="confirmDelete = false"
                        class="flex-1 rounded-xl border border-slate-200 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                        Cancelar
                    </button>
                    <button @click="excluir"
                        class="flex-1 rounded-xl bg-red-500 hover:bg-red-600 py-3 text-sm font-bold text-white transition">
                        Excluir
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>
