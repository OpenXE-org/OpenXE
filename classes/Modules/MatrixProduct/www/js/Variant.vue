<!--
SPDX-FileCopyrightText: 2023-2024 Andreas Palm

SPDX-License-Identifier: LicenseRef-EGPL-3.1
-->

<script setup>
import AutoComplete from "@res/vue/AutoComplete.vue";
import Button from "primevue/button";
import Dialog from "primevue/dialog";
import Select from "primevue/select";
import Fluid from "primevue/fluid";
import {onMounted, ref} from "vue";
import axios from "axios";
import {AlertErrorHandler} from "@res/js/ajaxErrorHandler";

const props = defineProps({
  articleId: String,
  variantId: String,
});
const emit = defineEmits(['save', 'close']);

const model = ref({});

onMounted(async () => {
  model.value = await axios.get('index.php?module=matrixprodukt&action=artikel&cmd=variantedit', {
    params: {...props}
  }).then(response => { return {...props, ...response.data}})
})

async function save() {
  await axios.post('index.php?module=matrixprodukt&action=artikel&cmd=variantsave', {...props, ...model.value})
      .catch(AlertErrorHandler)
      .then(() => {emit('save')});
}

const buttons = {
  abbrechen: () => emit('close'),
  speichern: save
}
</script>

<template>
  <Dialog visible modal header="Variante" style="width: 500px" @update:visible="emit('close')">
    <Fluid>
      <div class="grid gap-1" style="grid-template-columns: 25% 75%;">
        <label>Artikel</label>
        <AutoComplete v-model="model.variant"
                      :option-label="(item) => [item.nummer, item.name].join(' ')"
                      ajax-filter="artikelnummer"
                      force-selection
                      autofocus
        />
        <template v-for="group in model.groups">
          <label>{{ group.name }}</label>
          <Select v-model="group.selected" :options="group.options" option-label="name" option-value="value" />
        </template>
      </div>
    </Fluid>
    <template #footer>
      <Button label="ABBRECHEN" @click="emit('close')" />
      <Button label="SPEICHERN" @click="save" />
    </template>
  </Dialog>
</template>