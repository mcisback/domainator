<script>
    import route from 'ziggy-js';

    import DashboardLayout from '../../Layouts/DashboardLayout.svelte'

    import AlertBox from "../../Components/Alerts/AlertBox.svelte";
    import { slide } from 'svelte/transition';
    import SedoAccountPanel from "../../Components/DomainRequests/SedoAccountPanel.svelte";

    export let domainRequests = []
    export let sedoAccounts = []
    export let sedoCategories = []
    export let sedoLanguages = []

    let form = {
        success: false,
        message: null
    }

    let currentDomainRequest = null
    let currentSedoAccountId = null
    let currency = "USD"
    let currencySymbol = '$'


    if(sedoAccounts.length > 0) {
        currentSedoAccountId = sedoAccounts[0].id
    }

    const columns = [
        // 'id',
        'domains',
        'submitted_by_user',
        'submitted_at',
        'approved_by_user',
        'approved_at',
        'registered_at',
        'sedo_account',
        'verified_on_sedo_at',
    ]

    let spinners = {
        registerDomain: false,
        deleteDomain: false,
        deleteDomainsRequest: false,
        addDomainToSEDO: false,
        verifyDomainOnSEDO: false,
    }

    console.log('domainRequests: ', domainRequests)
    console.log('sedoAccounts: ', sedoAccounts)
    console.log('sedoCategories: ', sedoCategories)
    console.log('sedoLanguages: ', sedoLanguages)

</script>

<DashboardLayout let:props let:sections let:currentUser>
    <div class="container w-100 p-4">
        <h1 class="text-center">Domain Requests</h1>
        <h2 class="text-center">(Click Rows For Domain Tools)</h2>

        <div class="row mb-3">
            <div class="mx-auto">
                {#if form.message}
                    <div transition:slide>
                        <AlertBox bind:success={form.success}>
                            <span>{@html form.message}</span>
                        </AlertBox>
                    </div>
                {/if}
            </div>
        </div>

        {#if currentDomainRequest !== null}
            {#key currentDomainRequest}
                <SedoAccountPanel
                    bind:currentDomainRequest={currentDomainRequest}
                    bind:currentSedoAccountId={currentSedoAccountId}
                    bind:sedoAccounts={sedoAccounts}
                    bind:sedoLanguages={sedoLanguages}
                    bind:sedoCategories={sedoCategories}
                    bind:domainRequests={domainRequests}
                    bind:spinners={spinners}
                    bind:form={form}
                />
            {/key}
        {/if}

        {#if sedoAccounts.length > 0}

            {#if domainRequests.length > 0}
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
                            {#each domainRequests as domainRequest, i}
                                <tr on:click={() => {
                                    currentDomainRequest = null
                                    currentDomainRequest = domainRequests[i]
                                }}>

                                    {#each columns as col}
                                        {#if col === 'domains'}
                                            <table>
                                                <tbody>
                                                    {#each domainRequest[col] as domain}
                                                        <tr>
                                                            <td>
                                                                {#if !domain.registered}
                                                                    <span style="color: red;">
                                                                        <i class="fa-sharp fa-solid fa-xmark"></i>
                                                                    </span>
                                                                {:else}
                                                                    <span style="color: green;">
                                                                        <i class="fa-solid fa-check"></i>
                                                                    </span>
                                                                {/if}
                                                                &nbsp;
                                                                {domain.domain}
                                                            </td>
                                                            <td>
                                                                {domain.price ? (domain.price + ' ' + currencySymbol) : 'N.A.'}
                                                            </td>
                                                        </tr>
                                                    {/each}
                                                </tbody>
                                            </table>
                                        {:else if col === 'approved_by_user'}
                                            <td>
                                                {#if domainRequest[col] === null}
                                                    <span style="color: red;">
                                                        <i class="fa-sharp fa-solid fa-xmark"></i>
                                                    </span>
                                                {:else}
                                                    <span style="color: green;">
                                                        <i class="fa-solid fa-check"></i>
                                                        &nbsp;
                                                        {domainRequest[col].username}
                                                    </span>
                                                {/if}
                                            </td>
                                        {:else if col === 'submitted_by_user'}
                                            <td>
                                                {#if domainRequest[col] === null}
                                                    <span style="color: red;">
                                                        <i class="fa-sharp fa-solid fa-xmark"></i>
                                                    </span>
                                                {:else}
                                                    <span style="color: green;">
                                                        <i class="fa-solid fa-check"></i>
                                                        &nbsp;
                                                        {domainRequest[col].username}
                                                    </span>
                                                {/if}
                                            </td>
                                        {:else if col === 'sedo_account'}
                                            <td>
                                                {#if domainRequest[col] === null}
                                                    <span style="color: red;">
                                                        <i class="fa-sharp fa-solid fa-xmark"></i>
                                                    </span>
                                                {:else}
                                                    <span style="color: green;">
                                                        <i class="fa-solid fa-check"></i>
                                                        &nbsp;
                                                        {domainRequest[col].name}
                                                    </span>
                                                {/if}
                                            </td>
                                        {:else if col === 'verified_on_sedo_at'}
                                            <td>
                                                {#if domainRequest[col] === null}
                                                    <span style="color: red;">
                                                        <i class="fa-sharp fa-solid fa-xmark"></i>
                                                    </span>
                                                {:else}
                                                    <span style="color: green;">
                                                        <i class="fa-solid fa-check"></i>
                                                        &nbsp;
                                                        {domainRequest[col]}
                                                    </span>
                                                {/if}
                                            </td>
                                        {:else if col === 'registered_at'}
                                            <td>
                                                {#if domainRequest[col] === null}
                                                    <span style="color: red;">
                                                        <i class="fa-sharp fa-solid fa-xmark"></i>
                                                    </span>
                                                {:else}
                                                    <span style="color: green;">
                                                        {domainRequest[col]}
                                                    </span>
                                                {/if}
                                            </td>
                                        {:else if col === 'submitted_at'}
                                            <td>
                                                {#if domainRequest[col] === null}
                                                    <span style="color: red;">
                                                        <i class="fa-sharp fa-solid fa-xmark"></i>
                                                    </span>
                                                {:else}
                                                    <span style="color: green;">
                                                        {domainRequest[col]}
                                                    </span>
                                                {/if}
                                            </td>
                                        {:else if col === 'approved_at'}
                                            <td>
                                                {#if domainRequest[col] === null}
                                                    <span style="color: red;">
                                                        <i class="fa-sharp fa-solid fa-xmark"></i>
                                                    </span>
                                                {:else}
                                                    <span style="color: green;">
                                                        {domainRequest[col]}
                                                    </span>
                                                {/if}
                                            </td>
                                        {:else}
                                            <td>
                                                {domainRequest[col]}
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
                Add A SEDO Account to approve Domain Requests
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
