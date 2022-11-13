<script>
    import {page, useForm} from '@inertiajs/inertia-svelte'
    import { fade } from 'svelte/transition';

    import route from 'ziggy-js';

    import GuestLayout from '../../Layouts/GuestLayout.svelte'
    import LogBox from "../../Components/LogBox.svelte";
    import Spinner from "../../Components/Spinners/Spinner.svelte";

    // export let errors = {}

    let showSpinner = false

    let form = useForm({
        email: null,
    })

    const onSubmit = async () => {
        showSpinner = true
        $form.post(route('password.email'), {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            onSuccess: (...p) => {
                showSpinner = false

                console.log('onSuccess: ', $form, ...p)
            },
            onError: (errors) => {
                showSpinner = false
                $form.errors = errors

                console.log('$form: ', $form)
            }
        })
    }
</script>

<GuestLayout let:props>
    <div class="container">
        <div>

            <LogBox bind:showSpinner={showSpinner} action={route('password.email')} onSubmit={onSubmit} title="Recupera Password">

                <slot name="beforeForm">
                    {#if props.status}
                        <div transition:fade class="alert alert-success" role="alert">
                            {props.status}
                        </div>
                    {/if}
                </slot>

                <div class="row mb-3">
                    <label for="email" class="col-md-4 col-form-label text-md-end">Indirizzo Email</label>

                    <div class="col-md-6">
                        <input bind:value={$form.email} id="email" type="email" class={$form.errors?.email ? 'form-control is-invalid' : 'form-control'} name="email" required autocomplete="email">

                        {#if $form.errors?.email }
                            <span class="invalid-feedback" role="alert">
                                <strong>{$form.errors?.email}</strong>
                            </span>
                        {/if}
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
