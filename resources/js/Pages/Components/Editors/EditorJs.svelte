<script>
    import {onMount} from "svelte";

    import EditorJS from '@editorjs/editorjs';
    import Header from '@editorjs/header';
    // import List from '@editorjs/list';

    import {clickOutside} from "../../Actions/clickOutside";

    export let id = 'editorjs'
    export let editor = null
    export let data = null

    const saved = () => {
        editor.save().then(savedData => {
            data = savedData
        })
        .catch(err => {
            console.error(`EditorJS#${id} Error: `, err)
        })
    }

    onMount(() => {
        editor = new EditorJS({
            holder: id,
            tools: {
                header: Header,
                // list: List
            },
            onChange: () => {
                saved()
            },
            onPaste: () => {
                saved()
            }
        })
    })
</script>

<div id={id} use:clickOutside on:clickOutside={() => {
    saved()
}}></div>
