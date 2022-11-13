<script>
    // import { inertia } from '@inertiajs/inertia-svelte'

    import { page } from '@inertiajs/inertia-svelte'

    const {
        user = null,
        errors = null,
        csrf_token = null
    } = $page.props

    // console.log('PAGE PROPS: ', {$page, props: $page.props, user, errors, csrf_token})
</script>

<main>
    <slot props={$page.props}></slot>
</main>
