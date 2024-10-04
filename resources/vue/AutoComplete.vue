<!--
SPDX-FileCopyrightText: 2023 Andreas Palm

SPDX-License-Identifier: AGPL-3.0-only
-->

<script setup>
import {ref} from "vue";
import AutoComplete from "primevue/autocomplete";
import axios from "axios";
import SearchIcon from "@primevue/icons/search";

const props = defineProps({
  ajaxFilter: String,
  modelValue: null,
  forceSelection: Boolean,
  inputId: String,
});
const emit = defineEmits(['update:modelValue']);

const items = ref([]);
async function search(event) {
  await axios.get('index.php',
      {
        params: {
          module: 'ajax',
          action: 'filter',
          filtername: props.ajaxFilter,
          term: event.query,
          object: true
        }
      })
      .then(response => items.value = response.data)
}
</script>

<template>
  <AutoComplete
      :modelValue="modelValue"
      @update:modelValue="value => emit('update:modelValue', value)"
      :suggestions="items"
      @complete="search"
      dataKey="id"
      :forceSelection="forceSelection"
      dropdown
      :input-id="inputId"
  >
    <template #dropdownicon>
      <SearchIcon />
    </template>
  </AutoComplete>
</template>