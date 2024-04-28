<!--
SPDX-FileCopyrightText: 2023 Andreas Palm

SPDX-License-Identifier: LicenseRef-EGPL-3.1
-->

<script setup>
import axios from "axios";
import {ref} from 'vue';
import {reloadDataTables} from "@res/js/jqueryBridge";
import AddGlobalToArticle from "./AddGlobalToArticle.vue";
import GroupEdit from "./GroupEdit.vue";
import OptionEdit from "./OptionEdit.vue";
import Variant from "./Variant.vue";
import Translation from "./Translation.vue";
import CreateMissing from "./CreateMissing.vue";

const model = ref(null);

document.getElementById('main').addEventListener('click', async (ev) => {
  const target = ev.target;
  if (!target || !target.classList.contains('vueAction'))
    return;
  const ds = target.dataset;
  if (ds.action.endsWith('Delete')) {
    const cnf = confirm('Wirklich löschen?');
    if (!cnf)
      return;
    let url;
    switch (ds.action) {
      case 'groupDelete':
        url = ds.articleId > 0
            ? 'index.php?module=matrixprodukt&action=artikel&cmd=groupdelete'
            : 'index.php?module=matrixprodukt&action=list&cmd=delete';
        await axios.post(url, {groupId: ds.groupId});
        break;
      case 'optionDelete':
        url = ds.articleId > 0
            ? 'index.php?module=matrixprodukt&action=artikel&cmd=optiondelete'
            : 'index.php?module=matrixprodukt&action=optionenlist&cmd=delete';
        await axios.post(url, {optionId: ds.optionId});
        break;
      case 'variantDelete':
        url = 'index.php?module=matrixprodukt&action=artikel&cmd=variantdelete';
        await axios.post(url, {variantId: ds.variantId});
        break;
      case 'translationDelete':
        url = 'index.php?module=matrixprodukt&action=translation&cmd=delete';
        await axios.post(url, {id: ds.id, type: ds.type});
        break;
    }
    onSave();
    return;
  }

  model.value = ds;
});

function onSave() {
  reloadDataTables();
  onClose();
}

function onGroupSave() {
  location.reload();
}

function onClose() {
  model.value = null;
}
</script>

<template>
  <template v-if="model">
    <AddGlobalToArticle v-if="model.action === 'addGlobalToArticle'" v-bind="model" @close="onClose" @save="onGroupSave" />
    <GroupEdit v-else-if="model.action === 'groupEdit'" v-bind="model" @close="onClose" @save="onGroupSave" />
    <OptionEdit v-else-if="model.action === 'optionEdit'" v-bind="model" @close="onClose" @save="onSave" />
    <Variant v-else-if="model.action === 'variantEdit'" v-bind="model" @close="onClose" @save="onSave" />
    <CreateMissing v-else-if="model.action === 'createMissing'" v-bind="model" @close="onClose" @save="onSave" />
    <Translation v-else-if="model.action === 'translationEdit'" v-bind="model" @close="onClose" @save="onSave" />
  </template>
</template>
