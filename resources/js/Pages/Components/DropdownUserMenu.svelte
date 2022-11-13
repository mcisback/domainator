<script>
    import { slide } from 'svelte/transition'

    import route from 'ziggy-js';

    export let currentUser

    let showUserMenu = false
    let logoutForm

    const isInsideAdminArea = route().current().includes('admin.')
</script>

<div class="col-3" style="cursor: pointer; z-index: 100;" on:click={() => showUserMenu = !showUserMenu}>
    <div class="row">
        <div class="col-1">
            <i class="fa-solid fa-user"></i>
        </div>
        <div class="col-8">
            Hello, {currentUser.firstName} {currentUser.lastName}
        </div>
        <div class="col-1">
            <i class={showUserMenu ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down'}></i>
        </div>
    </div>

    {#if showUserMenu}
        <div class="row position-relative" transition:slide>
            <div class="position-absolute p-4" style="background-color: #141414; top: 0; left: 0;">
                {#if currentUser.isAdmin() || currentUser.isStaffMember()}
                    <div class="row py-2" style="border-bottom: thin solid #ffffff50;">
                        <div class="col-1">
                            <i class={isInsideAdminArea ? 'fa-solid fa-users' : 'fa-solid fa-user-secret'}></i>
                        </div>
                        <div class="col">
                            Go To: <a href={isInsideAdminArea ? route('dashboard.index') : route('dashboard.index')}>
                                {isInsideAdminArea ? 'Members Dashboard' : 'Admin Dashboard'}
                            </a>
                        </div>
                    </div>
                {/if}

                <div class="row py-2">
                    <div class="col-1">
                        <i class="fa-solid fa-gear"></i>
                    </div>
                    <div class="col">
                        <a href={route('dashboard.me.index')}>Profile</a>
                    </div>
                </div>

                <div class="row py-2" style="border-bottom: thin solid #ffffff50;">
                    <div class="col-1">
                        <i class="fa-sharp fa-solid fa-address-card"></i>
                    </div>
                    <div class="col">
                        <a href="#settings">Settings</a>
                    </div>
                </div>

                <div class="row py-2">
                    <div class="col-1">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </div>
                    <div class="col">
                        <a href={route('logout')}>
                            Logout
                        </a>

                        <form id="logout-form" action={route('logout')} method="POST" style="display: none;">
                            <input bind:this={logoutForm} type="submit" value="Logout">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    {/if}
</div>

<style>
    a {
        color: white;
    }
</style>
