<script>
    import {page, useForm} from '@inertiajs/inertia-svelte'
    import route from 'ziggy-js';

    import GuestLayout from '../Layouts/GuestLayout.svelte'
    import LogBox from "../Components/LogBox.svelte";

    // export let errors = {}

    let email = ''
    let password = ''
    let csrf_token = ''

    // let form = useForm({
    //     email: null,
    //     password: null,
    //     remember: false,
    // })
    //
    async function onSubmit() {
        // $form.post(route('login'), {
        //     // headers: {
        //     //     'Content-Type': 'application/json',
        //     //     'Accept': 'application/json'
        //     // },
        //     onSuccess: (...p) => console.log('onSuccess: ', $form, ...p),
        //     onError: (errors) => {
        //         $form.errors = errors
        //
        //         console.log('$form: ', $form)
        //     }
        // })
    }
</script>

<GuestLayout let:props>
    <div class="container">
        <LogBox onSubmit={onSubmit} action={route('verification.resend')} title="Login">

            <slot name="beforeForm">
                {#if props.resent}
                    <div class="alert alert-success" role="alert">
                       A fresh verification link has been sent to your email address.
                    </div>
                {/if}

                Before proceeding, please check your email for a verification link.
                If you did not receive the email,
            </slot>

            <button type="submit" class="btn btn-link p-0 m-0 align-baseline">click here to request another</button>.

        </LogBox>
    </div>
</GuestLayout>

<style>
    /*a {*/
    /*    color: black;*/
    /*    text-decoration: none;*/
    /*}*/
</style>
