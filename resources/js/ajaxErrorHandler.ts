// SPDX-FileCopyrightText: 2023-2025 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

import {AxiosError} from 'axios';

export function AlertErrorHandler(error: AxiosError) {
    if (error.response === undefined || error.response.status >= 500) {
        console.log('Unknown error on axios request', error);
        alert('Unerwarteter Fehler, weitere Hinweise ggf. in der JavaScript-Konsole');
    } else {
        console.log('ClientError on axios request', error);
        alert(error.response.data.error);
    }
}