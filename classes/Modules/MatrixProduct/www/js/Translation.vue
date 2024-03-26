<!--
SPDX-FileCopyrightText: 2023 Andreas Palm

SPDX-License-Identifier: LicenseRef-EGPL-3.1
-->

<script setup>
import {ref, onMounted} from "vue";
import axios from "axios";
import Dialog from "primevue/dialog";
import Button from "primevue/button";
import Dropdown from "primevue/dropdown";
import {AlertErrorHandler} from "@res/js/ajaxErrorHandler";


const props = defineProps({
  type: String,
  id: String,
});
const emit = defineEmits(['save', 'close']);

const model = ref({});
const languages = ref([]);

onMounted(async () => {
  if (props.id > 0) {
    const url = 'index.php?module=matrixprodukt&action=translation&cmd=edit';
    model.value = await axios.get(url, {
      params: props
    }).then(response => response.data)
  }
  axios.get('index.php',
      {
        params: {
          module: 'ajax',
          action: 'filter',
          filtername: 'activelanguages',
          object: true
        }
      }).then(response => {
       languages.value = response.data;
  });
})

async function save() {
  if (!parseInt(props.id) > 0)
    model.value.id = 0;
  const url = 'index.php?module=matrixprodukt&action=translation&cmd=save';
  await axios.post(url, {...props, ...model.value})
      .catch(AlertErrorHandler)
      .then(() => {emit('save')});
}

function ready() {
  if (model.value.nameExternalFrom && !model.value.nameExternalTo)
    return false;
  return model.value.languageTo && model.value.nameFrom && model.value.nameTo;
}
</script>

<template>
  <Dialog visible modal header="Übersetzung anlegen/bearbeiten" style="width: 500px" @update:visible="emit('close')" class="p-fluid">
    <div class="grid gap-1" style="grid-template-columns: 25% 75%">
      <label for="matrixProduct_nameFrom">DE Name:</label>
      <input type="text" v-model="model.nameFrom" required />
      <label for="matrixProduct_nameExternalFrom">DE Name Extern:</label>
      <input type="text" v-model="model.nameExternalFrom" />
      <label for="matrixProduct_languageTo">Sprache:</label>
      <Dropdown
          v-model="model.languageTo"
          :options="languages"
          option-label="bezeichnung_de"
          option-value="iso"
      />
      <label for="matrixProduct_nameTo">Übersetzung Name:</label>
      <input type="text" v-model="model.nameTo" required>
      <label for="matrixProduct_nameTo">Übersetzung Name Extern:</label>
      <input type="text" v-model="model.nameExternalTo" :required="model.nameExternalFrom">
    </div>
    <template #footer>
      <Button label="ABBRECHEN" @click="emit('close')" />
      <Button label="SPEICHERN" @click="save" :disabled="!ready()"/>
    </template>
  </Dialog>
</template>
