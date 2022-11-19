<script>
    // import { inertia } from '@inertiajs/inertia-svelte'

    import { page } from '@inertiajs/inertia-svelte'

    import MainMenu from '../Components/MainMenu.svelte'
    import LeftMenu from "../Components/LeftMenu.svelte";
    import {LaraUser} from "../../user";

    let {
        user = null,
    } = $page.props

    const sections = [
        {
            label: 'Add Domain',
            route: 'dashboard.index',
            faIcon: 'fa-solid fa-globe',
            bgColor: '#d5ecfc',
            permissions: ['domains.request'],
        },
        {
            label: 'Domains',
            route: 'dashboard.domains.index',
            faIcon: 'fa-solid fa-tab',
            bgColor: '#d5ecfc',
            permissions: ['domains.manage'],
        },
        {
            label: 'SEDO',
            route: 'dashboard.sedoAccounts.index',
            faIcon: 'fa-solid fa-users-gear',
            bgColor: '#d5ecfc',
            permissions: ['sedo.manage'],
        },
        {
            label: 'Namecheap',
            route: 'dashboard.namecheap.index',
            faIcon: 'fa-solid fa-gears',
            bgColor: '#d5ecfc',
            permissions: ['namecheap.manage'],
        },
        {
            label: 'Users',
            route: 'dashboard.users.index',
            faIcon: 'fa-solid fa-users',
            bgColor: '#d5ecfc',
            permissions: ['users.manage'],
        },
    ]

    const currentUser = LaraUser(user)

    console.log('current user: ', currentUser)
</script>

<div>
    <MainMenu title="Dashboard" props={$page.props} currentUser={currentUser} />

    <div id="main" class="d-flex">

        <LeftMenu props={$page.props} sections={sections} currentUser={currentUser} />

        <main class="d-flex flex-column align-items-center w-100">
            <slot props={$page.props} sections={sections} currentUser={currentUser}></slot>
        </main>
    </div>
</div>

<style>
    main, #main {
        max-width: 1920px;
        min-height: 100vh;
    }
</style>


