<script>
    import DashboardLayout from "../Layouts/DashboardLayout.svelte";
    import route from "ziggy-js";
    import Spinner from "../Components/Spinners/Spinner.svelte";

    import { slide } from 'svelte/transition';
    import AlertBox from "../Components/Alerts/AlertBox.svelte";

    const spinners = {
        checkDomain: false
    }

    let isAvailable = null

    const initFormData = () => {
        return {
            domain: '',
        }
    }

    let formData = initFormData()

    let formMessage = null
    let formSuccess = false

    const requestDomainRegistration = () => {
        spinners.checkDomain = true

        console.log('requestDomainRegistration() Sending formData: ', formData)

        axios.post(route('dashboard.domains.store'), {
            domain: formData.domain
        })
        .then(res => res.data)
        .then(data => {
            console.log('Response data: ', data)

            spinners.checkDomain = false
            formSuccess = data.success
            formMessage = data.message

            isAvailable = data.isAvailable
        })
        .catch(err => {
            spinners.checkDomain = false

            formSuccess = false
            formMessage = err.response.data.message

            console.log('Err: ', err.response.data)
        })
    }

    const checkDomainAvailability = () => {
        spinners.checkDomain = true

        console.log('checkDomainAvailability() Sending formData: ', formData)

        axios.post(route('api.index', {
            action: 'checkDomain',
            domain: formData.domain
        }))
        .then(res => res.data)
        .then(data => {
            console.log('Response data: ', data)
            spinners.checkDomain = false

            formSuccess = data.success
            formMessage = data.message

            isAvailable = data.isAvailable
        })
        .catch(err => {
            spinners.checkDomain = false

            formSuccess = false
            formMessage = err.response.data.message

            console.log('Err: ', err.response.data)
        })
    }

    const onSubmit = () => {
        if(isAvailable === true) {
            return requestDomainRegistration()
        }

        return checkDomainAvailability()
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

        <div class="mx-auto">
            <form on:submit|preventDefault={onSubmit}>
                <div class="row">
                    <label for="domain">Domain</label>

                    <div class="input-group mb-3">
                        <input type="text" name="domain" id="domain" class="form-control" placeholder="Type domain to check" bind:value={formData.domain}>
                        <span class="input-group-text" id="basic-addon2">

                        {#if spinners.checkDomain}
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
                    </span>
                    </div>
                </div>

                <div class="row mb-0">
                    <div class="col-md-8 offset-md-4">
                        {#if isAvailable === true}
                            <button type="submit" class="btn btn-primary btn-red">
                                Request Domain Registration
                            </button>
                        {:else}
                            <button type="submit" class="btn btn-primary btn-red">
                                Check Domain Availability
                            </button>
                        {/if}
                    </div>
                </div>

            </form>
        </div>
    </div>
</DashboardLayout>

<style>
    /*[id^="paragraph"] {*/
    /*    border: thin solid black;*/
    /*}*/
</style>

