<template>
    <div>
      <label :for="selectId">{{ label }}</label>
      <select
        :id="selectId"
        v-model="selected"
        @change="updateValue"
        class="browser-default"
      >
        <option v-if="defaultOption" :value="defaultOptionValue" disabled>{{ defaultOptionText }}</option>
        <option v-for="(option, index) in options" :key="index" :value="option.value">
          {{ option.name }}
        </option>
      </select>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      modelValue: {
        type: String,
        default: null,
      },
      options: {
        type: Array,
        required: true,
      },
      label: {
        type: String,
        default: 'Select an option',
      },
      defaultOption: {
        type: Boolean,
        default: true,
      },
      defaultOptionText: {
        type: String,
        default: 'Choose an option',
      },
      defaultOptionValue: {
        type: String,
        default: '',
      },
    },
    data() {
      return {
        selected: this.modelValue || this.defaultOptionValue,
      };
    },
    computed: {
      selectId() {
        return `select-${Math.random().toString(36).substr(2, 9)}`;
      },
    },
    watch: {
      modelValue(value) {
        this.selected = value;
      },
    },
    methods: {
      updateValue() {
        this.$emit('update:modelValue', this.selected);
      },
    },
  };
  </script>

<style scoped>
select.browser-default {
  background-color: #41333338;
  color: rgb(75, 192, 192);
  border: 1px solid #6d6666;
  border-radius: 4px;
  padding: 8px 12px;
}
</style>
  