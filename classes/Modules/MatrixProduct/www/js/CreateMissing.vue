<script setup>
import Button from "primevue/button";
import Dialog from "primevue/dialog";
import MultiSelect from "primevue/multiselect";
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
  }).then(response => { return {...props, ...response.data}})
})

async function save() {
  await axios.post('index.php?module=matrixprodukt&action=artikel&cmd=createMissing', {...props, ...model.value})
      .catch(AlertErrorHandler)
      .then(() => {emit('save')});
}
</script>

<template>
  <Dialog visible modal header="Variante" style="width: 500px" @update:visible="emit('close')">
    <div class="grid gap-1" style="grid-template-columns: 25% 75%;">
      <template v-for="group in model.groups">
        <label>{{ group.name }}</label>
        <MultiSelect v-model="group.selected" :options="group.options" optionLabel="name" optionValue="value" />
      </template>
    </div>
    <template #footer>
      <Button label="ABBRECHEN" @click="emit('close')" />
      <Button label="ERSTELLEN" @click="save" />
    </template>
  </Dialog>
</template>

<style scoped>

</style>