<script>
    import {slide, fade} from "svelte/transition"
    import route from "ziggy-js";
    import DashboardLayout from "../../Layouts/DashboardLayout.svelte";
    import AlertBox from "../../Components/Alerts/AlertBox.svelte";
    import Switch from "../../Components/Checkboxes/Switch.svelte";
    import Spinner from "../../Components/Spinners/Spinner.svelte";

    export let allowedTdls = ''
    // export let currentUser = {}

    // console.log('currentUser: ', currentUser)
    // console.log('settings: ', settings)

    const formData = {
        // ...settings
        allowedTdls,
    }

    const spinners = {
        update: false,
    }

    let formMessage = null
    let formSuccess = false

    const onSubmit = () => {
        console.log('formData: ', formData)

        spinners.update = true

        axios.put(route('dashboard.settings.update'), formData)
            .then(res => res.data)
            .then(data => {
                console.log('dashboard.settings.update: ', data)

                spinners.update = false

                formSuccess = true
                formMessage = data.message
            })
            .catch(err => {
                spinners.update = false

                formSuccess = false
                formMessage = err.response.data.message
            })
    }

</script>

<DashboardLayout let:currentUser>
    <div class="position-relative container mx-auto p-5" style="max-width: 50%; ">
        <h1 class="text-center">Settings</h1>

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
                    <label for="allowedTdls">
                        Domain TDLs (.com, .info, ecc)
                        <br>
                        (One per line)
                    </label>

                    <textarea bind:value={formData.allowedTdls} name="allowedTdls" id="allowedTdls" cols="30" rows="10"></textarea>
                </div>

                <div class="row mb-3">
                    <button class="btn btn-primary btn-red w-100" type="submit">
                        {#if spinners?.update}
                            <Spinner />
                        {/if}

                        Update Settings
                    </button>
                </div>

            </form>
        </div>

    </div>
</DashboardLayout>
