<script>

import { slide } from 'svelte/transition';
import TogglableArrow from "../Widgets/ToggableArrow.svelte";
import SedoSelectAccount from "./SedoSelectAccount.svelte";
import AlertBox from "../Alerts/AlertBox.svelte";
import Spinner from "../Spinners/Spinner.svelte";
import Switch from "../Checkboxes/Switch.svelte";
import {updateCurrentDomain, verifyDomainOnSEDO} from "../../PageFunctions/DomainRequests/verifyDomainOnSEDO";
import {promiseChainSequence} from "../../Helpers/promiseChainSequence";
import {addDomainToSEDO} from "../../PageFunctions/DomainRequests/addDomainToSEDO";

export let domainRequests = []
export let currentDomainRequest = null
export let spinners = {}

export let currentSedoAccountId = null
export let sedoAccounts = []
export let sedoCategories = []
export let sedoLanguages = []

// export let domainsToProcess = []


export let form = {}

let verifiedCount = 0
let sedoAccountCount = 0
let domainsToProcessCount = 0

let verifiedDomains = []

let doCheckAll = true
let domainsToProcess = []
let checkedDomains = {}

spinners['domainsSpinner'] = {}

// Preprocess domains
Object.entries(currentDomainRequest.domains).forEach(([index, domain]) => {
    const {domain: domainName, registered, sedo_account, verified_on_sedo_at} = domain;

    domain.toggled = true
    domain.isRegisteredButNotOnSEDO = domain.sedo_account === null && domain.registered
    domain.isOnSEDOButNotVerified = domain.sedo_account !== null && domain.verified_on_sedo_at === null

    checkedDomains[domainName] = {
        checked: true,
        enableWhoisProtection: false,
        isForSale: false,
        registered,
        verified_on_sedo_at,
        fixedprice: false,
    }

    if(!sedo_account) {
        sedoAccountCount++
    }

    domainsToProcess.push(domainName)

    if(verified_on_sedo_at) {
        verifiedCount++;

        verifiedDomains.push(domain)
    } else {
        domainsToProcessCount++;
    }
})

console.log('Domains count: ', {verifiedCount, verifiedDomains, domainsToProcessCount})

console.log('Checked domains: ', {checkedDomains, domainsToProcess})

let price = 0
let minprice = 0
// let fixedprice = false
// let isForSale = false
// let enableWhoisProtectionForAll = false
let sedoCategoryIds = [] // value is null
let sedoLanguage = 'en'

const checkAll = () => {
    domainsToProcess = []

    console.log('Object.entries(currentDomainRequest.domains): ', Object.entries(currentDomainRequest.domains))
    console.log('domainsToProcessAll: ', document)

    if(doCheckAll === true) {
        Object.entries(currentDomainRequest.domains)
            .filter(([index, domain]) => !domain.verified_on_sedo_at)
            .forEach(([index, domain]) => {
                checkedDomains[domain.domain].checked = true

                domainsToProcess.push(domain.domain)
            })
    } else {
        Object.entries(currentDomainRequest.domains)
            .filter(([index, domain]) => !domain.verified_on_sedo_at)
            .forEach(([index, domain]) => {
                checkedDomains[domain.domain].checked = false
            })
    }

    console.log('domainsToProcess: ', domainsToProcess)
    console.log('checkedDomains: ', checkedDomains)
}

console.log('currentDomainRequest: ', currentDomainRequest)
console.log('domainsToProcess: ', domainsToProcess)

function pageAddDomainsToSEDO(
    sedoCategoryIds,
    sedoLanguage,
    isForSale,
    price,
    minprice,
    fixedprice
) {
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
        )
            .then(data => {
                console.log('addDomainToSEDO() Response data: ', data)

                spinners.addDomainToSEDO = false
                spinners.domainsSpinner[domain.domain] = false

                form.success = data.success
                form.message = data.message
                domainRequests = data.domainRequests

                domain = updateCurrentDomain(domain, data.domainRequest)
            })
            .catch(err => {
                spinners.addDomainToSEDO = false
                spinners.domainsSpinner[domain.domain] = false

                form.success = false
                form.message = err.response.data.message

                console.log('Err: ', err.response.data)
            })
    })
}

</script>

<div class="row mb-3 fw-bold">
    <table>
        <thead>
        <tr>
            <th>
                <input type="checkbox" name={`domainsToProcessAll`} id={`domainsToProcessAll`} bind:checked={doCheckAll} on:change={checkAll} disabled={(verifiedCount === currentDomainRequest.domains.length) || sedoAccountCount === 0}}>
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
                        <input type="checkbox" name={`domainsToProcess[${i}]`} id={`domainsToProcess[${i}]`} disabled={(domain.sedo_account && domain.verified_on_sedo_at)|| null} bind:group={domainsToProcess} value={domain.domain} checked={domainsToProcess.includes(domain.domain)} on:change={() => console.log('Checkbox domain: ', {index, domain: domain.domain, domainsToProcess, checkedDomains})}>
                    {/if}
                </td>

                <td>
                    <input type="text" name={`domain[${i}]`} id={`domain[${i}]`} class="form-control" value={domain.domain} disabled>
                </td>

            </tr>

            {#if domain.isOnSEDOButNotVerified}
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
    <div class="col text-center" transition:slide>
        <button class="btn btn-primary btn-red" on:click={() => verifyDomainOnSEDO(currentDomainRequest, spinners, form, domainRequests)}
                disabled={(verifiedCount === currentDomainRequest.domains.length) || sedoAccountCount === 0}>
            {#if spinners.verifyDomainOnSEDO}
                <i class="fa-solid fa-certificate"></i>
                <Spinner />
            {:else}
                <i class="fa-solid fa-certificate"></i>
                Bulk Verify Domain On SEDO
            {/if}
        </button>
    </div>
</div>
