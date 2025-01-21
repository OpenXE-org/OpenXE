<!--
SPDX-FileCopyrightText: 2023-2024 Andreas Palm

SPDX-License-Identifier: LicenseRef-EGPL-3.1
-->

<script setup>
import {ref, onMounted} from "vue";
import axios from "axios";
import Dialog from "primevue/dialog";
import Listbox from "primevue/listbox";
import Fluid from "primevue/fluid";
import Button from "primevue/button";
import {AlertErrorHandler} from '@res/js/ajaxErrorHandler';

const props = defineProps({
  articleId: String
})
const emit = defineEmits(['save', 'close']);

const model = ref(null);
const group = ref(null);
const selected = ref([]);
onMounted(async () => {
  model.value = await fetch('index.php?module=matrixprodukt&action=list&cmd=selectoptions')
      .then(x => x.json())
})
async function save() {
  await axios.post('index.php?module=matrixprodukt&action=artikel&cmd=addoptions', {
    articleId: props.articleId,
    optionIds: selected.value
  })
      .then(() => {emit('save')})
      .catch(AlertErrorHandler);
}
</script>

<template>
  <Dialog visible modal header="Globale Optionen hinzufügen" style="width: 500px" @update:visible="emit('close')">
    <Fluid>
      <div v-if="model" class="grid gap-1" style="grid-template-columns: 25% 75%">
        <label for="matrixProductOptions" style="padding-top: 5px;">Optionen:</label>
        <Listbox multiple
                 :options="model"
                 option-group-label="name"
                 option-group-children="options"
                 option-label="name"
                 option-value="id"
                 list-style="height: 200px"
                 v-model="selected" />
      </div>
    </Fluid>
    <template #footer>
      <Button label="ABBRECHEN" @click="emit('close')" />
      <Button label="HINZUFÜGEN" @click="save" :disabled="selected.length === 0"/>
    </template>
  </Dialog>
</template>