<!--
SPDX-FileCopyrightText: 2025 Andreas Palm

SPDX-License-Identifier: AGPL-3.0-only
-->

<script setup>
import axios from "axios";
import {ref} from 'vue';
import {reloadDataTables} from "@res/js/jqueryBridge";
import Edit from "./Edit.vue";

const model = ref(null);

document.getElementById('main').addEventListener('click', async (ev) => {
  const target = ev.target;
  if (!target || !target.classList.contains('vueAction'))
    return;
  const ds = target.dataset;
  if (ds.action === 'delete') {
    const cnf = confirm('Wirklich löschen?');
    if (!cnf)
      return;
    let url = 'index.php?module=crossselling&action=delete';
    await axios.post(url, {id: ds.id});
    onSave();
    return;
  }

  model.value = ds;
});

function onSave() {
  reloadDataTables();
  onClose();
}

function onClose() {
  model.value = null;
}
</script>

<template>
  <template v-if="model">
    <Edit v-bind="model" @close="onClose" @save="onSave" />
  </template>
</template>
