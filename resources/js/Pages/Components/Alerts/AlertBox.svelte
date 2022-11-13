<script>
    import { slide } from 'svelte/transition';

    export let success = false
    export let dismissable = false

    export let dismiss = false
</script>

{#if !dismiss}
    <div class={'alert alert-' + (success ? 'success' : 'danger') + (dismissable ? ' alert-dismissible' : '')} role="alert">
        <slot></slot>
        {#if dismissable}
            <button type="button" class="btn-close" aria-label="Close" on:click|preventDefault={() => dismiss = !dismiss}></button>
        {/if}
    </div>
{/if}
