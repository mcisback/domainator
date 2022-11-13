<script>
    export let id = 'template'
    export let templates = []
    export let onSelectTemplate = null
    export let currentTemplate
</script>

<label for={id}>Choose Template (current: {currentTemplate}): </label>

<div class="form-control p-4">
    <input type="hidden" name="{id}" id="{id}" bind:value={currentTemplate}>

    {#each templates as template (template)}
        <div style="cursor: pointer;" on:click={() => onSelectTemplate(template)}>
            {template}
        </div>
    {/each}
</div>
