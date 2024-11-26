<!--
SPDX-FileCopyrightText: 2023-2024 Andreas Palm

SPDX-License-Identifier: LicenseRef-EGPL-3.1
-->

<script setup>
import {ref, onMounted} from "vue";
import axios from "axios";
import Dialog from "primevue/dialog";
import Button from "primevue/button";
import Fluid from "primevue/fluid";
import InputText from "primevue/inputtext";
import Select from "primevue/select";
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
  <Dialog visible modal header="Übersetzung anlegen/bearbeiten" style="width: 500px" @update:visible="emit('close')">
    <Fluid>
      <div class="grid gap-1" style="grid-template-columns: 25% 75%">
        <label for="matrixProduct_nameFrom">DE Name:</label>
        <InputText v-model="model.nameFrom" required autofocus />
        <label for="matrixProduct_nameExternalFrom">DE Name Extern:</label>
        <InputText v-model="model.nameExternalFrom" />
        <label for="matrixProduct_languageTo">Sprache:</label>
        <Select
            v-model="model.languageTo"
            :options="languages"
            option-label="bezeichnung_de"
            option-value="iso"
        />
        <label for="matrixProduct_nameTo">Übersetzung Name:</label>
        <InputText v-model="model.nameTo" required />
        <label for="matrixProduct_nameTo">Übersetzung Name Extern:</label>
        <InputText v-model="model.nameExternalTo" :required="model.nameExternalFrom" />
      </div>
    </Fluid>
    <template #footer>
      <Button label="ABBRECHEN" @click="emit('close')" />
      <Button label="SPEICHERN" @click="save" :disabled="!ready()"/>
    </template>
  </Dialog>
</template>
