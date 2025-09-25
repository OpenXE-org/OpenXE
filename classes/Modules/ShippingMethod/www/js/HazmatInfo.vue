<script setup>
import {InputNumber,InputText} from "primevue";
import { useI18n } from 'vue-i18n';

const {t, d, n} = useI18n();

const model = defineModel();
</script>

<template>
  <div class="grid grid-cols-classic-form gap-1">
    <label>{{ t('hazmat.unNumber') }}:</label>
    <InputText v-model="model.unNumber" maxlength="4" />
    <label>{{ t('hazmat.class') }}:</label>
    <InputText v-model="model.class" maxlength="6" />
    <label>{{ t('hazmat.classificationCode') }}:</label>
    <InputText v-model="model.classificationCode" />
    <label>{{ t('hazmat.packingCode') }}:</label>
    <InputText v-model="model.packingCode" />
    <label>{{ t('hazmat.description') }}:</label>
    <InputText v-model="model.description" />
    <label>{{ t('hazmat.weight') }}:</label>
    <InputNumber v-model="model.weight" :max-fraction-digits="2" />
    <label>{{ t('hazmat.factor') }}:</label>
    <InputNumber v-model="model.factor" :min="0" :max="999" />
  </div>
</template>

<i18n src="./locales.yaml" lang="yaml"></i18n>