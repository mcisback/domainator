<script>
    export let items = [];
    export let activeTabValue = 1;

    const handleClick = tabValue => () => (activeTabValue = tabValue);
</script>

<ul class="nav nav-tabs">
    {#each items as item}
        <li class="nav-item" style="cursor: pointer;">
            <span
                on:click={handleClick(item.value)}
                class={activeTabValue === item.value ? 'nav-link active' : 'nav-link'}
                aria-current="page" href="#">
                {item.label}
            </span>
        </li>
    {/each}
</ul>

{#each items as item}
    {#if activeTabValue === item.value}
        <slot activeTabValue={item.value}></slot>
    {/if}
{/each}
