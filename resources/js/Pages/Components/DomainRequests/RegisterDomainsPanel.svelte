<script>

import { slide } from 'svelte/transition';
import TogglableArrow from "../Widgets/ToggableArrow.svelte";
import SedoSelectAccount from "./SedoSelectAccount.svelte";
import AlertBox from "../Alerts/AlertBox.svelte";
import Spinner from "../Spinners/Spinner.svelte";
import Switch from "../Checkboxes/Switch.svelte";
import {promiseChainSequence} from "../../Helpers/promiseChainSequence";
import {registerDomain} from "../../PageFunctions/DomainRequests/registerDomain";
import {deleteDomainsRequest} from "../../PageFunctions/DomainRequests/deleteDomainsRequest";
import {deleteDomain} from "../../PageFunctions/DomainRequests/deleteDomain";
import {TABS_VALUES} from "../../Enums/tabsValue";

export let domainRequests = []
export let currentDomainRequest = null
export let spinners = {}

export let form = {}

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

    checkedDomains[domainName] = {
        checked: true,
        enableWhoisProtection: false,
        isForSale: false,
        registered,
        fixedprice: false,
    }

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
// let fixedprice = false
// let isForSale = false
let enableWhoisProtectionForAll = false
let sedoCategoryIds = [] // value is null
let sedoLanguage = 'en'

const doEnableWhoisProtectionForAll = () => {
    console.log('EnableWhoisProtectionForAll()')

    currentDomainRequest.domains.forEach(domain => {
        checkedDomains[domain.domain].enableWhoisProtection = enableWhoisProtectionForAll
    })

    console.log('EnableWhoisProtectionForAll()', checkedDomains)
}

const checkAll = () => {
    domainsToProcess = []

    console.log('Object.entries(currentDomainRequest.domains): ', Object.entries(currentDomainRequest.domains))
    console.log('domainsToProcessAll: ', document)

    if(doCheckAll === true) {
        Object.entries(currentDomainRequest.domains)
            .filter(([index, domain]) => !domain.registered)
            .forEach(([index, domain]) => {
                checkedDomains[domain.domain].checked = true

                domainsToProcess.push(domain.domain)
            })
    } else {
        Object.entries(currentDomainRequest.domains)
            .filter(([index, domain]) => !domain.registered)
            .forEach(([index, domain]) => {
                checkedDomains[domain.domain].checked = false
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
                enableWhoisProtection: checkedDomains[domainName].checked,
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
            checkedDomains[domainName].checked, // enableWhoisProtection
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

                currentDomainRequest = {
                    ...currentDomainRequest
                }
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

            if(data.isLastDomain === true) {
                console.log('isLastDomain: ', data.isLastDomain)

                currentDomainRequest = null
            } else {
                console.log('Resetting domains to: ', data.domainRequestDomains)

                currentDomainRequest = {
                    ...currentDomainRequest,
                    domains: [...data.domainRequestDomains]
                }
            }
        })
        .catch(err => {
            spinners.deleteDomainsRequest = false
            spinners.domainsSpinner[domain.domain] = false

            form.success = false
            form.message = err.response.data.message

            console.log('Err: ', err.response.data)
        })
}

// export let activeTabValue = 1
// if(registeredCount === currentDomainRequest.domains.length) {
//     activeTabValue = TABS_VALUES.ADD_TO_SEDO_TAB
// }

</script>

{#key currentDomainRequest}
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
                        disabled={registeredCount === currentDomainRequest.domains.length}
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
                            <input type="checkbox" name={`domainsToProcess[${i}]`} id={`domainsToProcess[${i}]`} disabled={domain.registered || null} bind:group={domainsToProcess} value={domain.domain} checked={domainsToProcess.includes(domain.domain)} on:change={() => console.log('Checkbox domain: ', {index, domain: domain.domain, domainsToProcess, checkedDomains})}>
                        {/if}
                    </td>

                    <td>
                        <input type="text" name={`domain[${i}]`} id={`domain[${i}]`} class="form-control" value={domain.domain} disabled>
                    </td>

                    <td>
                        <button
                            class="btn btn-primary btn-red"
                            on:click={() => pageDeleteSingleDomain(domain)}
                            disabled={domain.registered || null}
                        >
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>
                {#if !domain.registered}
                    <tr>
                        <td></td>
                        <td colspan="2" style="padding-left: 1rem;">
                            <Switch
                                id={`whois_protection_domain_${domain.id}`}
                                bind:checked={checkedDomains[domain.domain].enableWhoisProtection}
                                style="display: inline-block;"
                            >
                                Whois Protection
                            </Switch>
                        </td>
                    </tr>
                {/if}
                <br>
            {/each}
            </tbody>
        </table>

    </div>

    {#if !currentDomainRequest.registered && (registeredCount !== currentDomainRequest.domains.length)}
        <div class="row mb-3">
            <Switch id="whois_protection" onChange={doEnableWhoisProtectionForAll} bind:checked={enableWhoisProtectionForAll}>
                Whois Protection For All
            </Switch>
        </div>
    {/if}

    <div class="row mb-3">
        <div class="col text-center">
            <button
                class="btn btn-primary btn-red"
                on:click={() => registerAllDomains()}
                disabled={registeredCount > 0 || null}
            >
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
            <button
                class="btn btn-primary btn-red"
                on:click={() => pageDeleteDomainsRequest()}
                disabled={registeredCount > 0 || null}
            >
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

{/key}
