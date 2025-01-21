// SPDX-FileCopyrightText: 2023 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

import {globSync} from 'glob';
import * as path from 'path';
import vue from '@vitejs/plugin-vue';

const globpattern = [
    'classes/Modules/*/www/js/?(*.)entry.{js,ts}',
    'www/themes/*/js/?(*.)entry.{js,ts}'
];

const inputs = globSync(globpattern)
    .map(file =>  {
        const regex = /(?<prefix>themes|Modules)\/(?<name>\w+)\/(\w+\/)*((?<entry>\w+)\.)?entry\.(js|ts)$/;
        const match = file.match(regex);
        console.log(match);
        let entryname = file;
        if (match) {
            entryname = [match.groups.prefix.toLowerCase(), match.groups.name].join('/');
            if (match.groups.entry && match.groups.entry.toLowerCase() !== match.groups.name.toLowerCase())
                entryname += '-'+match.groups.entry;
        }
        return [entryname, file];
    })

/** @type {import('vite').UserConfig} */
export default {
    build: {
        rollupOptions: {
            input: {
                ...Object.fromEntries(inputs)
            },
            output: {
                assetFileNames: function(assetInfo) {
                    console.log(assetInfo);
                    return 'assets/[name]-[hash][extname]';
                },
                entryFileNames: function(chunkInfo) {
                    console.log(chunkInfo);
                    return '[name]-[hash].js';
                },
                chunkFileNames: function (chunkInfo) {
                    console.log(chunkInfo);
                    return '[name]-[hash].js';
                }
            }
        },
        manifest: true,
        outDir: 'www/dist',
    },
    plugins: [vue()],
    mode: 'development',
    resolve: {
        alias: {
            '@res': path.resolve(__dirname, 'resources')
        }
    }
}