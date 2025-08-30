// SPDX-FileCopyrightText: 2023 Andreas Palm
//
// SPDX-License-Identifier: LicenseRef-EGPL-3.1

import '@res/css/vue.css';
import 'primeicons/primeicons.css';
import {createApp} from "vue";
import PrimeVue from "primevue/config";
import Aura from '@primevue/themes/aura';
import {definePreset} from "@primevue/themes";
import { createI18n } from 'vue-i18n';
import pl_de from "primelocale/de.json";

import messages from '@intlify/unplugin-vue-i18n/messages';

const OpenXePreset = definePreset(Aura, {
    semantic: {
        formField: {
            borderRadius: '3px'
        },
        colorScheme: {
            light: {
                formField: {
                    color: '#6d6d6f'
                },
                content: {
                    background: 'transparent'
                }
            }
        }
    }
});

const numberFormats = {
    'de': {
        currency: {style: 'currency', currency: 'EUR', notation: 'standard'},
        weight: {style: 'unit', unit: 'kilogram', minimumFractionDigits: 3, maximumFractionDigits: 3}
    }
}

const i18n = createI18n({
    locale: 'de',
    fallbackLocale: 'de',
    missingWarn: false,
    fallbackWarn: false,
    messages,
    numberFormats
})


export function createVueApp(rootComponent, rootProps) {
    return createApp(rootComponent, rootProps)
        .use(PrimeVue, {
            theme: {
                preset: OpenXePreset,
                options: {
                    darkModeSelector: '.openXeDarkMode'
                }
            },
            locale: pl_de.de
        })
        .use(i18n);
}