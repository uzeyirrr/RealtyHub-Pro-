<script setup>
import { onMounted, ref } from 'vue';

defineProps({
  modelValue: {
    type: [String, Number],
    required: true,
  },
  options: {
    type: Array,
    required: true,
  },
  valueField: {
    type: String,
    default: 'id',
  },
  labelField: {
    type: String,
    default: 'name',
  },
  placeholder: {
    type: String,
    default: 'Seçiniz',
  },
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
  if (input.value.hasAttribute('autofocus')) {
    input.value.focus();
  }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
  <select
    ref="input"
    class="border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm"
    :value="modelValue"
    @change="$emit('update:modelValue', $event.target.value)"
  >
    <option value="">{{ placeholder }}</option>
    <option
      v-for="option in options"
      :key="option[valueField]"
      :value="option[valueField]"
    >
      {{ option[labelField] }}
    </option>
  </select>
</template> 