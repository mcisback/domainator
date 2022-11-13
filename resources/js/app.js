require('./bootstrap');
require('./moment');
// require('./summernote');

import {LaraUser} from "./user";

window.LaraUser = LaraUser

import route from 'ziggy-js';

const response = await fetch('/api/ziggy');
const Ziggy = await response.json();

route('home', undefined, undefined, Ziggy);

import { createInertiaApp } from '@inertiajs/inertia-svelte'

createInertiaApp({
    resolve: name => import(`./Pages/${name}.svelte`),
    setup({ el, App, props }) {
        new App({ target: el, props })
    },
})

