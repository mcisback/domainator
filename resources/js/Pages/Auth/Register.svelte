<script>
    import { Inertia } from '@inertiajs/inertia'
    import {page, useForm} from '@inertiajs/inertia-svelte'
    import route from 'ziggy-js';

    import GuestLayout from '../Layouts/GuestLayout.svelte'
    import LogBox from "../Components/LogBox.svelte";

    let form = useForm({
        firstName: null,
        lastName: null,
        username: null,
        email: null,
        password: null,
        password_confirmation: null
    })

    const getUsername = () => {
        if($form.firstName !== '' && $form.lastName !== '') {
            $form.username = `${$form.firstName}.${$form.lastName}`.toLowerCase()
        } else {
            $form.username = ''
        }

        console.log('username: ', $form.username)
    }


    function onSubmit() {
        $form.post(route('register'), {
            // headers: {
            //     'Content-Type': 'application/json',
            //     'Accept': 'application/json'
            // },
            onSuccess: (...p) => console.log('onSuccess: ', p),
            onError: (...p) => console.log('onError: ', p)
        })
    }

</script>

<GuestLayout let:props>
    <div class="container">
        <div>

            <LogBox action={route('register')}  onSubmit={onSubmit} title="Crea Nuovo Utente">

                <div class="row mb-3">
                    <label for="name" class="col-md-4 col-form-label text-md-end">Nome</label>

                    <div class="col-md-6">
                        <input bind:value={$form.firstName} on:input={getUsername} id="firstName" type="text" class="{$form.errors?.firstName ? 'form-control is-invalid' : 'form-control'}" name="firstName" required autocomplete="name">

                        {#if $form.errors?.firstName}
                            <span class="invalid-feedback" role="alert">
                                <strong>{$form.errors?.firstName}</strong>
                            </span>
                        {/if}
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="lastName" class="col-md-4 col-form-label text-md-end">Cognome</label>

                    <div class="col-md-6">
                        <input bind:value={$form.lastName} on:input={getUsername} id="lastName" type="text" class="{$form.errors?.lastName ? 'form-control is-invalid' : 'form-control'}" name="lastname" required autocomplete="family-name">

                        {#if $form.errors?.lastname}
                            <span class="invalid-feedback" role="alert">
                                <strong>{$form.errors?.name}</strong>
                            </span>
                        {/if}
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="username" class="col-md-4 col-form-label text-md-end">Nickname</label>

                    <div class="col-md-6">
                        <input id="username" bind:value={$form.username} type="text" class="{$form.errors?.username ? 'form-control is-invalid' : 'form-control'}" name="username" required autocomplete="username">

                        {#if $form.errors?.username}
                            <span class="invalid-feedback" role="alert">
                                <strong>{$form.errors?.username}</strong>
                            </span>
                        {/if}
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="email" class="col-md-4 col-form-label text-md-end">Email</label>

                    <div class="col-md-6">
                        <input bind:value={$form.email} id="email" type="email" class="{$form.errors?.email ? 'form-control is-invalid' : 'form-control'}" name="email" required autocomplete="email">

                        {#if $form.errors?.email}
                            <span class="invalid-feedback" role="alert">
                                <strong>{$form.errors?.email}</strong>
                            </span>
                        {/if}
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="password" class="col-md-4 col-form-label text-md-end">Password</label>

                    <div class="col-md-6">
                        <input bind:value={$form.password} id="password" type="password" class="{$form.errors?.password ? 'form-control is-invalid' : 'form-control'}" name="password" required autocomplete="new-password">

                        {#if $form.errors?.password}
                            <span class="invalid-feedback" role="alert">
                                <strong>{$form.errors?.password}</strong>
                            </span>
                        {/if}
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="password-confirm" class="col-md-4 col-form-label text-md-end">Conferma Password</label>

                    <div class="col-md-6">
                        <input bind:value={$form.password_confirmation} id="password-confirm" type="password" class="{$form.errors?.password ? 'form-control is-invalid' : 'form-control'}" name="password_confirmation" required autocomplete="new-password">
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
