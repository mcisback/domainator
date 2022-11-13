<script>

    import { slide } from 'svelte/transition'

    import DashboardLayout from "../Layouts/DashboardLayout.svelte";
    import route from "ziggy-js";
    import {onMount} from "svelte";
    import {fetchApi} from "../../Api/fetchApi";
    import AlertBox from "../Components/Alerts/AlertBox.svelte";
    import TemplateSelector from "../Components/Select/TemplateSelector.svelte";
    import Switch from "../Components/Checkboxes/Switch.svelte";

    let templates = []
    let currentTemplate = null

    let showSpinner = false
    let formSuccess = false
    let formMessage = null

    let toggleFieldsData = {}

    const initFormData = () => {
        return {
            fields: {}
        }
    }

    let formData = initFormData()

    const onSubmit = () => {
        showSpinner = true

        // Object.entries(formData.fields).forEach(([field, fieldData]) => {
        //     fieldData.data
        // })

        console.log('Sending formData: ', formData)

        axios.put(route('dashboard.editor.update', {
            template: currentTemplate,
        }), formData).then(res => {
            console.log('Res: ', res)

            return res.data
        })
            .then(data => {
                console.log('Response data: ', data)

                showSpinner = false
                formSuccess = true

                formMessage = data.message
            })
            .catch(err => {
                showSpinner = false
                formSuccess = false

                formMessage = err.response.data.message

                console.log('Err: ', err)
            })
    }

    const selectTemplate = (template) => {
        formData = initFormData()

        formData.template = template
        currentTemplate = template

        console.log('You Choose: ', formData.template)
        console.log(`currentTemplate changed to ${currentTemplate}`)


        getTemplateFields(template)
    }

    let fields = {}
    let fieldsConfig = {}
    let fieldsTypes = [
        'string',
        'text',
        'editor',
        'select',
        'url',
        'domain',
        'password',
        'image_url',
        'one-per-line-array',
    ]

    const getTemplateFields = (template) => {
        fetchApi('getTemplateFields', {
            template
        })
        .then(data => {
            console.log('data.fields: ', data.fields)

            fieldsConfig = data.fields.config

            delete data.fields.config

            fields = Object.entries(data.fields).map(f => f[1])

            // let order = 0
            //
            // fields = Object.fromEntries(Object.entries(data.fields).map(([field, value]) => {
            //     if(typeof value === 'string') {
            //         value = {
            //             type: value
            //         }
            //     }
            //
            //     value.name = field
            //     value.order = order++
            //
            //     return [
            //         field,
            //         value
            //     ]
            // }))

            formData.fields = {...data.fields}

            console.log(`getTemplateFields('${template}'): `, {fields, fieldsConfig})
        })
    }

    const getTemplates = () => {
        fetchApi('getTemplates')
            .then(data => {
                templates = data.templates
                currentTemplate = templates[0]
                formData.template = templates[0]

                getTemplateFields(currentTemplate)

                console.log('getTemplates(): ', {templates, currentTemplate, formData})
            })
    }

    function swapElements(array, source, dest) {
        return source === dest
            ? array : array.map((item, index) => index === source
                ? array[dest] : index === dest
                    ? array[source] : item);
    }

    const orderUp = (event, field, atIndex) => {
        console.log('orderUp()')

        // let _fields = Object.entries(fields)

        console.log('orderUp() fields: ', fields)

        fields = swapElements(fields, atIndex - 1, atIndex)

        const tmp = fields[atIndex - 1].order
        fields[atIndex - 1].order = fields[atIndex].order
        fields[atIndex].order = tmp

        console.log('orders: ', {fields1: fields[atIndex - 1].order, fields2: tmp})

        // fields = Object.fromEntries(fields)
        fields = [...fields]

        console.log('orderUp _fields: ', {field1: fields[atIndex], fields2: fields[atIndex - 1], formData, fields})
    }

    const orderDown = (event, field, atIndex) => {
        console.log('orderDown()')

        // let _fields = Object.entries(fields)

        fields = swapElements(fields, atIndex, atIndex + 1)

        const tmp = fields[atIndex + 1].order
        fields[atIndex + 1].order = fields[atIndex].order
        fields[atIndex].order = tmp

        console.log('orders: ', {fields1: fields[atIndex + 1].order, fields2: tmp})

        // fields = Object.fromEntries(fields)
        fields = [...fields]

        console.log('orderUp _fields: ', {field1: fields[atIndex], fields2: fields[atIndex - 1], formData, fields})
    }

    onMount(() => {
        getTemplates()
    })

    const sortFields = () => {
        return fields.sort((a, b) => a.order - b.order)
    }
</script>

<DashboardLayout>
    <div class="position-relative container mx-auto p-4" style="max-width: 50vh; ">
        <h1 class="text-center">Fields Editor</h1>

        {#if formMessage}
            <div class="mx-auto" transition:slide>
                <AlertBox bind:success={formSuccess}>
                    {formMessage}
                </AlertBox>
            </div>
        {/if}

        <div class="mx-auto">
            <form on:submit|preventDefault={onSubmit}>
                <div class="row mb-3">
                    <TemplateSelector templates={templates} onSelectTemplate={selectTemplate} bind:currentTemplate={formData.template} />
                </div>

                {#if Object.keys(fields).length > 0}
                    <div class="row mb-3" transition:slide>
                        <div class="row mb-3">
                            <button type="submit" class="btn btn-primary btn-red">
                                {#if showSpinner}
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                {/if}

                                Update Fields
                            </button>
                        </div>

                        <div class="row mb-3">
                            <div class="col-1">
                                #
                            </div>
                            <div class="col-4">
                                Field
                            </div>
                            <div class="col-4">
                                Type
                            </div>
                            <div class="col-1">
                                Required ?
                            </div>
                        </div>
                        {#each sortFields() as {name: field, order, type, data}, index}
                            <div class="row mb-3">
                                <div class="col-1">
                                    #{order}
                                </div>
                                <div class="col-4">
                                    <input type="text" name={field} id={field} bind:value={formData.fields[field].name} disabled>
                                </div>
                                <div class="col-4">
                                    <select name={`${field}_type`} id={`${field}_type`} bind:value={formData.fields[field].type}>
                                        {#each fieldsTypes as fieldType}
                                            <option value={fieldType} selected={fieldType === type ? true : null}>
                                                {fieldType}
                                            </option>
                                        {/each}
                                    </select>
                                </div>
                                <div class="col-1">
                                    <Switch id={`${field}_required`} bind:checked={formData.fields[field].required} />
                                </div>
                                <div class="col-1">
                                    <div class="row">
                                        {#if index === 0}
                                            <div class="col-1">
                                                <span>
                                                    <i class="fa-solid fa-minus"></i>
                                                </span>
                                            </div>
                                        {/if}

                                        {#if index > 0}
                                            <div class="col-1" style="cursor: pointer;" on:click={(e) => orderUp(e, field, index)}>
                                                <span>
                                                    <i class="fa-solid fa-arrow-up"></i>
                                                </span>
                                            </div>
                                        {/if}

                                        {#if index < (fields.length - 1)}

                                            <div class="col-1" style="cursor: pointer;" on:click={(e) => orderDown(e, field, index)}>
                                                <span>
                                                    <i class="fa-solid fa-arrow-down"></i>
                                                </span>
                                            </div>

                                        {/if}

                                        {#if index >= (fields.length - 1)}
                                            <div class="col-1">
                                                <span>
                                                    <i class="fa-solid fa-minus"></i>
                                                </span>
                                            </div>
                                        {/if}
                                    </div>
                                </div>
                            </div>

                            {#if type === 'select'}
                                <div class="row mb-3" transition:slide>
                                    <label for={`${field}_data`} class="text-capitalize" on:click={() => toggleFieldsData[field] = !toggleFieldsData[field]} style="cursor: pointer;">
                                        Field Data (1 row per line): <i class={toggleFieldsData[field] ? 'fa-solid fa-caret-up' : 'fa-solid fa-caret-down'}></i>
                                    </label>
                                    {#if toggleFieldsData[field] ?? false}
                                        <div class="col w-100" transition:slide>
                                            <textarea class="w-100" name={`${field}_data`} id={`${field}_data`} cols="30" rows="15" value={data ? data.join(',').replaceAll(',', "\n") : ''} on:change={(e) => {
                                                data = e.target.value.split('\n')
                                            }}></textarea>
                                        </div>
                                    {/if}
                                </div>
                            {/if}
                        {/each}
                    </div>
                {/if}
            </form>
        </div>
    </div>
</DashboardLayout>
