<script>
    import route from 'ziggy-js';

    import DashboardLayout from '../../Layouts/DashboardLayout.svelte'

    import AlertBox from "../../Components/Alerts/AlertBox.svelte";
    import { slide } from 'svelte/transition';
    import Spinner from "../../Components/Spinners/Spinner.svelte";
    import Switch from "../../Components/Checkboxes/Switch.svelte";

    export let domainRequests = []
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
        'verified_on_sedo_at',
    ]

    const spinners = {
        registerDomain: false,
        deleteDomain: false,
        addDomainToSEDO: false,
        verifyDomainOnSEDO: false,
    }

    const formData = {

    }

    console.log('domainRequests: ', domainRequests)
    console.log('sedoAccounts: ', sedoAccounts)
    console.log('sedoCategories: ', sedoCategories)
    console.log('sedoLanguages: ', sedoLanguages)

    const registerDomain = (domain) =>  {
        spinners.registerDomain = true

        console.log('registerDomain() Sending domain: ', domain)

        axios.post(route('dashboard.domainRequests.register', {
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
            domainRequests = data.domainRequests

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

        axios.post(route('dashboard.domainRequests.addToSedo', {
            domain: domain.id,
            sedoAccount: currentSedoAccountId
        }), {
            sedoCategoryIds,
            sedoLanguage,
            isForSale,
            price,
            minprice,
            fixedprice,
        })
            .then(res => res.data)
            .then(data => {
                console.log('addDomainToSEDO() Response data: ', data)

                spinners.addDomainToSEDO = false
                formSuccess = data.success
                formMessage = data.message
                domainRequests = data.domainRequests

                currentDomain = updateCurrentDomain(domain, data.domainRequests)
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

        axios.delete(route('dashboard.domainRequests.destroy', {
            domain: domain.id
        }))
        .then(res => res.data)
        .then(data => {
            console.log('deleteDomain() Response data: ', data)

            spinners.deleteDomain = false
            formSuccess = data.success
            formMessage = data.message
            domainRequests = data.domainRequests

            currentDomain = null
        })
        .catch(err => {
            spinners.deleteDomain = false

            formSuccess = false
            formMessage = err.response.data.message

            console.log('Err: ', err.response.data)
        })
    }

    const updateCurrentDomain = (domain, domainRequests) => {
        return domainRequests.filter(d => d.domain === domain.domain)[0]
    }

    const verifyDomainOnSEDO = (domain) =>  {
        spinners.verifyDomainOnSEDO = true

        axios.post(route('dashboard.domainRequests.sedoVerify', {
            domain: domain.id,
            sedoAccount: domain.sedo_account.id
        }))
            .then(res => res.data)
            .then(data => {
                console.log('verifyDomainOnSEDO() Response data: ', data)

                spinners.verifyDomainOnSEDO = false
                formSuccess = data.success
                formMessage = data.message
                domainRequests = data.domainRequests

                currentDomain = updateCurrentDomain(domain, data.domainRequests)
            })
            .catch(err => {
                spinners.verifyDomainOnSEDO = false

                formSuccess = false
                formMessage = err.response.data.message

                console.log('Err: ', err.response.data)
            })
    }

</script>

<DashboardLayout let:props let:sections let:currentUser>
    <div class="container w-100 p-4">
        <h1 class="text-center">Domain Requests</h1>
        <h2 class="text-center">(Click Rows For Domain Tools)</h2>

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

                {#if currentDomain.sedo_account !== null && currentDomain.verified_on_sedo_at === null}
                    <div class="row mb-3">
                        <label for="sedo_account">SEDO Account</label>
                        <input type="text" name="sedo_account" id="sedo_account"  class="form-control" value={currentDomain.sedo_account.name} disabled>
                    </div>
                    <div class="row mb-3">
                        <AlertBox type="warning">
                            <strong>&#x26A0; Note:</strong> You can verify <b>{currentDomain.domain}</b> on SEDO by adding a <b>DNS TXT Record</b> with this values:
                            <br>
                            Host: <b>blank</b> or <b>@</b>
                            <br>
                            Value: <b>{currentDomain.sedo_account.domain_ownership_id}</b>
                            <br>
                            <br>
                            This is described <a href="https://sedo.com/member/ownership_verification.php" target="_blank" rel="noreferrer">here</a>
                        </AlertBox>
                    </div>
                {/if}

                {#if currentDomain.sedo_account === null && currentDomain.registered}

                    <div class="row mb-3">
                        <AlertBox type="warning">
                            <strong>&#x26A0; Note:</strong> Adding domainRequests To SEDO does not have an immediate effect as domainRequests first have to pass a couple of checks before they get added to an account. You will be notified via eMail in case any checks fail.
                            <br>
                            This is how SEDO API works.
                        </AlertBox>
                    </div>

                    <div class="row mb-3">
                        <label for={`sedo_account`}>
                            Select SEDO Account
                        </label>
                        <select name={`sedo_account`} id={`sedo_account`} bind:value={currentSedoAccountId} class="form-select" disabled={currentDomain.sedo_account || null}>
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

                    <div class="row mb-3 align-items-center">
                        <div class="col">
                            <Switch id="is_for_sale" bind:checked={isForSale}>
                                Is For Sale ?
                            </Switch>
                        </div>

                        <div class="col">
                            <Switch id="fixed_price" bind:checked={fixedprice}>
                                Fixed Price ?
                            </Switch>
                        </div>

                        <div class="col">

                            <div class="row">
                                <label for="price" class="col col-form-label">Price</label>

                                <div class="col">
                                    <input type="number" name="price" id="price" bind:value={price} class="form-control">
                                </div>
                            </div>

                        </div>

                        <div class="col">

                            <div class="row">
                                <label for="minprice" class="col col-form-label">Min. Price</label>

                                <div class="col">
                                    <input type="number" name="minprice" id="minprice" bind:value={minprice} class="form-control">
                                </div>
                            </div>

                        </div>
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
                            {#if spinners.addDomainToSEDO}
                                <i class="fa-solid fa-plus"></i>
                                <Spinner />
                            {:else}
                                <i class="fa-solid fa-plus"></i>
                                Add to SEDO
                            {/if}
                        </button>
                    </div>

                    {#if currentDomain.registered && currentDomain.sedo_account !== null}

                        <div class="col text-center" transition:slide>
                            <button class="btn btn-primary btn-red" on:click={() => verifyDomainOnSEDO(currentDomain)}
                                    disabled={currentDomain.verified_on_sedo_at || null}>
                                {#if spinners.verifyDomainOnSEDO}
                                    <i class="fa-solid fa-certificate"></i>
                                    <Spinner />
                                {:else}
                                    <i class="fa-solid fa-certificate"></i>
                                    Verify Domain On SEDO
                                {/if}
                            </button>
                        </div>

                    {/if}

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
                            {#each domainRequests as domain, i}
                                <tr on:click={() => currentDomain = domainRequests[i]}>

                                    <td>
                                        {domain.domains.join(',')}
                                    </td>

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
                                                        {domain[col].username ?? 'Not Found'}
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
                                        {:else if col === 'verified_on_sedo_at'}
                                            <td>
                                                {#if domain[col] === null}
                                                    <span style="color: red;">
                                                        <i class="fa-sharp fa-solid fa-xmark"></i>
                                                    </span>
                                                {:else}
                                                    <span style="color: green;">
                                                        <i class="fa-solid fa-check"></i>
                                                        &nbsp;
                                                        {domain[col]}
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
                    No domainRequests Requests Yet
                </div>
            {/if}
        {:else}
            <div class="row mb-3">
                No SEDO Accounts
                <br>
                Add A SEDO Account to approve domainRequests
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
