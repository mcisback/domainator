<script>

    import route from "ziggy-js";

    export let currentUser
    // export let props
    export let sections

</script>

<aside id="leftMenu" class="p-4">
    {#each sections as {label, route: laravelRoute, faIcon, bgColor, permissions=null}, i}
        {#if permissions === null || currentUser.hasPermissionTo(permissions)}
            <div class="row py-2 flex-nowrap">
                <div class="col-2">
                    <i class={faIcon}></i>
                </div>

                <div class="col text-capitalize">
                    <a href={route(laravelRoute)}>{label}</a>
                </div>
            </div>
        {/if}
    {/each}
</aside>

<style>
    #leftMenu {
        background: rgb(192, 58, 118);
        background: linear-gradient(0deg, rgb(192, 58, 118) 0%, rgb(148, 0, 0) 100%);
        color: white;
        width: 25%;
        font-size: 1.5rem;
        cursor: pointer;
    }

    a {
        color: white;
    }
</style>
