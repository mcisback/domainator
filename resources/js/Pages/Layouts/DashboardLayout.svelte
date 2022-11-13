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
            label: 'Generator',
            route: 'dashboard.index',
            faIcon: 'fa-solid fa-gears',
            bgColor: '#d5ecfc',
            permissions: ['generator.manage'],
        },
        {
            label: 'Translations',
            route: 'dashboard.translations.index',
            faIcon: 'fa-solid fa-language',
            bgColor: '#d5ecfc',
            permissions: ['translations.manage'],
        },
        {
            label: 'FTP Servers',
            route: 'dashboard.ftpserver.index',
            faIcon: 'fa-solid fa-server',
            bgColor: '#d5ecfc',
            permissions: ['ftpserver.manage'],
        },
        {
            label: 'Users',
            route: 'dashboard.users.index',
            faIcon: 'fa-solid fa-users',
            bgColor: '#d5ecfc',
            permissions: ['users.manage'],
        },
        // {
        //     label: 'Fields',
        //     route: 'dashboard.editor.index',
        //     faIcon: 'fa-solid fa-pen-to-square',
        //     bgColor: '#d5ecfc',
        //     permissions: ['fields.manage'],
        // }
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


