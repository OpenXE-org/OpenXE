<!--
SPDX-FileCopyrightText: 2023 Andreas Palm

SPDX-License-Identifier: LicenseRef-EGPL-3.1
-->

<script setup>
import {ref, onMounted} from "vue";
import axios from "axios";
import Button from "primevue/button";
import Dialog from "primevue/dialog";
import {AlertErrorHandler} from "@res/js/ajaxErrorHandler";

const props = defineProps({
  optionId: String,
  groupId: String,
  articleId: String
});
const emit = defineEmits(['save', 'close']);

const model = ref({});

onMounted(async () => {
  if (props.optionId > 0) {
    const url = props.articleId > 0
        ? 'index.php?module=matrixprodukt&action=artikel&cmd=optionedit'
        : 'index.php?module=matrixprodukt&action=optionenlist&cmd=edit';
    model.value = await axios.get(url, {
      params: props
    }).then(response => response.data)
  }
})

async function save() {
  const url = props.articleId > 0
      ? 'index.php?module=matrixprodukt&action=artikel&cmd=optionsave'
      : 'index.php?module=matrixprodukt&action=optionenlist&cmd=save';
  await axios.post(url, {...props, ...model.value})
      .then(() => {emit('save')})
      .catch(AlertErrorHandler);
}
</script>

<template>
  <Dialog visible modal header="Option anlegen/bearbeiten" style="width: 500px" @update:visible="emit('close')">
    <div class="grid gap-1" style="grid-template-columns: 25% 75%">
      <label for="matrixProduct_option_name">Name:</label>
      <input id="matrixProduct_option_name" type="text" v-model="model.name" required />
      <label for="matrixProduct_option_nameExternal">Name Extern:</label>
      <input id="matrixProduct_option_nameExternal" type="text" v-model="model.nameExternal" />
      <label for="matrixProduct_option_articleNumberSuffix">Artikelnummer-Suffix:</label>
      <input id="matrixProduct_option_articleNumberSuffix" type="text" v-model="model.articleNumberSuffix" />
      <label for="matrixProduct_option_sort">Sortierung:</label>
      <input id="matrixProduct_option_sort" type="text" v-model="model.sort" />
      <label for="matrixProduct_option_active">Aktiv:</label>
      <input id="matrixProduct_option_active" type="checkbox" v-model="model.active" class="justify-self-start" />
    </div>
    <template #footer>
      <Button label="ABBRECHEN" @click="emit('close')" />
      <Button label="SPEICHERN" @click="save" :disabled="!model.name" />
    </template>
  </Dialog>
</template>
