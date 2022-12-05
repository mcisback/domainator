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
    import Spinner from "../../Components/Spinners/Spinner.svelte";
    import Switch from "../../Components/Checkboxes/Switch.svelte";

    export let domains = []
    export let sedoAccounts = []
    export let sedoCategories = []
    export let sedoLanguages = []

    let formMessage = null
    let formSuccess = false

    let currentDomain = null
    let currentSedoAccountId = null
    let enableWhoisProtection = false
    let sedoCategoryIds = []
    let sedoLanguage = 'en'
    let isForSale = false
    let price = 0
    let minprice = 0
    let fixedprice = false
    let currency = "USD"


    if(sedoAccounts.length > 0) {
        currentSedoAccountId = sedoAccounts[0].id
    }

    const columns = [
        // 'id',
        'domain',
        'registered',
        'submitted_by_user',
        'submitted_at',
        'approved_by_user',
        'approved_at',
        'registered_at',
        'sedo_account',
    ]

    const spinners = {
        registerDomain: false,
        deleteDomain: false,
        addDomainToSEDO: false,
    }

    const formData = {

    }

    console.log('domains: ', domains)
    console.log('sedoAccounts: ', sedoAccounts)
    console.log('sedoCategories: ', sedoCategories)
    console.log('sedoLanguages: ', sedoLanguages)

    const registerDomain = (domain) =>  {
        spinners.registerDomain = true

        console.log('registerDomain() Sending domain: ', domain)

        axios.post(route('dashboard.domains.register', {
            domain: domain.id
        }), {
            enableWhoisProtection
        })
        .then(res => res.data)
        .then(data => {
            console.log('registerDomain() Response data: ', data)

            spinners.registerDomain = false
            formSuccess = data.success
            formMessage = data.message
            domains = data.domains

            currentDomain.registered = data.success
        })
        .catch(err => {
            spinners.registerDomain = false

            formSuccess = false
            formMessage = err.response.data.message

            console.log('Err: ', err.response.data)
        })
    }

    const addDomainToSEDO = (domain) =>  {
        spinners.addDomainToSEDO = true

        console.log('addDomainToSEDO() Sending domain: ', {domain, sedoCategoryIds, sedoLanguage})

        return

        axios.post(route('dashboard.domains.addToSedo', {
            domain: domain.id,
            sedoAccount: currentSedoAccountId
        }), {
            sedoCategoryIds
        })
            .then(res => res.data)
            .then(data => {
                console.log('addDomainToSEDO() Response data: ', data)

                spinners.addDomainToSEDO = false
                formSuccess = data.success
                formMessage = data.message
                domains = data.domains
            })
            .catch(err => {
                spinners.addDomainToSEDO = false

                formSuccess = false
                formMessage = err.response.data.message

                console.log('Err: ', err.response.data)
            })
    }

    const deleteDomain = (domain) =>  {
        spinners.deleteDomain = true

        console.log('deleteDomain() Sending domain: ', domain)

        axios.delete(route('dashboard.domains.destroy', {
            domain: domain.id
        }))
        .then(res => res.data)
        .then(data => {
            console.log('deleteDomain() Response data: ', data)

            spinners.deleteDomain = false
            formSuccess = data.success
            formMessage = data.message
            domains = data.domains

            currentDomain = null
        })
        .catch(err => {
            spinners.deleteDomain = false

            formSuccess = false
            formMessage = err.response.data.message

            console.log('Err: ', err.response.data)
        })
    }

</script>

<DashboardLayout let:props let:sections let:currentUser>
    <div class="container w-100 p-4">
        <h1 class="text-center">Domains Requests</h1>
        <h2 class="text-center">(Click Row For Domain Tools)</h2>

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

        {#if currentDomain !== null}
            <div class="row mb-3 p-4" transition:slide>
                <div class="row mb-3 fw-bold">
                    <label for="domain">
                        Domain
                    </label>
                    <input type="text" name="domain" id="domain" class="form-control" value={currentDomain.domain} disabled>
                </div>

                {#if currentDomain.sedo_account === null && currentDomain.registered}

                    <div class="row mb-3">
                        <AlertBox type="warning">
                            <strong>&#x26A0; Note:</strong> Adding Domain To SEDO does not have an immediate effect as domains first have to pass a couple of checks before they get added to an account. You will be notified via eMail in case any checks fail.
                            <br>
                            This is how SEDO API works.
                        </AlertBox>
                    </div>

                    <div class="row mb-3">
                        <label for={`sedo_account`}>
                            Select SEDO Account
                        </label>
                        <select name={`sedo_account`} id={`sedo_account`} bind:value={currentSedoAccountId} class="form-select">
                            {#each sedoAccounts as {id, name, partner_id}}
                                <option value={id}>
                                    {name}:{partner_id}
                                </option>
                            {/each}
                        </select>
                    </div>

                    <div class="row mb-3">
                        <label for={`sedo_categories`}>
                            SEDO Domain Categories
                        </label>
                        <select name={`sedo_categories`} id={`sedo_categories`} bind:value={sedoCategoryIds} class="form-select" multiple>
                            {#each Object.entries(sedoCategories) as [id, category]}
                                <option value={id}>
                                    {id} : {category}
                                </option>
                            {/each}
                        </select>
                    </div>

                    <div class="row mb-3">
                        <label for={`sedo_language`}>
                            SEDO Domain Language
                        </label>
                        <select name={`sedo_language`} id={`sedo_language`} bind:value={sedoLanguage} class="form-select">
                            {#each Object.entries(sedoLanguages) as [iso, language]}
                                <option value={iso}>
                                    {iso} : {language}
                                </option>
                            {/each}
                        </select>
                    </div>


                {/if}

                <div class="row mb-3">
                    <div class="col text-center">
                        <button class="btn btn-primary btn-red" on:click={() => registerDomain(currentDomain)} disabled={currentDomain.registered || null}>
                            {#if spinners.registerDomain}
                                <i class="fa-solid fa-check"></i>
                                <Spinner />
                            {:else}
                                <i class="fa-solid fa-check"></i>
                                Register
                            {/if}
                        </button>
                    </div>

                    <div class="col text-center">
                        <button class="btn btn-primary btn-red" on:click={() => addDomainToSEDO(currentDomain)} disabled={!(currentDomain.sedo_account === null && currentDomain.registered)}>
                            <i class="fa-solid fa-plus"></i>
                            Add To SEDO
                        </button>
                    </div>

                    <div class="col text-center">
                        <button class="btn btn-primary btn-red" on:click={() => deleteDomain(currentDomain)} disabled={(currentDomain.registered || null)}>
                            {#if spinners.deleteDomain}
                                <i class="fa-solid fa-trash"></i>
                                <Spinner />
                            {:else}
                                <i class="fa-solid fa-trash"></i>
                                Delete
                            {/if}
                        </button>
                    </div>
                </div>

                {#if !currentDomain.registered}
                    <div class="row mb-3">
                        <Switch id="whois_protection" bind:checked={enableWhoisProtection}>
                            Whois Protection
                        </Switch>
                    </div>
                {/if}

            </div>
        {/if}

        {#if sedoAccounts.length > 0}

            {#if domains.length > 0}
                <div class="row">

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                {#each columns as col}
                                    <th scope="col" class="text-capitalize">
                                        {col.replaceAll('_', ' ')}
                                    </th>
                                {/each}
                            </tr>
                        </thead>

                        <tbody>
                            {#each domains as domain, i}
                                <tr on:click={() => currentDomain = domains[i]}>
                                    {#each columns as col}
                                        {#if col === 'approved_by_user'}
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
                                        {:else if col === 'submitted_by_user'}
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
                                                    <span style="color: red;">
                                                        <i class="fa-sharp fa-solid fa-xmark"></i>
                                                    </span>
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
                                                    </span>
                                                {:else}
                                                    <span style="color: green;">
                                                        <i class="fa-solid fa-check"></i>
                                                    </span>
                                                {/if}
                                            </td>
                                        {:else if col === 'registered_at'}
                                            <td>
                                                {#if domain[col] === null}
                                                    <span style="color: red;">
                                                        <i class="fa-sharp fa-solid fa-xmark"></i>
                                                    </span>
                                                {:else}
                                                    <span style="color: green;">
                                                        {domain[col]}
                                                    </span>
                                                {/if}
                                            </td>
                                        {:else if col === 'submitted_at'}
                                            <td>
                                                {#if domain[col] === null}
                                                    <span style="color: red;">
                                                        <i class="fa-sharp fa-solid fa-xmark"></i>
                                                    </span>
                                                {:else}
                                                    <span style="color: green;">
                                                        {domain[col]}
                                                    </span>
                                                {/if}
                                            </td>
                                        {:else if col === 'approved_at'}
                                            <td>
                                                {#if domain[col] === null}
                                                    <span style="color: red;">
                                                        <i class="fa-sharp fa-solid fa-xmark"></i>
                                                    </span>
                                                {:else}
                                                    <span style="color: green;">
                                                        {domain[col]}
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
                    No Domains Requests Yet
                </div>
            {/if}
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
    /*table tbody tr {*/
    /*    display: flex;*/
    /*    flex-wrap: wrap;*/
    /*}*/

    /*table tbody td {*/
    /*    display: block;*/
    /*}*/
</style>
