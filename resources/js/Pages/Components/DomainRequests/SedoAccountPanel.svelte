<script>
    import { slide } from 'svelte/transition';
    import AlertBox from "../Alerts/AlertBox.svelte";
    import Switch from "../Checkboxes/Switch.svelte";
    import Spinner from "../Spinners/Spinner.svelte";
    import {registerDomain} from "../../PageFunctions/DomainRequests/registerDomain";
    import {verifyDomainOnSEDO} from "../../PageFunctions/DomainRequests/verifyDomainOnSEDO";
    import {addDomainToSEDO} from "../../PageFunctions/DomainRequests/addDomainToSEDO";
    import {deleteDomain} from "../../PageFunctions/DomainRequests/deleteDomain";
    import {intervalLoop} from "../../Helpers/intervalLoop";
    import {deleteDomainsRequest} from "../../PageFunctions/DomainRequests/deleteDomainsRequest";
    import {promiseChainSequence} from "../../Helpers/promiseChainSequence";

    export let domainRequests = {}
    export let form = {}
    export let currentDomainRequest = {}
    export let currentSedoAccountId = null
    export let sedoAccounts = []
    export let sedoCategories = []
    export let sedoLanguages = []
    export let spinners = []

    let doCheckAll = true
    let domainsToProcess = []
    let checkedDomains = {}

    spinners['domainsSpinner'] = {}

    Object.entries(currentDomainRequest.domains).forEach(([index, {domain}]) => {
        checkedDomains[domain] = true
        domainsToProcess.push(domain)
    })

    let price = 0
    let minprice = 0
    let fixedprice = false
    let isForSale = false
    let enableWhoisProtection = false
    let sedoCategoryIds = null
    let sedoLanguage = 'en'

    const checkAll = () => {
        domainsToProcess = []

        console.log('Object.entries(currentDomainRequest.domains): ', Object.entries(currentDomainRequest.domains))
        console.log('domainsToProcessAll: ', document)

        if(doCheckAll === true) {
            Object.entries(currentDomainRequest.domains).forEach(([index, domain]) => {
                checkedDomains[domain.domain] = true

                domainsToProcess.push(domain.domain)
            })
        } else {
            Object.entries(currentDomainRequest.domains).forEach(([index, domain]) => {
                checkedDomains[domain.domain] = false
            })
        }

        console.log('domainsToProcess: ', domainsToProcess)
        console.log('checkedDomains: ', checkedDomains)
    }

    console.log('currentDomainRequest: ', currentDomainRequest)
    console.log('domainsToProcess: ', domainsToProcess)

    function registerAllDomains() {
        // currentDomainRequest, enableWhoisProtection, spinners, form, domainRequests

        console.log('Going to register: ', domainsToProcess)

        if (domainsToProcess.length <= 0) {
            form.success = false
            form.message = 'No Domain Selected for Registration'

            return
        }

        form.success = null
        form.message = ''

        promiseChainSequence(domainsToProcess, (domainName, res) => {

            console.log('promiseChainSequence: ', domainName, res)

            const domain = currentDomainRequest.domains.filter(domain => domain.domain === domainName)[0]

            console.log('Registering domain: ',
                domainName,
                {
                    domain,
                    currentDomainRequest,
                    enableWhoisProtection,
                    spinners,
                    form,
                    domainRequests
                }
            )

            spinners.registerDomain = true
            spinners.domainsSpinner[domain.domain] = true

            return registerDomain (
                domain,
                currentDomainRequest,
                enableWhoisProtection,
                spinners,
                form,
                domainRequests
            ).then(data => {
                console.log('registerDomain() Response data: ', data)

                spinners.domainsSpinner[domain.domain] = false
                form.success = data.success
                form.message += data.message + `\n<br>`
                domainRequests = data.domainRequests

                domain.registered = data.success

                spinners.registerDomain = false
            })
            .catch(err => {
                spinners.domainsSpinner[domain.domain] = false

                form.success = false
                form.message += err.response.data.message + `\n<br>`

                console.log('Err: ', err.response.data)

                spinners.registerDomain = false
            })
        })

    }

    function pageDeleteDomainRequest(currentDomainRequest) {
        spinners.deleteDomainsRequest = true

        deleteDomainsRequest(currentDomainRequest)
            .then(data => {
                console.log('deleteDomainsRequest() Response data: ', data)

                spinners.deleteDomainsRequest = false
                form.success = data.success
                form.message = data.message
                domainRequests = data.domainRequests
                currentDomainRequest = null

                spinners.deleteDomainsRequest = false
        })
            .catch(err => {
                spinners.deleteDomainsRequest = false

                form.success = false
                form.message = err.response.data.message

                console.log('Err: ', err.response.data)

                spinners.deleteDomainsRequest = false
            })
    }
</script>

<div class="row mb-3 p-4" transition:slide>
    <div class="row mb-3 fw-bold">
        <table>
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" name={`domainsToProcessAll`} id={`domainsToProcessAll`} bind:checked={doCheckAll} on:change={checkAll} >
                    </th>
                    <th>Domain</th>
                </tr>
            </thead>

            <tbody>
                {#each Object.entries(currentDomainRequest.domains) as [index, domain], i}
                    <tr>
                        <td>
                            {#if spinners.domainsSpinner[domain.domain]}
                                <Spinner />
                            {:else}
                                <input type="checkbox" name={`domainsToProcess[${i}]`} id={`domainsToProcess[${i}]`} disabled={domain.registered || null} bind:group={domainsToProcess} value={domain.domain} checked={domainsToProcess.includes(domain.domain)} on:change={() => console.log('Checkbox domain: ', {index, domain: domain.domain, domainsToProcess, checkedDomains})}>
                            {/if}
                        </td>

                        <td>
                            <input type="text" name={`domain[${i}]`} id={`domain[${i}]`} class="form-control" value={domain.domain} disabled>
                        </td>

                        <td>
                            <button
                                class="btn btn-primary btn-red"
                                on:click={() => console.log('[NOT IMPLEMENTED] Deleting domain: ', domain.domain)}
                                disabled={domain.registered || null}
                            >
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <br>
            {/each}
            </tbody>
        </table>

    </div>

    <!-- Is it is on SEDO but not verified -->
    {#if currentDomainRequest.sedo_account !== null && currentDomainRequest.verified_on_sedo_at === null}
        <div class="row mb-3">
            <label for="sedo_account">SEDO Account</label>
            <input type="text" name="sedo_account" id="sedo_account"  class="form-control" value={currentDomainRequest.sedo_account.name} disabled>
        </div>
        <div class="row mb-3">
            <AlertBox type="warning">
                <strong>&#x26A0; Note:</strong> You can verify <b>{currentDomainRequest.domain}</b> on SEDO by adding a <b>DNS TXT Record</b> with this values:
                <br>
                Host: <b>blank</b> or <b>@</b>
                <br>
                Value: <b>{currentDomainRequest.sedo_account.domain_ownership_id}</b>
                <br>
                <br>
                This is described <a href="https://sedo.com/member/ownership_verification.php" target="_blank" rel="noreferrer">here</a>
            </AlertBox>
        </div>
    {/if}

    <!-- Is Registered but not on SEDO -->
    {#if currentDomainRequest.sedo_account === null && currentDomainRequest.registered}

        <div class="row mb-3">
            <AlertBox type="warning">
                <strong>&#x26A0; Note:</strong> Adding domainRequest To SEDO does not have an immediate effect as domainRequest first have to pass a couple of checks before they get added to an account. You will be notified via eMail in case any checks fail.
                <br>
                This is how SEDO API works.
            </AlertBox>
        </div>

        <div class="row mb-3">
            <label for={`sedo_account`}>
                Select SEDO Account
            </label>
            <select name={`sedo_account`} id={`sedo_account`} bind:value={currentSedoAccountId} class="form-select" disabled={currentDomainRequest.sedo_account || null}>
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
            <button class="btn btn-primary btn-red" on:click={() => registerAllDomains()} disabled={currentDomainRequest.registered || null}>
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
            <button class="btn btn-primary btn-red" on:click={() => addDomainToSEDO(currentDomainRequest, sedoCategoryIds,
    sedoLanguage, isForSale, price, minprice, fixedprice, spinners, form, domainRequests)} disabled={!(currentDomainRequest.sedo_account === null && currentDomainRequest.registered)}>
                {#if spinners.addDomainToSEDO}
                    <i class="fa-solid fa-plus"></i>
                    <Spinner />
                {:else}
                    <i class="fa-solid fa-plus"></i>
                    Add to SEDO
                {/if}
            </button>
        </div>

        <!-- Is Registered and on SEDO but not verified -->
        {#if currentDomainRequest.registered && currentDomainRequest.sedo_account !== null}

            <div class="col text-center" transition:slide>
                <button class="btn btn-primary btn-red" on:click={() => verifyDomainOnSEDO(currentDomainRequest, spinners, form, domainRequests)}
                        disabled={currentDomainRequest.verified_on_sedo_at || null}>
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
            <button class="btn btn-primary btn-red" on:click={() => pageDeleteDomainRequest(currentDomainRequest)} disabled={(currentDomainRequest.registered || null)}>
                {#if spinners.deleteDomainsRequest}
                    <i class="fa-solid fa-trash"></i>
                    <Spinner />
                {:else}
                    <i class="fa-solid fa-trash"></i>
                    Delete All
                {/if}
            </button>
        </div>
    </div>

    {#if !currentDomainRequest.registered}
        <div class="row mb-3">
            <Switch id="whois_protection" bind:checked={enableWhoisProtection}>
                Whois Protection
            </Switch>
        </div>
    {/if}

</div>
