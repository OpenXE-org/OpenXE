<!--
SPDX-FileCopyrightText: 2023 Andreas Palm

SPDX-License-Identifier: LicenseRef-EGPL-3.1
-->

<script setup>
import {ref, onMounted} from "vue";
import axios from "axios";
import Dialog from "primevue/dialog";
import Button from "primevue/button";
import {AlertErrorHandler} from "@res/js/ajaxErrorHandler";
import AutoComplete from "@res/vue/AutoComplete.vue";

const props = defineProps({
  groupId: String,
  articleId: String
});
const emit = defineEmits(['save', 'close']);

const model = ref({});

onMounted(async () => {
  if (props.groupId > 0) {
    const url = props.articleId > 0
        ? 'index.php?module=matrixprodukt&action=artikel&cmd=groupedit'
        : 'index.php?module=matrixprodukt&action=list&cmd=edit';
    model.value = await axios.get(url, {
      params: props
    }).then(response => response.data)
  }
})

async function save() {
  if (!parseInt(props.groupId) > 0)
    model.value.groupId = 0;
  const url = props.articleId > 0
      ? 'index.php?module=matrixprodukt&action=artikel&cmd=groupsave'
      : 'index.php?module=matrixprodukt&action=list&cmd=save';
  await axios.post(url, {...props, ...model.value})
      .catch(AlertErrorHandler)
      .then(() => {emit('save')});
}
</script>

<template>
  <Dialog visible modal header="Gruppe anlegen/bearbeiten" style="width: 500px" @update:visible="emit('close')" class="p-fluid">
    <div class="grid gap-1" style="grid-template-columns: 25% 75%">
      <label for="matrixProduct_group_name">Name:</label>
      <input type="text" v-model="model.name" required />
      <label for="matrixProduct_group_nameExternal">Name Extern:</label>
      <input type="text" v-model="model.nameExternal" />
      <label for="matrixProduct_group_project">Projekt:</label>
      <AutoComplete
          v-model="model.project"
          :optionLabel="item => [item.abkuerzung, item.name].join(' ')"
          ajaxFilter="projektname"
          forceSelection
      />
      <label v-if="articleId" for="matrixProduct_group_sort">Sortierung:</label>
      <input v-if="articleId" type="text" v-model="model.sort">
      <label for="matrixProduct_group_required">Pflicht:</label>
      <input type="checkbox" v-model="model.required" class="justify-self-start">
      <label for="matrixProduct_group_active">Aktiv:</label>
      <input type="checkbox" v-model="model.active" class="justify-self-start">
    </div>
    <template #footer>
      <Button label="ABBRECHEN" @click="emit('close')" />
      <Button label="SPEICHERN" @click="save" :disabled="!model.name"/>
    </template>
  </Dialog>
</template>
