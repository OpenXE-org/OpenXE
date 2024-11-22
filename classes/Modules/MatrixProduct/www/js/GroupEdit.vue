<!--
SPDX-FileCopyrightText: 2023-2024 Andreas Palm

SPDX-License-Identifier: LicenseRef-EGPL-3.1
-->

<script setup>
import {ref, onMounted} from "vue";
import axios from "axios";
import Dialog from "primevue/dialog";
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import InputNumber from "primevue/inputnumber";
import Checkbox from "primevue/checkbox";
import Fluid from "primevue/fluid";
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
  <Dialog visible modal header="Gruppe anlegen/bearbeiten" style="width: 500px" @update:visible="emit('close')">
    <Fluid>
      <div class="grid gap-1" style="grid-template-columns: 25% 75%">
        <label for="matrixProduct_group_name">Name:</label>
        <InputText id="matrixProduct_group_name" v-model="model.name" autofocus required />
        <label for="matrixProduct_group_nameExternal">Name Extern:</label>
        <InputText id="matrixProduct_group_nameExternal" v-model="model.nameExternal" />
        <label for="matrixProduct_group_project">Projekt:</label>
        <AutoComplete input-id="matrixProduct_group_project"
            v-model="model.project"
            :optionLabel="item => [item.abkuerzung, item.name].join(' ')"
            ajaxFilter="projektname"
            forceSelection
        />
        <label v-if="articleId" for="matrixProduct_group_sort">Sortierung:</label>
        <InputNumber v-if="articleId" v-model="model.sort" input-id="matrixProduct_group_sort" show-buttons />
        <label for="matrixProduct_group_required">Pflicht:</label>
        <Checkbox v-model="model.required" binary />
        <label for="matrixProduct_group_active">Aktiv:</label>
        <Checkbox v-model="model.active" binary />
      </div>
    </Fluid>
    <template #footer>
      <Button label="ABBRECHEN" @click="emit('close')" />
      <Button label="SPEICHERN" @click="save" :disabled="!model.name"/>
    </template>
  </Dialog>
</template>
