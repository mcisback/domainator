<script>
    import DashboardLayout from "../Layouts/DashboardLayout.svelte";
    import route from "ziggy-js";
    import Spinner from "../Components/Spinners/Spinner.svelte";

    import { slide } from 'svelte/transition';
    import AlertBox from "../Components/Alerts/AlertBox.svelte";
    import {intervalLoop} from "../Helpers/intervalLoop";

    export let allowedTdls = []

    let isAvailable = null
    let domainsText = null
    let domains = []
    let checkedDomains = {}
    let availableDomains = {}
    let domainsToRegister = []
    let domainsToRegisterAll = false
    let nAvailableDomains = 0
    let canSubmit = true

    const initFormData = () => {
        return {
            domain: '',
        }
    }

    let formData = initFormData()

    let formMessage = null
    let formSuccess = false

    const requestDomainRegistration = (domain) => {
        if(checkedDomains[domain].isAvailable === false) {
            formSuccess = false
            formMessage = "Domain is not available for registration"

            return
        }

        checkedDomains[domain].spinner = true
        checkedDomains[domain].message = 'Requesting ...'

        console.log('requestDomainRegistration() Requesting: ', domain)

        axios.post(route('dashboard.domains.store'), {
            domain
        })
        .then(res => res.data)
        .then(data => {
            console.log('Response data: ', data)

            // formSuccess = data.success

            checkedDomains[domain].spinner = false
            checkedDomains[domain].requested = true
            checkedDomains[domain].message = data.message
            checkedDomains[domain].isAvailable = true
        })
        .catch(err => {
            checkedDomains[domain].spinner = false

            formSuccess = false
            checkedDomains[domain].message =  err.response.data.message

            console.log('Err: ', err.response.data)
        })
    }

    const checkDomainAvailability = (domain) => {

        // availableDomains = {}

        checkedDomains[domain] = {
            domain,
            isAvailable: false,
            requested: false,
            spinner: true,
            message: 'Checking...',
        }

        console.log('checkDomainAvailability() Checking: ', domain)

        axios.post(route('api.index', {
            action: 'checkDomain',
            domain
        }))
        .then(res => res.data)
        .then(data => {
            console.log('Response data: ', data)
            console.log('Checked: ', checkedDomains[domain])

            // formSuccess = data.success

            checkedDomains[domain].message =  data.message
            checkedDomains[domain].isAvailable = data.isAvailable
            checkedDomains[domain].price = data.price
            checkedDomains[domain].spinner = false

            if(data.isAvailable === true) {
                nAvailableDomains++

                availableDomains[domain] = checkedDomains[domain]

                // domainsToRegister.push(domain)
            }

        })
        .catch(err => {
            checkedDomains[domain].spinner = false

            // formSuccess = false
            checkedDomains[domain].message =  err.response.data.message

            console.log('Err: ', err.response.data)
        })
    }

    const onSubmit = () => {
        const intervalTime = 1000

        canSubmit = true

        const domainsToCheck = domainsText.split("\n")
        const domains = []

        for(let i = 0; i < domainsToCheck.length; i++) {
            const domain = domainsToCheck[i]
            const isDomain = domain.match(/(\.[\w\d]+){1,2}$/gi)

            console.log(`Checking ${domain}: `, isDomain)

            if(!!isDomain) { // Is A Domain
                console.log(`${domain} is a domain`)

                const tdl = isDomain[0]

                if(!allowedTdls.includes(tdl)) {
                    formMessage = `${domain} is not allowed`
                    formSuccess = false
                    canSubmit = false

                    console.log(formMessage)

                    break
                } else {
                    domains.push(domain)
                }
            } else { // Is String
                console.log(`${domain} is a string`)

                allowedTdls.forEach(tdl => {
                    domains.push(domain + tdl)
                })
            }
        }

        console.log('canSubmit: ', canSubmit)
        console.log('Domains Text: ', domainsText)
        console.log('Checking domains: ', domains)

        console.log('domainsToRegister: ', domainsToRegister)
        console.log('nAvailableDomains: ', nAvailableDomains)
        console.log('availableDomains: ', availableDomains)
        console.log('domainsToRegisterAll: ', domainsToRegisterAll)

        if(canSubmit) {
            formSuccess = null

            if (nAvailableDomains === 0) {
                intervalLoop(domains, checkDomainAvailability, intervalTime)
            } else {
                intervalLoop(domainsToRegister, requestDomainRegistration, intervalTime)
            }
        }
    }

    const checkAll = () => {
        domainsToRegister = []

        if(domainsToRegisterAll === true) {
            Object.entries(availableDomains).forEach(([domain, domainData]) => {
                domainsToRegister.push(domain)
            })
        }
    }

    const clear = () => {
        isAvailable = null
        domainsText = null
        domains = []
        checkedDomains = {}
        availableDomains = {}
        domainsToRegister = []
        domainsToRegisterAll = false
        nAvailableDomains = 0

        formData = initFormData()

        formMessage = null
        formSuccess = false
    }

</script>

<DashboardLayout>
    <div class="container mx-auto p-1 pt-4" style="max-width: 50%; ">
        <h1 class="text-center">Domain Request</h1>

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

        <div class="row mb-5 mx-auto">
            <form on:submit|preventDefault={onSubmit}>
                <div class="row">
                    <label for="domains">
                        Domains (one per line
                        <br>
                        Allowed TDLs: {allowedTdls.join(', ')}
                    </label>

                    <div class="input-group mb-3">
                        <textarea name="domains" id="domains" class="form-control" bind:value={domainsText} cols="30" rows="10" required disabled={nAvailableDomains > 0}></textarea>
                    </div>
                </div>

                <div class="row mb-0">
                    <div class="col d-flex justify-content-between">
                        {#if nAvailableDomains > 0}
                            <button type="submit" class="btn btn-primary btn-red w-75" disabled={domainsToRegister.length === 0}>
                                Request {domainsToRegister.length || ''} Domains Registration
                            </button>

                            <button type="button" class="btn btn-light" on:click|preventDefault={clear}>
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        {:else}
                            <button type="submit" class="btn btn-primary btn-red w-100">
                                Check Domains Availability
                            </button>
                        {/if}
                    </div>
                </div>

            </form>
        </div>

        <div class="row mb-2">
            <h1>Domains Availability</h1>
        </div>

        <div class="row mb-3" transition:slide>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">
                            <input type="checkbox" name="domainsToRegisterAll" bind:checked={domainsToRegisterAll} id="domainsToRegisterAll" on:change={checkAll}>
                        </th>
                        <th>
                            Available
                        </th>

                        <th>
                            Requested
                        </th>

                        <th>
                            Domain
                        </th>

                        <th>
                            Price
                        </th>

                        <th>
                            Message
                        </th>
                    </tr>
                </thead>

                <tbody>
                    {#each Object.entries(checkedDomains) as [domain, {isAvailable, isPremium, requested, spinner, message, price}], i}
                        <tr>
                            <td>
                                <input type="checkbox" name={`domainsToRegister${i}`} id={`domainsToRegister${i}`} disabled={!isAvailable && !isPremium} bind:group={domainsToRegister} value={domain} checked={domainsToRegister.includes(domain)}>
                            </td>
                            <td>
                                {#if spinner}
                                    <Spinner />
                                {:else}
                                    {#if isAvailable === null}
                                        <span>
                                            <i class="fa-solid fa-globe"></i>
                                        </span>
                                    {:else if isAvailable === true}
                                        <span style="color: green;">
                                            <i class="fa-solid fa-check"></i>
                                        </span>
                                    {:else if isAvailable === false}
                                        <span style="color: red;">
                                            <i class="fa-sharp fa-solid fa-xmark"></i>
                                        </span>
                                    {/if}
                                {/if}
                            </td>

                            <td>
                                {#if spinner}
                                    <Spinner />
                                {:else}
                                    {#if requested === null}
                                        <span>
                                            <i class="fa-solid fa-globe"></i>
                                        </span>
                                    {:else if requested === true}
                                        <span style="color: green;">
                                            <i class="fa-solid fa-check"></i>
                                        </span>
                                    {:else if requested === false}
                                        <span style="color: red;">
                                            <i class="fa-sharp fa-solid fa-xmark"></i>
                                        </span>
                                    {/if}
                                {/if}
                            </td>

                            <td>
                                {domain}
                            </td>

                            <td>
                                {price}
                            </td>

                            <td>
                                {message}
                            </td>
                        </tr>
                    {:else}
                        <tr>
                            <td colspan={6}>
                                No Domains Yet
                            </td>
                        </tr>
                    {/each}
                </tbody>

            </table>

        </div>
    </div>
</DashboardLayout>

<style>
    /*[id^="paragraph"] {*/
    /*    border: thin solid black;*/
    /*}*/
</style>

