import { createInertiaApp } from '@inertiajs/svelte'

createInertiaApp({

    resolve: name => {

        const pages = import.meta.glob('./Pages/**/*.svelte', { eager: true })

        return pages[`./Pages/${name}.svelte`]

    },
    progress: {
        color: '#E02424'
    },
    setup({ el, App, props }) {

        new App({ target: el, props })

    },

})
