<script>
    import { slide } from 'svelte/transition';
    import AlertBox from "../Alerts/AlertBox.svelte";
    import TogglableArrow from "../Widgets/ToggableArrow.svelte";
    import Switch from "../Checkboxes/Switch.svelte";
    import Spinner from "../Spinners/Spinner.svelte";
    import {registerDomain} from "../../PageFunctions/DomainRequests/registerDomain";
    import {updateCurrentDomain, verifyDomainOnSEDO} from "../../PageFunctions/DomainRequests/verifyDomainOnSEDO";
    import {addDomainToSEDO} from "../../PageFunctions/DomainRequests/addDomainToSEDO";
    import {deleteDomain} from "../../PageFunctions/DomainRequests/deleteDomain";
    import {deleteDomainsRequest} from "../../PageFunctions/DomainRequests/deleteDomainsRequest";
    import {promiseChainSequence} from "../../Helpers/promiseChainSequence";
    import SedoSelectAccount from "./SedoSelectAccount.svelte";

    export let domainRequests = []
    export let form = {}
    export let currentDomainRequest = {}
    export let currentSedoAccountId = null
    export let sedoAccounts = []
    export let sedoCategories = []
    export let sedoLanguages = []
    export let spinners = []

    let registeredCount = 0
    let domainsToRegisterCount = 0

    let registeredDomains = []


    let doCheckAll = true
    let domainsToProcess = []
    let checkedDomains = {}

    spinners['domainsSpinner'] = {}

    // Preprocess domains
    Object.entries(currentDomainRequest.domains).forEach(([index, domain]) => {
        const {domain: domainName, registered} = domain;

        domain.toggled = true
        domain.isRegisteredButNotOnSEDO = domain.sedo_account === null && domain.registered
        domain.isOnSEDOButNotVerified = domain.sedo_account !== null && domain.verified_on_sedo_at === null

        checkedDomains[domainName] = true
        domainsToProcess.push(domainName)

        if(registered) {
            registeredCount++;

            registeredDomains.push(domain)
        } else {
            domainsToRegisterCount++;
        }
    })

    console.log('Domains count: ', {registeredCount, registeredDomains, domainsToRegisterCount})

    console.log('Checked domains: ', {checkedDomains, domainsToProcess})

    let price = 0
    let minprice = 0
    let fixedprice = false
    let isForSale = false
    let enableWhoisProtection = false
    let sedoCategoryIds = [] // value is null
    let sedoLanguage = 'en'

    const checkAll = () => {
        domainsToProcess = []

        console.log('Object.entries(currentDomainRequest.domains): ', Object.entries(currentDomainRequest.domains))
        console.log('domainsToProcessAll: ', document)

        if(doCheckAll === true) {
            Object.entries(currentDomainRequest.domains)
                .filter(([index, domain]) => !domain.registered)
                .forEach(([index, domain]) => {
                    checkedDomains[domain.domain] = true

                    domainsToProcess.push(domain.domain)
                })
        } else {
            Object.entries(currentDomainRequest.domains)
                .filter(([index, domain]) => !domain.registered)
                .forEach(([index, domain]) => {
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
            )
                .then(data => {
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

    function pageAddDomainsToSEDO(sedoCategoryIds, sedoLanguage, isForSale, price, minprice, fixedprice, spinners, form, domainRequests) {
        spinners.addDomainToSEDO = true

        const domainsToSEDO = currentDomainRequest.domains.filter(domain => domain.registered === true)

        console.log('pageAddDomainsToSEDO() Processing: ', domainsToSEDO)

        if(domainsToSEDO.length <= 0) {
            form.success = false
            form.message = 'No Registered Domain To Add to SEDO'

            return
        }

        promiseChainSequence(domainsToSEDO, (domain, res) => {

            console.log('pageAddDomainsToSEDO(), promiseChainSequence: ', domain, res)

            spinners.addDomainToSEDO = true
            spinners.domainsSpinner[domain.domain] = true

            return addDomainToSEDO(
                domain,
                sedoCategoryIds,
                sedoLanguage,
                isForSale,
                price,
                minprice,
                fixedprice,
            )
                .then(data => {
                    console.log('addDomainToSEDO() Response data: ', data)

                    spinners.addDomainToSEDO = false
                    form.success = data.success
                    form.message = data.message
                    domainRequests = data.domainRequests

                    domain = updateCurrentDomain(domain, data.domainRequest)
                })
                .catch(err => {
                    spinners.addDomainToSEDO = false

                    form.success = false
                    form.message = err.response.data.message

                    console.log('Err: ', err.response.data)
                })
        })
    }

    function pageDeleteDomainsRequest() {
        spinners.deleteDomainsRequest = true

        deleteDomainsRequest(currentDomainRequest)
            .then(data => {
                console.log('deleteDomainsRequest() Response data: ', data)

                spinners.deleteDomainsRequest = false
                form.success = data.success
                form.message = data.message
                domainRequests = data.domainRequests

                spinners.deleteDomainsRequest = false

                currentDomainRequest = null


            })
            .catch(err => {
                spinners.deleteDomainsRequest = false

                form.success = false
                form.message = err.response.data.message

                console.log('Err: ', err.response.data)
            })
    }

    function pageDeleteSingleDomain(domain) {
        spinners.deleteDomainsRequest = true
        spinners.domainsSpinner[domain.domain] = true

        console.log('pageDeleteSingleDomain() deleting domain: ', domain.domain)

        deleteDomain(domain)
            .then(data => {
                console.log('pageDeleteSingleDomain() Response data: ', data)

                spinners.deleteDomainsRequest = false
                spinners.domainsSpinner[domain.domain] = false
                form.success = data.success
                form.message = data.message
                domainRequests = data.domainRequests

                domain = null
            })
            .catch(err => {
                spinners.deleteDomainsRequest = false
                spinners.domainsSpinner[domain.domain] = false

                form.success = false
                form.message = err.response.data.message

                console.log('Err: ', err.response.data)
            })
    }

</script>

<div class="row mb-3 p-4" transition:slide>
    {#if registeredCount > 0}
        <div class="row mb-3">
            <AlertBox type="warning">
                <strong>&#x26A0; Note:</strong> Adding domainRequest To SEDO does not have an immediate effect as domainRequest first have to pass a couple of checks before they get added to an account. You will be notified via eMail in case any checks fail.
                <br>
                This is how SEDO API works.
            </AlertBox>
        </div>
    {/if}

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
                                on:click={() => deleteDomain(domain)}
                                disabled={domain.registered || null}
                            >
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                        {#if domain.isRegisteredButNotOnSEDO}
                            <!-- Is Registered but not on SEDO -->
                            <td>
                                <TogglableArrow
                                    bind:toggled={domain.toggled}
                                />
                            </td>
                        {/if}
                    </tr>
                    {#if domain.isRegisteredButNotOnSEDO}
                        {#key domain.toggled}

                            <!-- Is Registered but not on SEDO -->
                            <tr transition:slide>

                                <td colspan="3">

                                    <SedoSelectAccount
                                        bind:domain={domain}
                                        bind:currentDomainRequest={currentDomainRequest}
                                        bind:currentSedoAccountId={currentSedoAccountId}
                                        bind:sedoAccounts={sedoAccounts}
                                        bind:sedoCategories={sedoCategories}
                                        bind:sedoLanguage={sedoLanguage}
                                        bind:sedoLanguages={sedoLanguages}
                                        bind:sedoCategoryIds={sedoCategoryIds}
                                        bind:isForSale={isForSale}
                                        bind:fixedprice={fixedprice}
                                        bind:minprice={minprice}
                                        bind:price={price}
                                    />

                                </td>

                            </tr>

                        {/key}

                    {:else if domain.isOnSEDOButNotVerified}
                        <!-- Is it is on SEDO but not verified -->
                        <tr transition:slide>

                            <td colspan="3">

                                <div class="row mb-3">
                                    <label for="sedo_account">SEDO Account</label>
                                    <input type="text" name="sedo_account" id="sedo_account"  class="form-control" value={domain.sedo_account.name} disabled>
                                </div>
                                <div class="row mb-3">
                                    <AlertBox type="warning">
                                        <strong>&#x26A0; Note:</strong> You can verify <b>{domain.domain}</b> on SEDO by adding a <b>DNS TXT Record</b> with this values:
                                        <br>
                                        Host: <b>blank</b> or <b>@</b>
                                        <br>
                                        Value: <b>{domain.sedo_account.domain_ownership_id}</b>
                                        <br>
                                        <br>
                                        This is described <a href="https://sedo.com/member/ownership_verification.php" target="_blank" rel="noreferrer">here</a>
                                    </AlertBox>
                                </div>

                            </td>

                        </tr>
                    {/if}
                <br>
            {/each}
            </tbody>
        </table>

    </div>

    <div class="row mb-3">
        <div class="col text-center">
            <button class="btn btn-primary btn-red" on:click={() => registerAllDomains()} disabled={registeredCount > 0 || null}>
                {#if spinners.registerDomain}
                    <i class="fa-solid fa-check"></i>
                    <Spinner />
                {:else}
                    <i class="fa-solid fa-check"></i>
                    Bulk Register
                {/if}
            </button>
        </div>

        <div class="col text-center">
            <button class="btn btn-primary btn-red"
                    on:click={
                        () => pageAddDomainsToSEDO(
                            sedoCategoryIds,
                            sedoLanguage,
                            isForSale,
                            price,
                            minprice,
                            fixedprice
                        )}
                    disabled={!(currentDomainRequest.sedo_account === null && registeredCount >= 1)}>
                {#if spinners.addDomainToSEDO}
                    <i class="fa-solid fa-plus"></i>
                    <Spinner />
                {:else}
                    <i class="fa-solid fa-plus"></i>
                    Bulk Add to SEDO
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
                        Bulk Verify Domain On SEDO
                    {/if}
                </button>
            </div>

        {/if}

        <div class="col text-center">
            <button class="btn btn-primary btn-red" on:click={() => pageDeleteDomainsRequest()} disabled={(currentDomainRequest.registered || null)}>
                {#if spinners.deleteDomainsRequest}
                    <i class="fa-solid fa-trash"></i>
                    <Spinner />
                {:else}
                    <i class="fa-solid fa-trash"></i>
                    Bulk Delete All
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
