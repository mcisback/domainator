<script>
    // import { inertia } from '@inertiajs/inertia-svelte'

    import { page } from '@inertiajs/inertia-svelte'

    import MainMenu from '../Components/MainMenu.svelte'
    import LeftMenu from "../Components/LeftMenu.svelte";
    // import Modal from "../Components/Modals/Modal.svelte";

    import {LaraUser} from '../../user';

    console.log('LaraUser: ', LaraUser)

    let {
        user = null,
    } = $page.props

    const sections = [
        {
            label: 'Utenti',
            route: 'admin.users',
            faIcon: 'fa-solid fa-user',
            permission: 'admin.users',
            bgColor: '#e2ffeb'
        },
        {
            label: 'Permessi&Ruoli',
            route: 'admin.roles-and-permissions',
            faIcon: 'fa-solid fa-user-pen',
            permission: 'admin.roles-and-permissions',
            bgColor: '#e2ffeb'
        },
        {
            label: 'Go Live',
            route: 'admin.live.go',
            faIcon: 'fa-solid fa-podcast',
            permission: 'admin.live',
            bgColor: '#e0e3f2'
        },
        {
            label: 'Lezioni Live',
            route: 'admin.live.index',
            faIcon: 'fa-solid fa-headset',
            permission: 'admin.live',
            bgColor: '#e0e3f2'
        },
        {
            label: 'Community',
            route: 'admin.community',
            faIcon: 'fa-solid fa-comments',
            permission: 'admin.community',
            bgColor: '#ffede1'
        },
        {
            label: 'Support',
            route: 'admin.support',
            faIcon: 'fa-solid fa-circle-question',
            permission: 'admin.support',
            bgColor: '#d5ecfc'
        }
    ]

    let modal = null
    let modalProps = {}

    const onShowModal = (event) => {

        modal = event.detail.modal
        modalProps = {
            record: event.detail.record,
            format: event.detail.format
        }

        console.log('onShowModal', event.detail, {modalProps})
    }

    document.addEventListener('showmodal', (e) => {
        onShowModal(e)
    })

    document.addEventListener('closemodal', (e) => {
        modal = false
    })

    const currentUser = LaraUser(user)

    console.log('current user: ', currentUser)
</script>

<div class="position-relative">
    <MainMenu title="Area Amministratore" props={$page.props} currentUser={currentUser} />

    <div id="main" class="d-flex">

        <LeftMenu props={$page.props} sections={sections} currentUser={currentUser} />

        <main class="d-flex flex-column align-items-center w-100">
            <slot props={$page.props} sections={sections} currentUser={currentUser}></slot>
        </main>
    </div>

    {#if modal}
        <svelte:component this={modal} props={modalProps} currentUser={currentUser}>
        </svelte:component>
    {/if}
</div>

<style>
    main, #main {
        max-width: 1920px;
        min-height: 100vh;
    }
</style>


