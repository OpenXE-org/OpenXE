<!--
SPDX-FileCopyrightText: 2025 Andreas Palm

SPDX-License-Identifier: AGPL-3.0-only
-->

<script setup>
import {ref, onMounted} from "vue";
import axios from "axios";
import {Button, Checkbox, Dialog, Fluid, InputText} from "primevue";
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
    const url = 'index.php?module=lieferschwelle&action=edit';
    model.value = await axios.get(url, {
      params: props
    }).then(response => response.data)
  }
})

async function save() {
  if (!parseInt(props.id) > 0)
    model.value.id = 0;
  const url = model.value.id > 0
    ? 'index.php?module=lieferschwelle&action=edit'
    : 'index.php?module=lieferschwelle&action=add';
  await axios.post(url, {...props, ...model.value})
      .then(() => {emit('save')}, AlertErrorHandler);
}
</script>

<template>
  <Dialog visible modal header="Lieferschwelle anlegen/bearbeiten" style="width: 500px" @update:visible="emit('close')">
    <Fluid>
      <div class="grid gap-1" style="grid-template-columns: 25% 75%">
        <label for="lieferschwelle_originCountry">Ursprungsland:</label>
        <AutoComplete input-id="lieferschwelle_originCountry"
                      v-model="model.originCountry"
                      :option-label="(item) => [item.isoAlpha2, item.nameGerman].join(' ')"
                      ajax-filter="laender"
                      force-selection
        />
        <label for="lieferschwelle_destinationCountry">Empfängerland:</label>
        <AutoComplete input-id="lieferschwelle_destinationCountry"
                      v-model="model.destinationCountry"
                      :option-label="(item) => [item.isoAlpha2, item.nameGerman].join(' ')"
                      ajax-filter="laender"
                      force-selection
        />
        <label for="lieferschwelle_ustid">USt-ID:</label>
        <InputText v-model="model.ustId" input-id="lieferschwelle_ustid" show-buttons />
        <label for="lieferschwelle_active">Aktiv:</label>
        <Checkbox v-model="model.active" binary />
      </div>
    </Fluid>
    <template #footer>
      <Button label="ABBRECHEN" @click="emit('close')" />
      <Button label="SPEICHERN" @click="save" :disabled="!model.destinationCountry"/>
    </template>
  </Dialog>
</template>
