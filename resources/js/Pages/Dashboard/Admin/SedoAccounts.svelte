<script>
    import {slide, fade} from "svelte/transition"
    import route from "ziggy-js";
    import DashboardLayout from "../../Layouts/DashboardLayout.svelte";
    import AlertBox from "../../Components/Alerts/AlertBox.svelte";
    import Switch from "../../Components/Checkboxes/Switch.svelte";
    import Spinner from "../../Components/Spinners/Spinner.svelte";

    export let sedoAccounts = []
    export let currentUser = {}

    console.log('currentUser: ', currentUser)

    const defaultPorts = {
        ftp: 21,
        sftp: 22,
    }

    const formData = {
        name: '',
        partner_id: '',
        signkey: '',
    }

    const spinners = {
        addSedoAccount: false,
        deleteRow: false,
    }

    let formMessage = null
    let formSuccess = false

    const onSubmit = () => {
        console.log('formData: ', formData)

        spinners.addSedoAccount = true

        axios.post(route('dashboard.sedoAccounts.store'), formData)
            .then(res => res.data)
            .then(data => {
                spinners.addSedoAccount = false

                formSuccess = true
                formMessage = data.message
                sedoAccounts = data.sedoAccounts

            })
            .catch(err => {
                spinners.addSedoAccount = false

                formSuccess = false
                formMessage = err.response.data.message
            })
    }

    const deleteRow = (sedoAccount) => {
        console.log('formData: ', formData)

        spinners.deleteRow = true

        axios.delete(route('dashboard.sedoAccounts.destroy', {
            sedoAccount: sedoAccount.id
        }))
        .then(res => res.data)
        .then(data => {
            spinners.deleteRow = false

            formSuccess = true
            formMessage = data.message
            sedoAccounts = data.sedoAccounts

        })
        .catch(err => {
            spinners.deleteRow = false

            formSuccess = false
            formMessage = err.response.data.message
        })
    }

</script>

<DashboardLayout let:currentUser>
    <div class="position-relative container mx-auto p-4" style="max-width: 50%;">
        <h1 class="text-center">SEDO Accounts</h1>

        <div class="mx-auto">
            {#if formMessage}
                <div transition:slide>
                    <AlertBox bind:success={formSuccess}>
                        <span>{formMessage}</span>
                    </AlertBox>
                </div>
            {/if}
        </div>

        <div class="mx-auto">
            <form on:submit|preventDefault={onSubmit}>

                <div class="row mb-3">
                    <label for="name" class="text-capitalize">
                        Name
                    </label>
                    <input type="text" class="form-control" name="name" id="name" bind:value={formData.name} required>
                </div>

                <div class="row mb-3">
                    <label for="partner_id" class="text-capitalize">
                        PartnerID
                    </label>
                    <input type="text" class="form-control" name="partner_id" id="partner_id" bind:value={formData.partner_id} required>
                </div>

                <div class="row mb-3">
                    <label for="signkey" class="text-capitalize">
                        SignKey
                    </label>
                    <input type="text" class="form-control" name="signkey" id="signkey" bind:value={formData.signkey} required>
                </div>

                <div class="row mb-3">
                    <button class="btn btn-primary btn-red w-100" type="submit">
                        {#if spinners?.addSedoAccount}
                            <Spinner />
                        {/if}

                        Add SEDO Account
                    </button>
                </div>

            </form>
        </div>

        <div class="mx-auto mt-4">
            <div class="row mb-3">
                <h2>SEDO Accounts</h2>
            </div>

            {#if sedoAccounts.length <= 0}
                <div class="row mb-3">
                    <div class="alert alert-light" role="alert">
                        No SEDO Accounts Added Yet
                    </div>
                </div>
            {:else}
                {#each sedoAccounts as {id, name, partner_id, signkey}, i}
                    <div class="row mb-3 align-items-center">
                        <div class="col">
                            #{id} - {name}: {`"${partner_id}"`}:{signkey}
                        </div>

                        <div class="col">
                            <div class="row">
                                <div class="col-2">
                                    <button class="btn btn-primary btn-red" on:click={() => deleteRow(sedoAccounts[i])}>
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>


                    </div>
                {/each}
            {/if}
        </div>
    </div>
</DashboardLayout>
