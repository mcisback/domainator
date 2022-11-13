<script>
    import {onMount} from 'svelte';

    import 'quill/dist/quill.snow.css'
    import Quill from 'quill/dist/quill.min'


    export let id = 'editor'
    // export let key = 'editor'
    export let data = ''
    export let editor = null

    id += "_" + Math.floor(Math.random()*1000)

    onMount(() => {
        console.log('Mounting quill on: ' + `#${id}`)

        editor = new Quill(`#${id}`, {
            theme: 'snow'
        })

        editor.on('text-change', (delta, oldDelta, source) => {
            if(!!editor) {
                data = editor.root.innerHTML
            }

            console.log('Quill text-change: ', {id, data, editor})
        })
    })
</script>

<div id={id} class="quill-editor"></div>

<style>
    .quill-editor {
        min-height: 20rem;
    }
</style>
