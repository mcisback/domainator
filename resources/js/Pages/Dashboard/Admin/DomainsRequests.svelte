<script>
    import route from 'ziggy-js';
    import moment from 'moment';

    import DashboardLayout from '../../Layouts/DashboardLayout.svelte'
    import ModelTable from "../../Components/ModelTable.svelte";
    import {onMount} from "svelte";

    import {LaraUser} from '../../../user';
    import {color} from "quill/ui/icons";
    import AlertBox from "../../Components/Alerts/AlertBox.svelte";
    import { slide } from 'svelte/transition';

    export let domains = []
    export let sedoAccounts = []

    let formMessage = null
    let formSuccess = false

    const columns = [
        'id',
        'domain',
        'registered',
        'requested_by_user',
        'approved_by_user',
        'sedo_account',
    ]

    const spinners = {
        approveDomain: false,
    }

    const formData = {

    }

    console.log('domains: ', domains)
    console.log('sedoAccounts: ', sedoAccounts)

    const approveDomain = (domain) =>  {
        spinners.approveDomain = true

        console.log('approveDomain() Sending domain: ', domain)

        axios.post(route('api.index', {
            action: 'approveDomain',
            domain: domain.id
        }))
            .then(res => res.data)
            .then(data => {
                console.log('Response data: ', data)

                spinners.approveDomain = false
                formSuccess = data.success
                formMessage = data.message
                domains = data.domains
            })
            .catch(err => {
                spinners.approveDomain = false

                formSuccess = false
                formMessage = err.response.data.message

                console.log('Err: ', err.response.data)
            })
    }

</script>

<DashboardLayout let:props let:sections let:currentUser>
    <div class="container w-100 p-5">
        <h1 class="text-center">Domains Requests</h1>

        <div class="row mb-3">
            <div class="mx-auto">
                {#if formMessage}
                    <div transition:slide>
                        <AlertBox bind:success={formSuccess}>
                            <span>{formMessage}</span>
                        </AlertBox>
                    </div>
                {/if}
            </div>
        </div>

        {#if sedoAccounts.length > 0}

            <div class="row">

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">
                                #
                            </th>
                            {#each columns as col}
                                <th scope="col" class="text-capitalize">
                                    {col.replaceAll('_', ' ')}
                                </th>
                            {/each}
                        </tr>
                    </thead>

                    <tbody>
                        {#each domains as domain, i}
                            <tr>
                                <td>
                                    <button class="btn btn-primary btn-red" on:click={() => approveDomain(domain[i])} disabled={domain['registered'] === 1 || null}>
                                        <i class="fa-solid fa-arrow-up-from-bracket"></i>
                                    </button>
                                </td>
                                {#each columns as col}
                                    {#if col === 'approved_by_user'}
                                        <td>
                                            {#if domain[col] === null}
                                                <span style="color: red;">
                                                    <i class="fa-sharp fa-solid fa-xmark"></i>
                                                    &nbsp;
                                                    Waiting Approval
                                                </span>
                                            {:else}
                                                <span style="color: green;">
                                                    <i class="fa-solid fa-check"></i>
                                                    &nbsp;
                                                    {domain[col].username}
                                                </span>
                                            {/if}
                                        </td>
                                    {:else if col === 'requested_by_user'}
                                        <td>
                                            {#if domain[col] === null}
                                                <span style="color: red;">
                                                    <i class="fa-sharp fa-solid fa-xmark"></i>
                                                </span>
                                            {:else}
                                                <span style="color: green;">
                                                    <i class="fa-solid fa-check"></i>
                                                    &nbsp;
                                                    {domain[col].username}
                                                </span>
                                            {/if}
                                        </td>
                                    {:else if col === 'sedo_account'}
                                        <td>
                                            {#if domain[col] === null}
                                                <select name={`sedo_account_${i}`} id={`sedo_account_${i}`} class="form-select">
                                                    <option value="null">
                                                        Select SEDO Account
                                                    </option>
                                                    {#each sedoAccounts as {id, name, partner_id}}
                                                        <option value={id}>
                                                            {name}:{partner_id}
                                                        </option>
                                                    {/each}
                                                </select>
                                            {:else}
                                                <span style="color: green;">
                                                    <i class="fa-solid fa-check"></i>
                                                    &nbsp;
                                                    {domain[col].name}
                                                </span>
                                            {/if}
                                        </td>
                                    {:else if col === 'registered'}
                                        <td>
                                            {#if domain[col] === 0}
                                                <span style="color: red;">
                                                    <i class="fa-sharp fa-solid fa-xmark"></i>
                                                    &nbsp;
                                                    Unregistered
                                                </span>
                                            {:else}
                                                <span style="color: green;">
                                                    <i class="fa-solid fa-check"></i>
                                                </span>
                                            {/if}
                                        </td>
                                    {:else}
                                        <td>
                                            {domain[col]}
                                        </td>
                                    {/if}
                                {/each}
                            </tr>
                        {/each}
                    </tbody>
                </table>

            </div>
        {:else}
            <div class="row mb-3">
                No SEDO Accounts
                <br>
                Add A SEDO Account to approve domains
            </div>
        {/if}
    </div>
</DashboardLayout>

<style>
    table tbody tr:hover {
        background-color: cornsilk;
        cursor: pointer;
    }
</style>
