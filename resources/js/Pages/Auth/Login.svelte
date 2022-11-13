<script>
    import {page, remember, useForm} from '@inertiajs/inertia-svelte'
    import route from 'ziggy-js';

    import GuestLayout from '../Layouts/GuestLayout.svelte'
    import LogBox from "../Components/LogBox.svelte";

    let form = useForm({
        email: null,
        password: null,
        remember: false,
    })

    async function onSubmit() {
        $form.post(route('login'), {
            // headers: {
            //     'Content-Type': 'application/json',
            //     'Accept': 'application/json'
            // },
            onSuccess: (...p) => console.log('onSuccess: ', $form, ...p),
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

            <LogBox onSubmit={onSubmit} title="Login">

                {#if $form.errors?.message}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <span class="invalid-feedback" role="alert">
                                <strong>{$form.errors?.message}</strong>
                            </span>
                        </div>
                    </div>
                {/if}



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
                        <input bind:value={$form.password} id="password" type="password" class="{$form.errors?.password ? 'form-control is-invalid' : 'form-control'}" name="password" required autocomplete="current-password">

                        {#if $form.errors?.password}
                            <span class="invalid-feedback" role="alert">
                                <strong>{$form.errors?.password}</strong>
                            </span>
                        {/if}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6 offset-md-4">
                        <div class="form-check">
                            <input bind:checked={form.remember} class="form-check-input" type="checkbox" name="remember" id="remember">

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
