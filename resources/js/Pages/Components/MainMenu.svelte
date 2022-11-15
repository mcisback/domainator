<script>
    import route from 'ziggy-js'

    import { slide } from 'svelte/transition'
    import DropdownUserMenu from "./DropdownUserMenu.svelte";

    export let currentUser
    // export let props
    export let title

    let showUserMenu = false
    let logoutForm
</script>

<div id="mainMenu" class="px-5">
    <div class="row align-items-center p-2">

        <div class="col-3">
            <div class="row">
                <div class="col-1">
                    <i class="fa-solid fa-house"></i>
                </div>
                <div class="col-10">
                    <a href={route('dashboard.index')}>{title}</a>
                </div>
            </div>
        </div>
        <div class="col-6 d-flex justify-content-center align-items-center">
            <div>
                <img src="/img/logo-white1.png" alt="Logo" class="w-100">
            </div>
        </div>

        <DropdownUserMenu currentUser={currentUser} />

    </div>
</div>

<style>
    #mainMenu {
        background: rgb(78,78,78);
        background: linear-gradient(270deg, rgba(78,78,78,1) 0%, rgba(0,0,0,1) 100%);
        color: white;
    }

    a {
        color: white;
    }
</style>
