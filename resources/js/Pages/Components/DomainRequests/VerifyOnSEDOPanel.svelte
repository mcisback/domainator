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

    if(!!sedo_account) {
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

    console.log('Filtered Object.entries(currentDomainRequest.domains): ', Object.entries(currentDomainRequest.domains)
        .filter(([index, domain]) => !domain.verified_on_sedo_at))

    Object.entries(currentDomainRequest.domains)
        .filter(([index, domain]) => domain.verified_on_sedo_at === null)
        .forEach(([index, domain]) => {
            checkedDomains[domain.domain].checked = doCheckAll

            if(doCheckAll === true) {
                domainsToProcess.push(domain.domain)
            }
        })

    console.log('domainsToProcess: ', domainsToProcess)
    console.log('checkedDomains: ', checkedDomains)
}

checkAll()

console.log('currentDomainRequest: ', currentDomainRequest)
console.log('domainsToProcess: ', domainsToProcess)

function pageVerifyDomainsOnSEDO() {
    spinners.verifyDomainsOnSEDO = true

    const domainsToSEDO = currentDomainRequest.domains.filter(domain => domainsToProcess.includes(domain.domain))

    console.log('pageVerifyDomainsOnSEDO() Processing: ', domainsToSEDO)

    if(domainsToSEDO.length <= 0) {
        form.success = false
        form.message = 'No Registered Domain To Add to SEDO'

        return
    }

    promiseChainSequence(domainsToSEDO, (domain, res) => {

        console.log('pageVerifyDomainsOnSEDO(), promiseChainSequence: ', domain, res)

        spinners.verifyDomainsOnSEDO = true
        spinners.domainsSpinner[domain.domain] = true

        return verifyDomainOnSEDO(
            domain,
        )
            .then(data => {
                console.log('verifyDomainOnSEDO() Response data: ', data)

                spinners.verifyDomainsOnSEDO = false
                spinners.domainsSpinner[domain.domain] = false

                form.success = data.success
                form.message = data.message
                domainRequests = data.domainRequests

                domain = updateCurrentDomain(domain, data.domainRequest)

                currentDomainRequest = {
                    ...currentDomainRequest
                }
            })
            .catch(err => {
                spinners.verifyDomainsOnSEDO = false
                spinners.domainsSpinner[domain.domain] = false

                form.success = false
                form.message = err.response.data.message

                console.log('Err: ', err.response.data)
            })
    })
}

</script>

{#if currentDomainRequest.sedo_account}
    <div class="row mb-3">
        <label for="sedo_account">SEDO Account</label>
        <input type="text" name="sedo_account" id="sedo_account"  class="form-control" value={currentDomainRequest.sedo_account.name} disabled>
    </div>
    <div class="row mb-3">
        <AlertBox type="warning">
            <strong>&#x26A0; Note:</strong> You can MANUALLY verify a domain on SEDO by adding a <b>DNS TXT Record</b> with this values:
            <br>
            Host: <b>blank</b> or <b>@</b>
            <br>
            Value: <b>{currentDomainRequest.sedo_account.domain_ownership_id}</b>
            <br>
            <br>
            This is described <a href="https://sedo.com/member/ownership_verification.php" target="_blank" rel="noreferrer">here</a>
            <br>
            <br>
            By Clicking "Bulk Verify Domain On SEDO", it will be set automatic
            on all checked domains a TXT DNS Record with correct values using Namecheap API.
            <br>
            If the domain doesn't use Namecheap DNS, it will not work.
            <br>
            You might still need to verify it manually or double check it.
        </AlertBox>
    </div>

    <div class="row mb-3 fw-bold">
        <table>
            <thead>
            <tr>
                <th>
                    <input
                        type="checkbox"
                        name={`domainsToProcessAll`}
                        id={`domainsToProcessAll`}
                        bind:checked={doCheckAll}
                        on:change={checkAll}
                        disabled={(verifiedCount === currentDomainRequest.domains.length)}
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
                                disabled={domain.verified_on_sedo_at || null}
                                bind:group={domainsToProcess}
                                value={domain.domain}
                                checked={domainsToProcess.includes(domain.domain)}
                                on:change={() => console.log('Checkbox domain: ', {index, domain: domain.domain, domainsToProcess, checkedDomains})}>
                        {/if}
                    </td>

                    <td>
                        <input type="text" name={`domain[${i}]`} id={`domain[${i}]`} class="form-control" value={domain.domain} disabled>
                    </td>

                </tr>
                <br>
            {/each}
            </tbody>
        </table>

    </div>

    <div class="row mb-3">
        <div class="col text-center" transition:slide>
            <button class="btn btn-primary btn-red" on:click={() => pageVerifyDomainsOnSEDO()}
                    disabled={(verifiedCount === currentDomainRequest.domains.length)}>
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
{:else}
    You Need At Least 1 Domain Added To SEDO to Enable SEDO Verify
{/if}
