<script setup>
import { ref, computed } from 'vue'
import { CameraIcon, CheckCircleIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    modelValue: { type: File, default: null },
    initialUrl: { type: String, default: null },
    label:      { type: String, required: true },
    error:      { type: String, default: null },
})
const emit = defineEmits(['update:modelValue'])

const fileInput     = ref(null)
const localPreview  = ref(null)
const preview       = computed(() => localPreview.value ?? props.initialUrl)

function handleChange(event) {
    const file = event.target.files[0]
    if (!file) return
    localPreview.value = URL.createObjectURL(file)
    emit('update:modelValue', file)
}
</script>

<template>
    <div>
        <button type="button" @click="fileInput.click()"
            class="relative group w-full rounded-xl overflow-hidden focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-600">
            <div class="aspect-square rounded-xl overflow-hidden flex items-center justify-center border-2 transition"
                :class="preview ? 'border-amber-300 bg-amber-50' : 'border-dashed border-slate-300 bg-slate-50'">
                <img v-if="preview" :src="preview" class="w-full h-full object-cover" :alt="label" />
                <div v-else class="flex flex-col items-center gap-1">
                    <CameraIcon class="w-6 h-6 text-slate-300" />
                    <span class="text-xs text-slate-400">sem foto</span>
                </div>
            </div>
            <div class="absolute inset-0 bg-black/35 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                <CameraIcon class="w-5 h-5 text-white" />
            </div>
            <span v-if="preview"
                class="absolute top-1.5 right-1.5 inline-flex items-center justify-center w-5 h-5 rounded-full bg-green-500">
                <CheckCircleIcon class="w-3.5 h-3.5 text-white" />
            </span>
        </button>
        <p class="mt-1.5 text-xs font-semibold text-slate-700 text-center">{{ label }}</p>
        <p v-if="error" class="mt-0.5 text-xs text-red-500 text-center">{{ error }}</p>
        <input ref="fileInput" type="file" accept="image/jpeg,image/png,image/webp"
            class="hidden" @change="handleChange" />
    </div>
</template>
