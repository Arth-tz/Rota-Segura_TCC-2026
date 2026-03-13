<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3'

const form = useForm({
    first_name: '',
    last_name: '',
    data_nascimento: '',
    escola_nome: '',
    escola_endereco: '',
    turno: '',
    serie_ano: '',
    endereco_embarque: '',
    endereco_desembarque: '',
    obs_medica: '',
    ctt_emergencia: '',
    tel_emergencia: '',
})

const submit = () => {
    form.post(route('responsavel.alunos.store'))
}
</script>

<template>
    <Head title="Cadastrar Filho" />

    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Nunito:wght@300;400;500;600&display=swap" rel="stylesheet" />

    <div class="min-h-screen bg-slate-50 flex">

        <!-- Lado esquerdo -->
        <div class="hidden lg:flex lg:w-5/12 bg-blue-700 flex-col justify-between p-12 rounded-tr-3xl rounded-br-3xl relative overflow-hidden bg-[radial-gradient(circle_at_top_left,_#3b82f6,_#1d4ed8_60%)] z-10 shadow-[10px_0_50px_-15px_rgba(0,0,0,0.3)]">

            <Link href="http://localhost/rota-segura/public/" class="flex items-center gap-2 hover:opacity-80 transition-all duration-500 ease-in-out">
                <img src="/rota-segura/public/images/Logo_rota-segura_branco.png" alt="Logo Rota Segura" class="h-12 w-12">
                <div class="text-slate-50 font-bold text-lg tracking-tight" style="font-family:'Sora', sans-serif;">Rota Segura</div>
            </Link>

            <div class="relative z-10">
                <h2 class="text-4xl font-extrabold text-white leading-tight mb-6" style="font-family: 'Sora', sans-serif;">
                    Quase lá! Agora cadastre seu filho.
                </h2>

                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white/50 rounded-lg flex items-center justify-center flex-shrink-0">✓</div>
                        <p class="text-blue-100 text-sm">Seus dados foram salvos com sucesso</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white/50 rounded-lg flex items-center justify-center flex-shrink-0">→</div>
                        <p class="text-blue-100 text-sm">Cadastre seu filho para continuar</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white/50 rounded-lg flex items-center justify-center flex-shrink-0">◎</div>
                        <p class="text-blue-100 text-sm">Depois encontre uma van na sua região</p>
                    </div>
                </div>
            </div>

            <p class="relative z-10 text-blue-200 text-xs">© 2026 Rota Segura.</p>
        </div>

        <!-- Lado direito -->
        <div class="flex-1 flex items-center justify-center px-6 py-12">
            <div class="w-full max-w-lg">

                <!-- Header mobile -->
                <div class="lg:hidden flex items-center gap-2 mb-8">
                    <img src="/rota-segura/public/images/Logo_rota-segura_azul.png" alt="Logo Rota Segura" class="h-12 w-12">
                    <span class="text-slate-900 font-bold" style="font-family: 'Sora', sans-serif;">Rota Segura</span>
                </div>

                <h1 class="text-4xl font-bold text-center text-slate-900 mb-1" style="font-family: 'Sora', sans-serif;">
                    Cadastrar filho
                </h1>
                <p class="text-slate-400 text-sm mb-8 text-center">
                    Preencha os dados do seu filho para encontrar uma van.
                </p>

                <form @submit.prevent="submit" class="space-y-4">

                    <!-- Nome e Sobrenome -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Nome</label>
                            <input
                                v-model="form.first_name"
                                type="text"
                                placeholder="João"
                                required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-900 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                :class="{ 'border-red-400 focus:ring-red-400': form.errors.first_name }"
                            />
                            <p v-if="form.errors.first_name" class="text-red-500 text-xs mt-1">{{ form.errors.first_name }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Sobrenome</label>
                            <input
                                v-model="form.last_name"
                                type="text"
                                placeholder="Silva"
                                required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-900 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                :class="{ 'border-red-400 focus:ring-red-400': form.errors.last_name }"
                            />
                            <p v-if="form.errors.last_name" class="text-red-500 text-xs mt-1">{{ form.errors.last_name }}</p>
                        </div>
                    </div>

                    <!-- Data de nascimento e Turno -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Data de nascimento</label>
                            <input
                                v-model="form.data_nascimento"
                                type="date"
                                required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                :class="{ 'border-red-400 focus:ring-red-400': form.errors.data_nascimento }"
                            />
                            <p v-if="form.errors.data_nascimento" class="text-red-500 text-xs mt-1">{{ form.errors.data_nascimento }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Turno</label>
                            <select
                                v-model="form.turno"
                                required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                :class="{ 'border-red-400 focus:ring-red-400': form.errors.turno }"
                            >
                                <option value="" disabled>Selecione</option>
                                <option value="manha">Manhã</option>
                                <option value="tarde">Tarde</option>
                                <option value="noite">Noite</option>
                            </select>
                            <p v-if="form.errors.turno" class="text-red-500 text-xs mt-1">{{ form.errors.turno }}</p>
                        </div>
                    </div>

                    <!-- Nome da escola e Série -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Nome da escola</label>
                            <input
                                v-model="form.escola_nome"
                                type="text"
                                placeholder="E.E. João da Silva"
                                required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-900 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                :class="{ 'border-red-400 focus:ring-red-400': form.errors.escola_nome }"
                            />
                            <p v-if="form.errors.escola_nome" class="text-red-500 text-xs mt-1">{{ form.errors.escola_nome }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Série/Ano</label>
                            <input
                                v-model="form.serie_ano"
                                type="text"
                                placeholder="5º ano"
                                required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-900 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                :class="{ 'border-red-400 focus:ring-red-400': form.errors.serie_ano }"
                            />
                            <p v-if="form.errors.serie_ano" class="text-red-500 text-xs mt-1">{{ form.errors.serie_ano }}</p>
                        </div>
                    </div>

                    <!-- Endereço da escola -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Endereço da escola</label>
                        <input
                            v-model="form.escola_endereco"
                            type="text"
                            placeholder="Rua das Flores, 123 - Bairro"
                            required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-900 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            :class="{ 'border-red-400 focus:ring-red-400': form.errors.escola_endereco }"
                        />
                        <p v-if="form.errors.escola_endereco" class="text-red-500 text-xs mt-1">{{ form.errors.escola_endereco }}</p>
                    </div>

                    <!-- Endereços de embarque e desembarque -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Endereço de embarque</label>
                        <input
                            v-model="form.endereco_embarque"
                            type="text"
                            placeholder="Onde o motorista busca seu filho"
                            required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-900 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            :class="{ 'border-red-400 focus:ring-red-400': form.errors.endereco_embarque }"
                        />
                        <p v-if="form.errors.endereco_embarque" class="text-red-500 text-xs mt-1">{{ form.errors.endereco_embarque }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Endereço de desembarque</label>
                        <input
                            v-model="form.endereco_desembarque"
                            type="text"
                            placeholder="Onde o motorista deixa seu filho"
                            required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-900 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            :class="{ 'border-red-400 focus:ring-red-400': form.errors.endereco_desembarque }"
                        />
                        <p v-if="form.errors.endereco_desembarque" class="text-red-500 text-xs mt-1">{{ form.errors.endereco_desembarque }}</p>
                    </div>

                    <!-- Contato e telefone de emergência -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Contato de emergência</label>
                            <input
                                v-model="form.ctt_emergencia"
                                type="text"
                                placeholder="Nome do contato"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-900 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                :class="{ 'border-red-400 focus:ring-red-400': form.errors.ctt_emergencia }"
                            />
                            <p v-if="form.errors.ctt_emergencia" class="text-red-500 text-xs mt-1">{{ form.errors.ctt_emergencia }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Telefone de emergência</label>
                            <input
                                v-model="form.tel_emergencia"
                                type="text"
                                placeholder="(00) 00000-0000"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-900 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                :class="{ 'border-red-400 focus:ring-red-400': form.errors.tel_emergencia }"
                            />
                            <p v-if="form.errors.tel_emergencia" class="text-red-500 text-xs mt-1">{{ form.errors.tel_emergencia }}</p>
                        </div>
                    </div>

                    <!-- Observação médica -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Observação médica <span class="text-slate-400 normal-case font-normal">(opcional)</span></label>
                        <textarea
                            v-model="form.obs_medica"
                            rows="3"
                            placeholder="Alergias, medicamentos, necessidades especiais..."
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-900 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"
                            :class="{ 'border-red-400 focus:ring-red-400': form.errors.obs_medica }"
                        ></textarea>
                        <p v-if="form.errors.obs_medica" class="text-red-500 text-xs mt-1">{{ form.errors.obs_medica }}</p>
                    </div>

                    <!-- Erro geral -->
                    <div v-if="form.errors.geral" class="px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
                        {{ form.errors.geral }}
                    </div>

                    <!-- Botão -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full bg-blue-600 hover:bg-blue-700 disabled:opacity-60 disabled:cursor-not-allowed text-white font-semibold py-3.5 rounded-xl transition-all text-sm shadow-lg shadow-blue-200 mt-2"
                        style="font-family: 'Sora', sans-serif;"
                    >
                        {{ form.processing ? 'Salvando...' : 'Cadastrar filho' }}
                    </button>

                </form>

            </div>
        </div>

    </div>
</template>