<script>
    /*
    * TODO:
    * * DONE Delete Users
    * * DONE Add User Roles to created users
    * * Make QUILL Required?
    * * Fields Editor ?
    * * Edit FTP Server
    * * Template editor ?
    * * SFTP Working ?
    * */

    import {onMount} from "svelte";

    import { slide } from 'svelte/transition';

    import DashboardLayout from "../Layouts/DashboardLayout.svelte";
    import route from "ziggy-js";
    import {fetchApi} from "../../Api/fetchApi";
    import AlertBox from "../Components/Alerts/AlertBox.svelte";
    import TemplateSelector from "../Components/Select/TemplateSelector.svelte";
    import Switch from "../Components/Checkboxes/Switch.svelte";
    import QuillEditor from "../Components/Editors/QuillEditor.svelte";
    import Modal from "../Components/Modals/Modal.svelte";
    import ProgressBar from "../Components/Progress/ProgressBar.svelte";

    export let servers = null

    let currentTemplate = null
    let templates = []
    let locales = []
    let zipUrl = null
    let previewUrl = null
    let showSpinner = false
    let editors = {}
    let fields = {}
    let imagesEl = {}
    let publishToFtp = false
    let generatedTemplatePath = null
    let liveTranslations = false
    const ftp = {
        remotePath: null,
        files: null,
        count: 0,
    }

    const spinners = {
        publishToFtp: false
    }

    const landingUrl = () => {
        return servers ? servers?.filter(server => server.id === formData.server)[0].url + `/${formData.index_filename}` : ''
    }

    const initFormData = () => {
        return {
            yourDomain: '',
            header_image: null,
            paragraph1: '',
            paragraph2: '',
            imageType: '',
            default_locale: 'en',
            index_filename: 'index.html',
            references: null,
            privacyLinks: {},
            publishToFtp: false,
        }
    }

    let formData = initFormData()

    onMount(() => {
        getTemplates()
    })

    let fieldsConfig = {}

    const getTemplateFields = (template) => {
        fetchApi('getTemplateFields', {
            template
        })
        .then(data => {
            console.log('data.fields: ', data.fields)

            fieldsConfig = data.fields.config

            delete data.fields.config

            fields = Object.fromEntries(Object.entries(data.fields).map(([field, value]) => {
                if(typeof value === 'string') {
                    value = {
                        type: value
                    }
                }

                return [
                    field,
                    value
                ]
            }))

            formData.fields = fields

            console.log(`getTemplateFields for ${template}: `, {fields, fieldsConfig})
        })
    }

    const getTemplates = () => {
        fetchApi('getTemplates')
            .then(data => {
                templates = data.templates
                currentTemplate = templates[0]
                formData.template = templates[0]

                getLocales()

                getTemplateFields(currentTemplate)

                console.log('templates: ', templates, currentTemplate, formData)
            })
    }

    const getLocales = () => {
        fetchApi('getLocalesByTemplate', {
            template: currentTemplate
        })
            .then(data => {
                locales = data.locales

                formData.default_locale = locales[0]
            })
    }

    const selectTemplate = (template) => {
        formData = initFormData()

        formData.template = template
        currentTemplate = template

        console.log('You Choose: ', formData.template)
        console.log(`currentTemplate changed to ${currentTemplate}`)


        getTemplateFields(template)
        getLocales()
    }

    const readImage = (field, file) => {
        // Check if the filean image.
        console.log('File:', file)

        formData[field] = {}

        if (file.type && !file.type.startsWith('image/')) {
            formData[field]['imageError'] = 'File is not an image';

            return;
        }

        formData[field] = {
            name: file.name,
            type: file.type,
        }

        const reader = new FileReader();
        reader.addEventListener('load', (event) => {
            // console.log('event.target.result: ', {t: imageData, e: event.target.result})

            formData[field]['imageData'] = event.target.result;
        });
        reader.readAsDataURL(file);
    }

    const onSubmit = async () => {
        zipUrl = null

        if(Object.keys(editors).length > 0) {
            console.log('editors: ', editors)
        }

        // await sleep(1000)

        doSubmit()
    }

    const doSubmit = () => {
        showSpinner = true

        formData.publishToFtp = publishToFtp

        console.log('Sending formData: ', formData)

        axios.post(route('dashboard.generator'), {
            data: formData
        }).then(res => {
            console.log('Res: ', res)

            showSpinner = false

            return res.data.data
        })
        .then(data => {
            console.log('Response data: ', data)

            zipUrl = data.zipUrl
            previewUrl = data.previewUrl
            generatedTemplatePath = data?.generatedTemplatePath ?? null
        })
        .catch(err => {
            showSpinner = false

            console.log('Err: ', err.response.data)
        })
    }

    let ftpMessage = null
    let ftpSuccess = false

    const startPublishToFtp = () => {
        console.log('Starting Publishing To FTP: ', generatedTemplatePath)

        ftpMessage = null
        spinners.startPublishToFtp = true
        ftp.files = null
        ftpSuccess = false
        // spinners.doFtpUpload = false

        axios.post(route('dashboard.ftpserver.start'), {
            server_id: formData.server,
            generatedTemplatePath,
        }).then(res => {
            console.log('Res: ', res)

            spinners.startPublishToFtp = false

            return res.data
        })
        .then(data => {
            console.log('Response data: ', data)

            ftp.files = data.files.filter(file => file !== '').map(file => {
                return {
                    path: file,
                    status: 'ready',
                }
            })

            ftp.remotePath = data.remotePath
            ftp.count = 0

            ftpSuccess = true
            ftpMessage = data.message
        })
        .catch(err => {
            spinners.startPublishToFtp = false

            ftpSuccess = false
            ftpMessage = err.response.data

            console.log('Err: ', err.response.data)
        })
    }

    const uploadSingleFileToFTP = (file) => {
        file.status = 'uploading'

        console.log('Uploading: ', file)

        axios.post(route('dashboard.ftpserver.publish'), {
            server_id: formData.server,
            generatedTemplatePath,
            remotePath: ftp.remotePath,
            file: file.path,
        }).then(res => res.data)
        .then(data => {
            console.log('Response data: ', data)

            file.status = 'uploaded'

            ftp.count++;
        })
        .catch(err => {
            file.status = 'error'

            console.log('Err: ', err.response.data)
        })
    }

    const uploadToFtp = () => {
        console.log('Publishing To FTP: ', ftp)

        spinners.doFtpUpload = true

        const interval = setInterval(() => {
            if(ftp.count >= (ftp.files.length + 1)) {
                clearInterval(interval)

                spinners.doFtpUpload = false
            } else {
                if(ftp.files[ftp.count] !== undefined) {
                    if(ftp.files[ftp.count].status === 'ready') {
                        uploadSingleFileToFTP(ftp.files[ftp.count])
                    }
                }
            }
        }, 150)

        spinners.doFtpUpload = false
    }

    // const sortFields = () => {
    //     return Object.entries(fields)
    //     // .map(x => x[1]).sort((a, b) => a.order - b.order).map(x => [x.name, x])
    // }
</script>

<DashboardLayout>
    <div class="container mx-auto p-4" style="max-width: 50vh; ">
    <h1 class="text-center">Template Generator</h1>

    <div class="mx-auto">
        <form on:submit|preventDefault={onSubmit}>

            <div class="row mb-3">
                <TemplateSelector templates={templates} onSelectTemplate={selectTemplate} bind:currentTemplate={formData.template} />
            </div>

            {#if currentTemplate}
                <div class="row mb-3" transition:slide>
                    <label for="default_locale">
                        Default Locale (Locales are in template/{formData.template}/translations/)
                    </label>

                    <select name="default_locale" id="default_locale" bind:value={formData.default_locale} class="form-control form-select">
                        {#each locales as locale}
                            <option value={locale}>{locale}</option>
                        {/each}
                    </select>
                </div>
            {/if}

            <div class="row mb-3">
                <Switch id="live_translations" bind:checked={liveTranslations}>
                    Add Live Translations With JS
                </Switch>
            </div>

            {#if liveTranslations === true}
                {#if locales}
                    <div class="w-100" transition:slide>
                        {#each locales as locale}
                            <div class="row mb-3">
                                <label for="privacy_link_{locale}">Privacy Link For <span style="font-weight: bold; text-transform: uppercase;">{locale}</span>: </label>
                                <div class="input-group p-0">
                                    <span class="input-group-text text-uppercase">{locale}</span>
                                    <input type="url" name="privacy_link_{locale}" id="privacy_link_{locale}" bind:value={formData.privacyLinks[locale]} class="form-control">
                                </div>
                            </div>
                        {/each}
                    </div>
                {/if}
            {/if}

            {#if Object.keys(fields).length > 0}

                <div class="row mb-3">
                    <label for="index_filename">Index File Name (ex: index.html|index.php|whatever.html): </label>
                    <input type="text" name="index_filename" id="index_filename" bind:value={formData.index_filename} class="form-control">
                </div>

                {#each Object.entries(fields) as [field, fieldData], i}
                    {#if fieldData.type === 'domain'}
                        <div class="row mb-3">
                            <label for={field} class="text-capitalize">
                                {fieldData.required ? '* ' : ''}{field}
                            </label>
                            <div class="input-group p-0">
                                <span class="input-group-text">https://</span>
                                <input type="text" name={field} id={field} bind:value={formData[field]} class="form-control" required={fieldData.required ? true : null}>
                            </div>
                        </div>
                    {/if}

                    {#if fieldData.type === 'string'}
                        <div class="row mb-3">
                            <label for={field} class="text-capitalize">
                                {fieldData.required ? '* ' : ''}{field}
                            </label>
                            <input type="text" name={field} id={field} bind:value={formData[field]} class="form-control" required={fieldData.required ? true : null}>
                        </div>
                    {/if}

                    {#if fieldData.type === 'text'}
                        <div class="row mb-3">
                            <label for={field} class="text-capitalize">
                                {fieldData.required ? '* ' : ''}{field}
                            </label>
                            <textarea name={field} id={field} bind:value={formData[field]} class="form-control" required={fieldData.required ? true : null} rows="20" cols="10"></textarea>
                        </div>
                    {/if}

                    {#if fieldData.type === 'image_url'}
                        {#if formData[field]?.imageError}
                            <div class="row mb-3">
                                <div class="alert alert-danger" role="alert">{formData[field].imageError}</div>
                            </div>
                        {/if}

                        <div class="row mb-3">
                            <label for={field} class="text-capitalize">
                                {fieldData.required ? '* ' : ''}{field}
                            </label>
                            <input type="file" name={field} id={field} bind:this={imagesEl[field]} on:change={() => readImage(field, imagesEl[field].files[0])} class="form-control" required={fieldData.required ? true : null}>
                        </div>

                        {#if formData[field]?.imageData}
                            <div class="row mb-3">
                                <img src={formData[field].imageData} alt="" class="w-100" style="max-height: 20vh; max-width: 20vh;">
                            </div>
                        {/if}
                    {/if}

                    {#if fieldData.type === 'editor'}
                        <div class="row mb-3">
                            <label for={field} class="text-capitalize">
                                {fieldData.required ? '* ' : ''}{field}
                            </label>
                            <QuillEditor id={`${currentTemplate}_${field}`} bind:data={formData[field]} />
                        </div>
                    {/if}

                    {#if fieldData.type === 'one-per-line-array'}
                        <div class="row mb-3">
                            <label for="{field}">
                                <span class="text-capitalize">
                                    {fieldData.required ? '* ' : ''}{field}
                                </span>
                                (one per line):
                            </label>
                            <textarea rows="20" name="{field}" id="{field}"  bind:value={formData[field]} class="form-control" required={fieldData.required ? true : null}></textarea>
                        </div>
                    {/if}

                    {#if fieldData.type === 'select'}
                        <div class="row mb-3">
                            <label for="{field}">
                                <span class="text-capitalize">
                                    {fieldData.required ? '* ' : ''}{field}
                                </span>
                            </label>

                            <select name="{field}" id="{field}" bind:value={formData[field]} class="form-control form-select" required={fieldData.required ? true : null}>
                                {#each fieldData.data as option}
                                    <option value={option}>{option}</option>
                                {/each}
                            </select>
                        </div>
                    {/if}
                {/each}

            {/if}

            <div class="row mb-3">
                <div class="col-3">
                    <div class="form-check form-check-inline form-switch red">
                        <input class="form-check-input" type="checkbox" id="publishToFtp" bind:checked={publishToFtp}>
                        <label class="form-check-label" style="white-space: nowrap;" for="publishToFtp">
                            Publish To FTP Server
                        </label>
                    </div>
                </div>
            </div>

            {#if publishToFtp}
                <div class="row mb-3" transition:slide>
                    <label for="server">
                        Choose FTP Server:
                    </label>

                    {#if servers && servers.length > 0}
                        <select name="server" id="server" bind:value={formData.server} class="form-control form-select">
                            {#each servers as {id, name, host, port, username, path}}
                                <option value={id}>#{id} - {name}: "{username}"@{host}:{port}{path}</option>
                            {/each}
                        </select>
                    {:else}
                        <div class="alert alert-light" role="alert">
                            No FTP Servers Configured, Pleass Add One
                        </div>
                    {/if}
                </div>
            {/if}

            <div class="row mb-3">
                <button type="submit" class="btn btn-primary" disabled={showSpinner || null}>
                    {#if showSpinner}
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    {/if}

                    Generate
                </button>
            </div>

            {#if !!zipUrl}
                <div class="row mb-3" transition:slide>
                    <div class="alert alert-info" role="alert">
                        Generated Template:
                        <a href={zipUrl} class="btn btn-link p-0" style="border: none;">
                            Download
                        </a>
                            <br>
                            Preview:
                        <a href={previewUrl} class="btn btn-link p-0" style="border: none;" target="_blank">
                            Preview
                        </a>
                    </div>
                </div>
            {/if}

            {#if generatedTemplatePath}
                <div class="row mb-3" transition:slide>
                    <button class="btn btn-primary btn-red" on:click|preventDefault={startPublishToFtp}>
                        {#if spinners.startPublishToFtp}
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        {/if}

                        Start FTP Publishing Now
                    </button>
                </div>

                {#if ftpMessage}
                    <AlertBox bind:success={ftpSuccess}>
                        {ftpMessage}
                    </AlertBox>
                {/if}

                {#if ftpSuccess === true}

                    {#if ftp.files}
                        <Modal id="uploadToFtp" title="Upload to Ftp">
                            <div slot="footer" class="w-100">

                                {#if formData.server}
                                    <div class="w-100 mb-3">
                                        Landing URL:
                                        <a href={landingUrl()} target="_blank">
                                            {landingUrl()}
                                        </a>
                                    </div>
                                {/if}


                                <div class="w-100 mb-3">
                                    <ProgressBar max={ftp.files.length} count={ftp.count} />
                                </div>

                                <div class="w-100">
                                    <button class="btn btn-primary btn-red w-100" on:click|preventDefault={uploadToFtp}>
                                        {#if spinners.doFtpUpload}
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        {/if}

                                        Upload To FTP Now
                                    </button>
                                </div>

                            </div>
<!--                            <div class="row mb-3" transition:slide>-->
<!--                               -->
<!--                            </div>-->

                            <div class="mb-3">
                                {#each ftp.files as ftpFile}
                                    <div>
                                        <span>{ftpFile.path}</span>
                                        {#if ftpFile.status === 'ready'}
                                        <span>
                                            <i class="fa-solid fa-file"></i>
                                        </span>
                                        {/if}

                                        {#if ftpFile.status === 'uploading'}
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        {/if}

                                        {#if ftpFile.status === 'uploaded'}
                                        <span style="color: #009600;">
                                            <i class="fa-solid fa-check"></i>
                                        </span>
                                        {/if}

                                        {#if ftpFile.status === 'error'}
                                        <span style="color: #c40000;">
                                            <i class="fa-solid fa-xmark"></i>
                                        </span>
                                        {/if}
                                    </div>
                                {/each}
                            </div>
                        </Modal>
                    {/if}

                {/if}

            {/if}

        </form>
    </div>
</div>
</DashboardLayout>

<style>
    [id^="paragraph"] {
        border: thin solid black;
    }
</style>

