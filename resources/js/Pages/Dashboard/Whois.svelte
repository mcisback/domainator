<script>
    import DashboardLayout from "../Layouts/DashboardLayout.svelte";
    import route from "ziggy-js";
    import Spinner from "../Components/Spinners/Spinner.svelte";

    import { slide } from 'svelte/transition';
    import AlertBox from "../Components/Alerts/AlertBox.svelte";
    import {intervalLoop} from "../Helpers/intervalLoop";

    const spinners = {
        checkDomain: false
    }

    let isAvailable = null
    let domainsText = null
    let domains = []
    let checkedDomains = {}
    let availableDomains = {}
    let nAvailableDomains = 0

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

            if(data.isAvailable === true) {
                nAvailableDomains++
            }

            checkedDomains[domain].message =  data.message
            checkedDomains[domain].isAvailable = data.isAvailable
            checkedDomains[domain].spinner = false
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

        domains = domainsText.split("\n")

        console.log('Domains Text: ', domainsText)
        console.log('Checking domains: ', domains)

        if(nAvailableDomains === 0) {
            intervalLoop(domains, checkDomainAvailability, intervalTime)
        } else {
            intervalLoop(domains, requestDomainRegistration, intervalTime)
        }
    }

</script>

<DashboardLayout>
    <div class="container mx-auto p-4" style="max-width: 50%; ">
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
                    <label for="domains">Domains (one per line)</label>

                    <div class="input-group mb-3">
                        <textarea name="domains" id="domains" class="form-control" bind:value={domainsText} cols="30" rows="10" required disabled={nAvailableDomains > 0}></textarea>


                    </div>
                </div>

                <div class="row mb-0">
                    <div class="col">
                        {#if nAvailableDomains > 0}
                            <button type="submit" class="btn btn-primary btn-red w-100">
                                Request {nAvailableDomains} Domains Registration
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
                            Message
                        </th>
                    </tr>
                </thead>

                <tbody>
                    {#each Object.entries(checkedDomains) as [domain, {isAvailable, requested, spinner, message}]}
                        <tr>
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
                                {message}
                            </td>
                        </tr>
                    {:else}
                        <tr>
                            <td colspan={4}>
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

