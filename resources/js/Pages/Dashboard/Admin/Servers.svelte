<script>
    import {slide, fade} from "svelte/transition"
    import route from "ziggy-js";
    import DashboardLayout from "../../Layouts/DashboardLayout.svelte";
    import AlertBox from "../../Components/Alerts/AlertBox.svelte";
    import Switch from "../../Components/Checkboxes/Switch.svelte";

    export let servers = []
    export let users = []
    export let currentUser = {}

    console.log('currentUser: ', currentUser)

    const defaultPorts = {
        ftp: 21,
        sftp: 22,
    }

    const formData = {
        url: `https://sixreviews.com/{ftp_username}`,
        protocol: 'ftp',
        path: '/',
        port: 21,
        passive: false,
        ssl: false,
        usePrivateKey: false,
        user_id: currentUser.id,
    }

    const spinners = {
        testConnection: false,
        submit: false,
        listDirectories: false,
    }

    let formMessage = null
    let formSuccess = false
    let showFormMessage = false
    let connectionIsWorking = null
    let directories = null
    let showPrivateKeyTextarea = false

    $: showPrivateKeyTextarea = formData.usePrivateKey

    const onSelectProtocol = () => {
        console.log('formData.protocol changed')

        formData.port = defaultPorts[formData.protocol]
    }

    const onSubmit = () => {
        console.log('formData: ', formData)

        spinners.submit = true

        axios.post(route('dashboard.ftpserver.store'), formData)
            .then(res => res.data)
            .then(data => {
                spinners.submit = false

                formSuccess = true
                formMessage = data.message
                servers = data.servers

                showFormMessage = true
            })
            .catch(err => {
                spinners.submit = false

                formSuccess = false
                showFormMessage = true
                formMessage = err.response.data.message
            })
    }

    const testConnection = () => {
        console.log('testConnection data: ', formData)

        spinners.testConnection = true

        axios.post(route('dashboard.ftpserver.test'), formData)
            .then(data => data.data)
            .then(data => {
                console.log('test connection data: ', data)

                spinners.testConnection = false

                formSuccess = true
                connectionIsWorking = true
                formMessage = data.message

                showFormMessage = true
            })
            .catch(err => {
                console.error('test connection error: ', err)

                spinners.testConnection = false
                formSuccess = false
                formMessage = err.response.data.message

                showFormMessage = true

                console.error('test connection error: ', {err, formSuccess, formMessage})
            })
    }

    const listDirectories = () => {
        console.log('listDirectories data: ', formData)

        spinners.listDirectories = true
        directories = null

        axios.post(route('dashboard.ftpserver.list'), formData)
            .then(data => data.data)
            .then(data => {
                console.log('listDirectories data: ', data)

                spinners.listDirectories = false

                formSuccess = true
                connectionIsWorking = true
                formMessage = data.message
                directories = data.directories

                showFormMessage = true
            })
            .catch(err => {
                console.error('listDirectories error: ', err)

                spinners.listDirectories = false

                formSuccess = false
                formMessage = err.response.data.message

                console.error('listDirectories error: ', {err, formSuccess, formMessage})
            })
    }

    const deleteServer = (server) => {
        console.log('deleteServer data: ', server)

        axios.delete(route('dashboard.ftpserver.destroy', {
            ftpserver: server.id
        }))
        .then(data => {
            console.log('deleteServer data: ', data)

            formSuccess = true
            formMessage = data.data.message
            servers = data.data.servers

            showFormMessage = true
        })
        .catch(err => {
            console.error('deleteServer error: ', err)

            formSuccess = false
            formMessage = err.response.data.message

            showFormMessage = true

            console.error('deleteServer error: ', {err, formSuccess, formMessage})
        })
    }

    // onMount(() => {
    //     formData.url = 'https://sixreviews.com/' + formData.username
    // })
</script>

<DashboardLayout let:currentUser>
    <div class="position-relative container mx-auto p-5" style="max-width: 50vh; ">
        <h1 class="text-center">FTP Manager</h1>

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
                    <label for="template">* Choose Protocol: </label>

                    <select name="template" id="template" bind:value={formData.protocol} on:change={onSelectProtocol} class="form-control form-select" required>
                        <option value="ftp">FTP</option>
                        <option value="sftp">SFTP</option>
                    </select>
                </div>

                <div class="row mb-3">
                    <label for="name" class="text-capitalize">* Name</label>
                    <input type="text" name="name" id="name" bind:value={formData.name} class="form-control" required>
                </div>

                <div class="row mb-3">
                    <label for="url" class="text-capitalize">HTTP URL:</label>
                    <input type="url" name="url" id="url" bind:value={formData.url} class="form-control">
                </div>

                {#if currentUser.isAdmin()}
                    <div class="row mb-3">
                        <label for="name" class="text-capitalize">* (For Admins) Assign To User:</label>
                        <select name="user_id" id="user_id" bind:value={formData.user_id} class="form-control form-select" required>
                            {#each users as user}
                                <option value={user.id}>{user.firstName} {user.lastName} - {user.username} - {user.email}</option>
                            {/each}
                        </select>
                    </div>
                {/if}

                <div class="row mb-3">
                    <label for="host"class="text-capitalize">* Choose Host</label>
                    <div class="input-group p-0">
                        <span class="input-group-text">{formData.protocol}://</span>
                        <input type="text" name="host" id="host" bind:value={formData.host} class="form-control" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="port"class="text-capitalize">* Choose Port</label>
                    <input type="number" name="port" id="port" bind:value={formData.port} class="form-control" required>
                </div>

                <div class="row mb-3 position-relative">
                    {#if formData.host && formData.port}
                        <div class="row mb-3" transition:slide>
                            <label for="path"class="text-capitalize">Path</label>

                            <div class="input-group p-0 position-relative">
                                <input type="text" name="path" id="path" bind:value={formData.path} class="form-control">

                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" on:click={listDirectories}>
                                        <i class="fa-solid fa-folder-open"></i>
                                    </button>
                                </div>

                                {#if spinners.listDirectories}
                                    <div class="input-group-append d-flex align-items-center justify-content-center mx-3" transition:fade>
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    </div>
                                {/if}
                            </div>
                        </div>
                    {/if}

                    {#if directories}
                        <div class="row mb-3" transition:slide>
                            <div class="bg-white p-2" style="top: 0; left: 0; right: 0; z-index:10; cursor: pointer;">
                                {#each directories as directory}
                                    <div class="my-2" on:click={() => {
                                        formData.path = `${formData.path}/${directory}`.replace(/\/+/gm, '/')

                                        listDirectories()
                                    }}>
                                        {`${formData.path}/${directory}`.replace(/\/+/gm, '/')}
                                    </div>
                                {/each}
                            </div>
                        </div>
                    {/if}
                </div>

                <div class="row mb-3">
                    <label for="username" class="text-capitalize">Username</label>
                    <input type="text" name="username" id="username" bind:value={formData.username} class="form-control" required on:input={() => {
                        console.log('formData.url: ', {httpUrl: formData.url})

                        if(formData.url.includes('{ftp_username}')) {
                            formData.url = formData.url.replace('{ftp_username}', formData.username)
                        }
                    }}>
                </div>

                {#if !showPrivateKeyTextarea}
                    <div class="row mb-3">
                        <label for="password" class="text-capitalize">Password</label>
                        <input type="text" name="password" id="password" bind:value={formData.password} class="form-control">
                    </div>
                {:else}
                    <div class="row mb-3" transition:slide>
                        <label for="privateKey" class="text-capitalize">* Private Key</label>
                        <textarea name="privateKey" id="privateKey" bind:value={formData.privateKey} class="form-control" rows="20" required></textarea>
                    </div>
                {/if}

                <div class="row mb-3">
                    <div class="col-3">
                        <Switch id="passive" bind:checked={formData.passive}>
                            Passive Mode
                        </Switch>
                    </div>

                    <div class="col-3">
                        <Switch id="ssl" bind:checked={formData.ssl}>
                            SSL
                        </Switch>
                    </div>

                    {#if formData.protocol === 'sftp'}
                        <div class="col-3">
                            <Switch id="usePrivateKey" bind:checked={formData.usePrivateKey}>
                                Login With Private Key
                            </Switch>
                        </div>
                    {/if}

                </div>

                <div class="row mb-3">
                    <button class="btn btn-outline-secondary w-100" on:click|preventDefault={testConnection}>
                        {#if spinners?.testConnection}
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        {/if}

                        Test Connection
                    </button>
                </div>

                <div class="row mb-3">
                    <button class="btn btn-primary btn-red w-100" type="submit" disabled={!connectionIsWorking || null}>
                        {#if spinners?.submit}
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        {/if}

                        Add Server
                    </button>
                </div>

            </form>
        </div>

        <div class="mx-auto mt-4">
            <div class="row mb-3">
                <h2>Servers</h2>
            </div>

            {#if servers.length <= 0}
                <div class="row mb-3">
                    <div class="alert alert-light" role="alert">
                        No Servers Added Yet
                    </div>
                </div>
            {:else}
                {#each servers as {id, name, protocol, host, port, path, username}, i}
                    <div class="row mb-3 align-items-center">
                        <div class="col">
                            #{id} - {name}: {`"${username}"`}@{protocol}://{host}:{port}{path}
                        </div>

                        <div class="col">
                            <div class="row">
                                <div class="col-2">
                                    <button class="btn btn-primary btn-red">
                                        <i class="fa-solid fa-rotate-right"></i>
                                    </button>
                                </div>

                                <div class="col-2">
                                    <button class="btn btn-primary btn-red" on:click={() => deleteServer(servers[i])}>
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
