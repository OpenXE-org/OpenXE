<!--
SPDX-FileCopyrightText: 2025 Andreas Palm

SPDX-License-Identifier: AGPL-3.0-only
-->

<script setup>
import {ref, onMounted} from "vue";
import axios from "axios";
import {Button, Checkbox, Dialog, Fluid, InputNumber, Select} from "primevue";
import {AlertErrorHandler} from "@res/js/ajaxErrorHandler";
import AutoComplete from "@res/vue/AutoComplete.vue";

const types = [
  {name: 'Ähnlich', value: 1},
  {name: 'Zubehör', value: 2}
]

const props = defineProps({
  id: String,
});
const emit = defineEmits(['save', 'close']);

const model = ref({active: true});

onMounted(async () => {
  if (props.id > 0) {
    const url = 'index.php?module=crossselling&action=edit';
    model.value = await axios.get(url, {
      params: props
    }).then(response => response.data)
  }
})

async function save() {
  if (!parseInt(props.id) > 0)
    model.value.id = 0;
  const url = model.value.id > 0
    ? 'index.php?module=crossselling&action=edit'
    : 'index.php?module=crossselling&action=add';
  await axios.post(url, {...props, ...model.value})
      .then(() => {emit('save')}, AlertErrorHandler);
}
</script>

<template>
  <Dialog visible modal header="Crossselling-Artikel anlegen/bearbeiten" style="width: 500px" @update:visible="emit('close')">
    <Fluid>
      <div class="grid gap-1" style="grid-template-columns: 25% 75%">
        <label for="crossselling_type">Art:</label>
        <Select v-model="model.type" :options="types" option-label="name" option-value="value" />
        <label for="crossselling_mainArticle">Artikel:</label>
        <AutoComplete input-id="crossselling_mainArticle"
                      v-model="model.mainArticle"
                      :option-label="(item) => [item.nummer, item.name].join(' ')"
                      ajax-filter="artikelnummer"
                      force-selection
        />
        <label for="crossselling_connectedArticle">Crossselling Artikel:</label>
        <AutoComplete input-id="crossselling_connectedArticle"
                      v-model="model.connectedArticle"
                      :option-label="(item) => [item.nummer, item.name].join(' ')"
                      ajax-filter="artikelnummer"
                      force-selection
        />
        <label for="crossselling_shop">Shop:</label>
        <AutoComplete input-id="crossselling_shop"
                      v-model="model.shop"
                      option-label="bezeichnung"
                      ajax-filter="shopnameid"
        />
        <label for="crossselling_sort">Sortierung:</label>
        <InputNumber v-model="model.sort" input-id="crossselling_sort" show-buttons />
        <label for="crossselling_bidirectional">Bidirektionale Zuweisung:</label>
        <Checkbox v-model="model.bidirectional" binary />
        <label for="crossselling_group_active">Aktiv:</label>
        <Checkbox v-model="model.active" binary />
      </div>
    </Fluid>
    <template #footer>
      <Button label="ABBRECHEN" @click="emit('close')" />
      <Button label="SPEICHERN" @click="save" :disabled="!model.type || !model.mainArticle || !model.connectedArticle"/>
    </template>
  </Dialog>
</template>
