// SPDX-FileCopyrightText: 2023 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

export function reloadDataTables() {
    window.$('#main .dataTable').DataTable().ajax.reload();
}