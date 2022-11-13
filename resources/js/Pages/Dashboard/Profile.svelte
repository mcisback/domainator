<script>
    import route from 'ziggy-js';
    import { fade } from 'svelte/transition';

    import DashboardLayout from '../Layouts/DashboardLayout.svelte';
    import {page, useForm} from "@inertiajs/inertia-svelte";
    import Spinner from "../Components/Spinners/Spinner.svelte";

    export let status = false

    export let currentUser = $page.props.user

    const fields = Object.keys(currentUser)

    const excludes = [
        'id',
        'roles',
        'permissions',
        'email_verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
        'password',
    ]

    const aliases = {
        firstName: 'Nome',
        lastName: 'Cognome',
        roles: 'Ruoli',
        permissions: 'Permessi'
    }

    const disabled = [
        'email'
    ]

    let form = useForm({
        ...currentUser
    })

    // const conditionallyDisabled = (node, { disabled }) => {
    //     if (disabled) {
    //         node.setAttribute('disabled', 'true')
    //     } else {
    //         node.removeAttribute('disabled')
    //     }
    //
    //     return {
    //         destroy() {
    //             node.removeAttribute('disabled')
    //         }
    //     }
    // }

    let showSpinner = false

    console.log('$form: ', $form, fields)

    const onSubmit = async () => {
        showSpinner = true

        $form.put(route('dashboard.me.update', {
            id: currentUser.id
        }), {
            // headers: {
            //     'Content-Type': 'application/json',
            //     'Accept': 'application/json'
            // },
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

<DashboardLayout let:props let:sections let:currentUser>
    <div class="container w-100 mt-5">
        {#if status}
            <div class="row">
                <div class="alert alert-success" role="alert">
                    {status}
                </div>
            </div>
        {/if}
        <div class="row">
            <form action={route('dashboard.me.update', {
            id: currentUser.id
        })} method="POST" on:submit|preventDefault={onSubmit}>
                {#each fields as field}
                    {#if !excludes.includes(field)}
                        <div class="mb-3 row">
                            <label for={field} class="col-md-4 col-form-label text-md-end text-capitalize">
                                {aliases[field] ?? field}
                            </label>
                            <div class="col-md-6">
                                <input
                                    class="form-control" name={field}
                                    id={field}
                                    bind:value={$form[field]}
                                    disabled={disabled.includes(field) || null}
                                >
                            </div>
                        </div>
                    {/if}
                {/each}

                <div class="row mb-0">
                    <div class="col-md-8 offset-md-4">
                        <button type="submit" class="btn btn-primary btn-red">
                            Update Profile
                        </button>
                    </div>
                </div>

                {#if showSpinner}
                    <div class="row mt-4 text-center" transition:fade>
                        <div>
                            <Spinner />
                        </div>
                    </div>
                {/if}
            </form>
        </div>
    </div>
</DashboardLayout>

<style>

</style>
