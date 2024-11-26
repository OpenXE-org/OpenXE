<!--
SPDX-FileCopyrightText: 2023-2024 Andreas Palm

SPDX-License-Identifier: LicenseRef-EGPL-3.1
-->

<script setup>
import Button from "primevue/button";
import Dialog from "primevue/dialog";
import MultiSelect from "primevue/multiselect";
import Fluid from "primevue/fluid";
import InputText from "primevue/inputtext";
import {onMounted, ref} from "vue";
import axios from "axios";
import {AlertErrorHandler} from "@res/js/ajaxErrorHandler";

const props = defineProps({
  articleId: String,
});
const emit = defineEmits(['save', 'close']);

const model = ref({});

onMounted(async () => {
  model.value = await axios.get('index.php?module=matrixprodukt&action=artikel&cmd=createMissing', {
    params: {...props}
  }).then(response => {
    return {...props, ...response.data}
  })
})

async function save() {
  await axios.post('index.php?module=matrixprodukt&action=artikel&cmd=createMissing', {...props, ...model.value})
      .catch(AlertErrorHandler)
      .then(() => {
        emit('save')
      });
}
</script>

<template>
  <Dialog visible modal header="Variante" style="width: 500px" @update:visible="emit('close')">
    <Fluid>
      <div class="grid gap-1" style="grid-template-columns: 25% 75%;" autofocus>
        <label>Trennzeichen:</label>
        <InputText v-model="model.separator" maxlength="2" />
        <template v-for="group in model.groups">
          <label>{{ group.name }}</label>
          <MultiSelect v-model="group.selected" :options="group.options" option-label="name" option-value="value"/>
        </template>
      </div>
    </Fluid>
    <template #footer>
      <Button label="ABBRECHEN" @click="emit('close')"/>
      <Button label="ERSTELLEN" @click="save"/>
    </template>
  </Dialog>
</template>

<style scoped>

</style>