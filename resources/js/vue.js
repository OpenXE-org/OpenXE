// SPDX-FileCopyrightText: 2023 Andreas Palm
//
// SPDX-License-Identifier: LicenseRef-EGPL-3.1

import '@res/css/vue.css';
import '@res/css/primevue/_base.css';
import {createApp} from "vue";
import PrimeVue from "primevue/config";

export function createVueApp(rootComponent, rootProps) {
    return createApp(rootComponent, rootProps).use(PrimeVue);
}