// SPDX-FileCopyrightText: 2023 Andreas Palm
//
// SPDX-License-Identifier: LicenseRef-EGPL-3.1

import '@res/css/vue.css';
import {createApp} from "vue";
import PrimeVue from "primevue/config";
import Aura from '@primevue/themes/aura';
import {definePreset} from "@primevue/themes";

const OpenXePreset = definePreset(Aura, {

});

export function createVueApp(rootComponent, rootProps) {
    return createApp(rootComponent, rootProps)
        .use(PrimeVue, {
            theme: {
                preset: OpenXePreset,
                options: {
                    darkModeSelector: '.openXeDarkMode'
                }
            }
        });
}