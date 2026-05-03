<script setup>
import { computed, ref } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'

const props = defineProps({
    passageiros: {
        type: Array,
        default: () => [],
    },
})

const page = usePage()
const usuario = computed(() => page.props.auth?.user ?? null)

const activeSection = ref('inicio')

const navItems = [
    { key: 'inicio', label: 'Início' },
    { key: 'passageiros', label: 'Meus Passageiros' },
    { key: 'buscar', label: 'Buscar Vans' },
    { key: 'perfil', label: 'Meu Perfil' },
]

function goTo(section) {
    activeSection.value = section
}

function statusClasses(color) {
    if (color === 'green') return 'bg-emerald-50 text-emerald-700 border-emerald-200'
    if (color === 'amber') return 'bg-amber-50 text-amber-700 border-amber-200'
    return 'bg-slate-50 text-slate-700 border-slate-200'
}
</script>

<template>
    <Head title="Dashboard do Responsável" />

    <div class="min-h-screen bg-slate-50">
        <div class="flex min-h-screen">
            <aside class="hidden md:flex md:w-72 bg-white border-r border-slate-200 flex-col p-6">
                <div class="mb-8">
                    <p class="text-xs uppercase tracking-widest text-blue-600 font-semibold">Responsável</p>
                    <h1 class="text-xl font-bold text-slate-900 mt-1">Rota Segura</h1>
                </div>

                <nav class="space-y-2">
                    <button
                        v-for="item in navItems"
                        :key="item.key"
                        @click="goTo(item.key)"
                        class="w-full text-left px-4 py-2.5 rounded-lg text-sm font-medium transition"
                        :class="activeSection === item.key ? 'bg-blue-600 text-white' : 'text-slate-700 hover:bg-slate-100'"
                    >
                        {{ item.label }}
                    </button>
                </nav>

                <div class="mt-auto pt-6 border-t border-slate-200 text-sm text-slate-600">
                    <p class="font-semibold text-slate-800">{{ usuario?.nome ?? 'Responsável' }}</p>
                    <p>{{ usuario?.email }}</p>
                </div>
            </aside>

            <main class="flex-1 px-4 md:px-8 py-6 pb-24 md:pb-8">
                <header class="mb-6">
                    <h2 class="text-2xl font-bold text-slate-900">
                        {{ activeSection === 'inicio' ? 'Início' : activeSection === 'passageiros' ? 'Meus Passageiros' : activeSection === 'buscar' ? 'Buscar Vans' : 'Meu Perfil' }}
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">Painel do responsável</p>
                </header>

                <section v-if="activeSection === 'inicio'" class="space-y-4">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <article v-for="p in passageiros" :key="p.id_passageiro" class="bg-white border border-slate-200 rounded-xl p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="h-12 w-12 rounded-full bg-slate-200 overflow-hidden flex-shrink-0">
                                        <img v-if="p.foto_url" :src="p.foto_url" alt="Foto passageiro" class="h-full w-full object-cover">
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-900 truncate">{{ p.nome }}</p>
                                        <p class="text-xs text-slate-500">Passageiro #{{ p.id_passageiro }}</p>
                                    </div>
                                </div>
                                <span class="text-xs px-2.5 py-1 rounded-full border font-medium" :class="statusClasses(p.status_color)">
                                    {{ p.status_label }}
                                </span>
                            </div>
                            <div class="mt-4 flex justify-end">
                                <button @click="goTo('passageiros')" class="text-sm font-semibold text-blue-600 hover:text-blue-700">
                                    Ver detalhes
                                </button>
                            </div>
                        </article>
                    </div>
                </section>

                <section v-else-if="activeSection === 'passageiros'" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-slate-600">Lista de passageiros cadastrados</p>
                        <Link :href="route('responsavel.passageiros.create')" class="rounded-lg bg-blue-600 text-white px-4 py-2 text-sm font-semibold hover:bg-blue-700">
                            Adicionar passageiro
                        </Link>
                    </div>

                    <div class="space-y-3">
                        <article v-for="p in passageiros" :key="p.id_passageiro" class="bg-white border border-slate-200 rounded-xl p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="h-11 w-11 rounded-full bg-slate-200 overflow-hidden flex-shrink-0">
                                        <img v-if="p.foto_url" :src="p.foto_url" alt="Foto passageiro" class="h-full w-full object-cover">
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-900 truncate">{{ p.nome }}</p>
                                        <span class="text-xs px-2 py-0.5 rounded-full border" :class="statusClasses(p.status_color)">{{ p.status_label }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button class="text-xs font-semibold text-blue-600">Detalhes</button>
                                    <button class="text-xs font-semibold text-slate-700">Vínculo</button>
                                    <button class="text-xs font-semibold text-slate-700">Buscar van</button>
                                </div>
                            </div>
                        </article>
                    </div>
                </section>

                <section v-else-if="activeSection === 'buscar'" class="bg-white border border-slate-200 rounded-xl p-5">
                    <h3 class="font-semibold text-slate-900 mb-1">Buscar Vans</h3>
                    <p class="text-sm text-slate-600">Marketplace em preparação. Nesta seção você poderá filtrar e solicitar vaga escolhendo para qual passageiro enviar.</p>
                </section>

                <section v-else class="bg-white border border-slate-200 rounded-xl p-5 space-y-3">
                    <h3 class="font-semibold text-slate-900">Meu Perfil</h3>
                    <p class="text-sm text-slate-700"><span class="font-medium">Nome:</span> {{ usuario?.nome ?? '-' }}</p>
                    <p class="text-sm text-slate-700"><span class="font-medium">E-mail:</span> {{ usuario?.email ?? '-' }}</p>
                    <Link :href="route('profile.edit')" class="inline-flex rounded-lg bg-blue-600 text-white px-4 py-2 text-sm font-semibold hover:bg-blue-700">
                        Editar perfil
                    </Link>
                </section>
            </main>
        </div>

        <nav class="md:hidden fixed bottom-0 inset-x-0 bg-white border-t border-slate-200 z-50">
            <div class="grid grid-cols-4">
                <button @click="goTo('inicio')" class="py-2 text-xs font-medium" :class="activeSection === 'inicio' ? 'text-blue-600' : 'text-slate-500'">Início</button>
                <button @click="goTo('passageiros')" class="py-2 text-xs font-medium" :class="activeSection === 'passageiros' ? 'text-blue-600' : 'text-slate-500'">Passageiros</button>
                <button @click="goTo('buscar')" class="py-2 text-xs font-medium" :class="activeSection === 'buscar' ? 'text-blue-600' : 'text-slate-500'">Buscar</button>
                <button @click="goTo('perfil')" class="py-2 text-xs font-medium" :class="activeSection === 'perfil' ? 'text-blue-600' : 'text-slate-500'">Perfil</button>
            </div>
        </nav>
    </div>
</template>
