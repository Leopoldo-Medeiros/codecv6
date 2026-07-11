<template>
  <div class="tui-field" @click.stop="focus()">
    <div class="tui-field__head">
      <label :for="id" class="tui-field__label">{{ label }}</label>
      <span v-if="hint" class="tui-field__hint">({{ hint }})</span>
      <span v-if="done" class="tui-field__ok" aria-hidden="true">✔</span>
      <button
        v-if="revealable && modelValue"
        type="button"
        class="tui-field__reveal"
        :aria-pressed="revealed"
        :aria-label="revealed ? 'Hide password' : 'Show password'"
        tabindex="-1"
        @click.stop="revealed = !revealed"
      >[{{ revealed ? 'hide' : 'show' }}]</button>
    </div>
    <div class="tui-field__box">
      <span class="tui-field__prefix" aria-hidden="true">›</span>
      <input
        :id="id"
        ref="inputEl"
        class="tui-field__input"
        :type="revealed ? 'text' : type"
        :value="modelValue"
        :placeholder="placeholder"
        :autocomplete="autocomplete"
        :enterkeyhint="enterkeyhint"
        :disabled="disabled"
        spellcheck="false"
        autocapitalize="off"
        @input="onInput"
        @focus="$emit('focus')"
        @keydown.enter.prevent="$emit('enter')"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
withDefaults(defineProps<{
  id: string
  label: string
  modelValue: string
  type?: string
  placeholder?: string
  autocomplete?: string
  enterkeyhint?: 'next' | 'go' | 'done' | 'enter' | 'send'
  hint?: string
  done?: boolean
  disabled?: boolean
  /** show a [show]/[hide] toggle (password fields) */
  revealable?: boolean
}>(), {
  type: 'text',
  placeholder: '',
  autocomplete: 'off',
  enterkeyhint: 'next',
  hint: '',
  done: false,
  disabled: false,
  revealable: false,
})

const emit = defineEmits<{
  'update:modelValue': [value: string]
  'enter': []
  'focus': []
}>()

const inputEl = ref<HTMLInputElement | null>(null)
const revealed = ref(false)

function onInput(e: Event) {
  emit('update:modelValue', (e.target as HTMLInputElement).value)
}

function focus() {
  inputEl.value?.focus()
}

defineExpose({ focus })
</script>
