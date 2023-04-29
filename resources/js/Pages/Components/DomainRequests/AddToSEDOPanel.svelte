<script>

import SedoSelectAccount from "./SedoSelectAccount.svelte";
import AlertBox from "../Alerts/AlertBox.svelte";
import Spinner from "../Spinners/Spinner.svelte";
import {updateCurrentDomain} from "../../PageFunctions/DomainRequests/verifyDomainOnSEDO";
import {promiseChainSequence} from "../../Helpers/promiseChainSequence";
import {addDomainToSEDO} from "../../PageFunctions/DomainRequests/addDomainToSEDO";
import SedoSelectAccountRow from "./SedoSelectAccountRow.svelte";

export let domainRequests = []
export let currentDomainRequest = null
export let spinners = {}

export let domainsToProcess = []

export let currentSedoAccountId = null
export let sedoAccounts = []
export let sedoCategories = []
export let sedoLanguages = []

export let form = {}

let unprocessableCount = 0
let domainsToProcessCount = 0

let unprocessableDomains = []

let doCheckAll = true
let checkedDomains = {}

spinners['domainsSpinner'] = {}

// Preprocess domains
Object.entries(currentDomainRequest.domains).forEach(([index, domain]) => {
    const {domain: domainName, registered, price, sedo_account, verified_on_sedo_at} = domain;

    domain.toggled = true
    domain.isRegisteredButNotOnSEDO = domain.sedo_account === null && domain.registered
    domain.isOnSEDOButNotVerified = domain.sedo_account !== null && domain.verified_on_sedo_at === null

    checkedDomains[domainName] = {
        checked: true,
        enableWhoisProtection: false,
        registered,
        sedo_account,
        // SEDO Data
        isForSale: false,
        fixedprice: false,
        minprice: 0,
        price,
    }

    domainsToProcess.push(domainName)

    if(registered && sedo_account && !verified_on_sedo_at) {
        unprocessableCount++;

        unprocessableDomains.push(domain)
    } else {
        domainsToProcessCount++;
    }
})

console.log('Domains count: ', {unprocessableCount, unprocessableDomains, domainsToProcessCount})

console.log('Checked domains: ', {checkedDomains, domainsToProcess})

// let price = 0
// let minprice = 0
// let fixedprice = false
// let isForSale = false
// let enableWhoisProtectionForAll = false
let sedoCategoryIds = [] // value is null
let sedoLanguage = 'en'

const checkAll = () => {
    domainsToProcess = []

    console.log('Object.entries(currentDomainRequest.domains): ', Object.entries(currentDomainRequest.domains))
    console.log('domainsToProcessAll: ', document)

    Object.entries(currentDomainRequest.domains)
        .filter(([index, domain]) => !domain.sedo_account)
        .forEach(([index, domain]) => {
            checkedDomains[domain.domain].checked = doCheckAll

            if(doCheckAll === true) {
                domainsToProcess.push(domain.domain)
            }
        })

    console.log('domainsToProcess: ', domainsToProcess)
    console.log('checkedDomains: ', checkedDomains)
}

console.log('currentDomainRequest: ', currentDomainRequest)
console.log('domainsToProcess: ', domainsToProcess)

function pageAddDomainsToSEDO() {
    spinners.addDomainToSEDO = true

    const domainsToSEDO = currentDomainRequest.domains.filter(domain => domainsToProcess.includes(domain.domain))

    console.log('pageAddDomainsToSEDO() Processing: ', domainsToSEDO)

    if(domainsToSEDO.length <= 0) {
        form.success = false
        form.message = 'No Registered Domain To Add to SEDO'

        spinners.addDomainToSEDO = false

        return
    }

    promiseChainSequence(domainsToSEDO, (domain, res) => {

        console.log('pageAddDomainsToSEDO(), promiseChainSequence: ', domain, res)

        spinners.addDomainToSEDO = true
        spinners.domainsSpinner[domain.domain] = true

        return addDomainToSEDO(
            domain,
            currentSedoAccountId,
            sedoCategoryIds,
            sedoLanguage,
            checkedDomains[domain.domain].isForSale,
            checkedDomains[domain.domain].price,
            checkedDomains[domain.domain].minprice,
            checkedDomains[domain.domain].fixedprice,
        )
            .then(data => {
                console.log('addDomainToSEDO() Response data: ', data)

                spinners.addDomainToSEDO = false
                spinners.domainsSpinner[domain.domain] = false

                form.success = data.success
                form.message += data.message + `\n<br>`
                domainRequests = data.domainRequests

                domain = updateCurrentDomain(domain, data.domainRequests)
            })
            .catch(err => {
                spinners.addDomainToSEDO = false
                spinners.domainsSpinner[domain.domain] = false

                form.success = false
                form.message = err.response.data.message

                console.log('Err: ', err.response.data)
            })
    }).then(() => {
        currentDomainRequest = {
            ...currentDomainRequest
        }
    })
}

</script>

{#key currentDomainRequest}
    {#if unprocessableCount > 0}
        <div class="row mb-3">
            <AlertBox type="warning">
                <strong>&#x26A0; Note:</strong> Adding domain To SEDO does not have an immediate effect as domain first have to pass a couple of checks before they get added to an account. You will be notified via eMail in case any checks fail.
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
                    <input
                        type="checkbox" name={`domainsToProcessAll`}
                        id={`domainsToProcessAll`}
                        bind:checked={doCheckAll}
                        on:change={checkAll}
                        disabled={unprocessableCount === currentDomainRequest.domains.length}
                    >
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
                            <input
                                type="checkbox" name={`domainsToProcess[${i}]`}
                                id={`domainsToProcess[${i}]`}
                                disabled={domain.sedo_account || null}
                                bind:group={domainsToProcess}
                                value={domain.domain}
                                checked={domainsToProcess.includes(domain.domain)}
                                on:change={() => console.log('Checkbox domain: ', {index, domain: domain.domain, domainsToProcess, checkedDomains})}
                            >
                        {/if}
                    </td>

                    <td>
                        <input type="text" name={`domain[${i}]`} id={`domain[${i}]`} class="form-control" value={domain.domain} disabled>
                    </td>

                    <!--{#if domain.isRegisteredButNotOnSEDO}-->
                    <!--    &lt;!&ndash; Is Registered but not on SEDO &ndash;&gt;-->
                    <!--    <td>-->
                    <!--        <TogglableArrow-->
                    <!--            bind:toggled={domain.toggled}-->
                    <!--        />-->
                    <!--    </td>-->
                    <!--{/if}-->
                </tr>
                {#if domain.isRegisteredButNotOnSEDO}
                    <tr>
                        <td colspan="2">
                            <SedoSelectAccountRow
                                bind:sedoCategories={sedoCategories}
                                bind:sedoLanguage={sedoLanguage}
                                bind:sedoLanguages={sedoLanguages}
                                bind:sedoCategoryIds={sedoCategoryIds}
                                bind:isForSale={checkedDomains[domain.domain].isForSale}
                                bind:fixedprice={checkedDomains[domain.domain].fixedprice}
                                bind:minprice={checkedDomains[domain.domain].minprice}
                                bind:price={checkedDomains[domain.domain].price}
                            />
                        </td>
                    </tr>
                {/if}


                <br>
            {/each}
            </tbody>
        </table>


        <div class="row mb-3">

            <SedoSelectAccount
                bind:currentDomainRequest={currentDomainRequest}
                bind:currentSedoAccountId={currentSedoAccountId}
                bind:sedoAccounts={sedoAccounts}
                bind:sedoCategories={sedoCategories}
                bind:sedoLanguage={sedoLanguage}
                bind:sedoLanguages={sedoLanguages}
                bind:sedoCategoryIds={sedoCategoryIds}
            />

        </div>

    </div>

    <div class="row text-center">
        <button class="btn btn-primary btn-red"
                on:click={() => pageAddDomainsToSEDO()}
                disabled={unprocessableCount === currentDomainRequest.domains.length || null}>
            {#if spinners.addDomainToSEDO}
                <i class="fa-solid fa-plus"></i>
                <Spinner />
            {:else}
                <i class="fa-solid fa-plus"></i>
                Bulk Add to SEDO
            {/if}
        </button>
    </div>
{/key}
