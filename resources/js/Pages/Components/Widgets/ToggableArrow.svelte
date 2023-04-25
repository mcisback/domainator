<script>

    import { createEventDispatcher } from 'svelte';

    export let toggled = false

    const dispatch = createEventDispatcher();

    function dispatchToggled() {
        toggled = !toggled

        console.log(`toggableArrowClicked: ${toggled}`)

        dispatch('toggableArrowClicked', {
            toggled
        });
    }

</script>

<div class="togglable-arrow" on:click={() => dispatchToggled()}>
    {#if !toggled}
        <i class="fa-solid fa-caret-down"></i>
    {:else}
        <i class="fa-solid fa-caret-up"></i>
    {/if}
</div>

<style>
    .togglable-arrow {
        cursor: pointer;
    }
</style>
