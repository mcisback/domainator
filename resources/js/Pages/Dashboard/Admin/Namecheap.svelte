<script>
    import {slide, fade} from "svelte/transition"
    import route from "ziggy-js";
    import DashboardLayout from "../../Layouts/DashboardLayout.svelte";
    import AlertBox from "../../Components/Alerts/AlertBox.svelte";
    import Switch from "../../Components/Checkboxes/Switch.svelte";
    import Spinner from "../../Components/Spinners/Spinner.svelte";

    export let settings = []
    // export let currentUser = {}

    // console.log('currentUser: ', currentUser)
    console.log('settings: ', settings)

    const formData = {
        ...settings
    }

    const spinners = {
        update: false,
        deleteRow: false,
    }

    let formMessage = null
    let formSuccess = false

    const onSubmit = () => {
        console.log('formData: ', formData)

        spinners.update = true

        axios.put(route('dashboard.namecheap.update'), formData)
            .then(res => res.data)
            .then(data => {
                console.log('dashboard.namecheap.update: ', data)

                spinners.update = false

                formSuccess = true
                formMessage = data.message
                settings = data.settings

            })
            .catch(err => {
                spinners.update = false

                formSuccess = false
                formMessage = err.response.data.message
            })
    }

</script>

<DashboardLayout let:currentUser>
    <div class="position-relative container mx-auto p-5" style="max-width: 50vh; ">
        <h1 class="text-center">Namecheap Account Settings</h1>

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

                {#each Object.entries(settings) as [key, value]}
                    <div class="row mb-3">
                        <label for={key}>
                            {key.split(/(?=[A-Z])/).join(' ')}
                        </label>

                        <input type="text" class="form-control" name={key} bind:value={formData[key]} id={key} required />
                    </div>
                {/each}

                <div class="row mb-3">
                    <button class="btn btn-primary btn-red w-100" type="submit">
                        {#if spinners?.update}
                            <Spinner />
                        {/if}

                        Update Namecheap Info
                    </button>
                </div>

            </form>
        </div>

    </div>
</DashboardLayout>
