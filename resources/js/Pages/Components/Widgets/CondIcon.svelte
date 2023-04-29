<script>
    export let condition = null
    export let ifNullIcon = "fa-sharp fa-solid fa-xmark"
    export let ifNotNullIcon = "fa-solid fa-check"
</script>

{#if condition}
    <span style="color: red;">
        <i class={ifNullIcon}></i>
    </span>
{:else}
    <span style="color: green;">
        <i class={ifNotNullIcon}></i>
        &nbsp;
        <slot></slot>
    </span>
{/if}
