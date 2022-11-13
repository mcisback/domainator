<script>
    import QuillEditor from "./QuillEditor.svelte";

    export let id = 'editor'
    export let key = Math.random()
    export let data = ''
</script>

{#if key}
    <QuillEditor bind:id={id} bind:data={data} />
{/if}


