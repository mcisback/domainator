<script>
    import {page, useForm} from '@inertiajs/inertia-svelte'
    import route from 'ziggy-js';

    import GuestLayout from '../../Layouts/GuestLayout.svelte'
    import LogBox from "../../Components/LogBox.svelte";

    // export let errors = {}

    export let token = null
    export let email = null

    let form = useForm({
        email,
        password: null,
        password_confirmation: null,
        token
    })

    const onSubmit = async () => {
        $form.post(route('password.update'), {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            onSuccess: (...p) => {
                // showSpinner = false

                console.log('onSuccess: ', $form, ...p)
            },
            onError: (errors) => {
                $form.errors = errors

                console.log('$form: ', $form)
            }
        })
    }
</script>

<GuestLayout let:props>
    <div class="container">
        <div>

            <LogBox action={route('password.update')} onSubmit={onSubmit} title="Recupera Password">

                <input bind:value={$form.token} type="hidden" name="token" />

                <div class="row mb-3">
                    <label for="email" class="col-md-4 col-form-label text-md-end">Indirizzo Email</label>

                    <div class="col-md-6">
                        <input bind:value={$form.email} id="email" type="email" class={$form.errors?.email ? 'form-control is-invalid' : 'form-control'} name="email" required autocomplete="email">

                        {#if $form.errors?.email}
                            <span class="invalid-feedback" role="alert">
                                <strong>{ $form.errors?.email }</strong>
                            </span>
                        {/if}
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="password" class="col-md-4 col-form-label text-md-end">Password</label>

                    <div class="col-md-6">
                        <input bind:value={$form.password} id="password" type="password" class={$form.errors?.password ? 'form-control is-invalid' : 'form-control'} name="password" required autocomplete="new-password">

                        {#if $form.errors?.password}
                            <span class="invalid-feedback" role="alert">
                                <strong>{ $form.errors?.password }</strong>
                            </span>
                        {/if}
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="password-confirm" class="col-md-4 col-form-label text-md-end">Conferma Password</label>

                    <div class="col-md-6">
                        <input bind:value={$form.password_confirmation} id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                    </div>
                </div>

            </LogBox>

        </div>
    </div>
</GuestLayout>

<style>
    /*a {*/
    /*    color: black;*/
    /*    text-decoration: none;*/
    /*}*/

    /*a:hover, a.btn-link:hover {*/
    /*    color: #b125e2;*/
    /*}*/
</style>
