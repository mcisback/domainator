<script>
    import { page } from '@inertiajs/inertia-svelte'
    import route from 'ziggy-js';

    import GuestLayout from '../../Layouts/GuestLayout.svelte'
    import LogBox from "../../Components/LogBox.svelte";

    // export let errors = {}

    let email = ''
    let password = ''
    let csrf_token = ''

    // const onSubmit = async () => {
    //     const res = await fetch(route('login'), {
    //         method: 'POST',
    //         headers: {
    //
    //         },
    //         body: JSON.stringify({
    //             email,
    //             password,
    //             "_token": csrf_token
    //         })
    //     })
    //
    //     const json = await res.json()
    //     result = JSON.stringify(json)
    // }
</script>

<GuestLayout let:props>
    <div class="container">
        <div>

            <LogBox action={route('auth.passwords.reset')} title="Recupera Password" csrf_token={props.csrf_token}>

                <div class="row mb-3">
                    <label for="email" class="col-md-4 col-form-label text-md-end">Email</label>

                    <div class="col-md-6">
                        <input bind:value={email} id="email" type="email" class="{props.errors?.email ? 'form-control is-invalid' : 'form-control'}" name="email" required autocomplete="email">

                        {#if props.errors?.email}
                            <span class="invalid-feedback" role="alert">
                                <strong>{props.errors?.email}</strong>
                            </span>
                        {/if}
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="password" class="col-md-4 col-form-label text-md-end">Password</label>

                    <div class="col-md-6">
                        <input bind:value={password} id="password" type="password" class="{props.errors?.password ? 'form-control is-invalid' : 'form-control'}" name="password" required autocomplete="current-password">

                        {#if props.errors?.password}
                            <span class="invalid-feedback" role="alert">
                                <strong>{props.errors?.password}</strong>
                            </span>
                        {/if}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6 offset-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">

                            <label class="form-check-label" for="remember">
                                Ricordami
                            </label>
                        </div>
                    </div>
                </div>

                <span slot="submit">
                    {#if route('password.request')}
                        <a class="btn btn-link" href={route('password.request')}>
                            Recupera La Password
                        </a>
                    {/if}
                </span>

            </LogBox>

        </div>
    </div>
</GuestLayout>

<style>
    a {
        color: black;
        text-decoration: none;
    }

    a:hover, a.btn-link:hover {
        color: #b125e2;
    }
</style>
