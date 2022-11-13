<script>

    import {slide} from "svelte/transition";
    import route from "ziggy-js";

    export let title = 'Model Table'
    export let modelName = 'Record'
    export let records = []

    export let format = {}
    export let aliases = {
        created_at: 'Data Creazione',
        updated_at: 'Ultima Modifica',
    }
    export let cellTypes = {}
    export let formTypes = {}
    export let sortable = []
    export let editable = []
    export let disabled = []
    export let required = []
    export let hidden = []
    export let exclude = [
        'id'
    ]
    export let actions = {}

    const nRecords = records.length
    export let columns = {}

    columns = Object.keys(columns).map(col => {
        return {
            name: col,
            label: aliases[col] ?? col,
            type: formTypes[col]?.type ?? columns[col],
            sortable: sortable.includes(col),
            editable: editable.includes(col),
            disabled: disabled.includes(col) ? true : null,
            hidden: hidden.includes(col),
            exclude: exclude.includes(col),
            required: required.includes(col) ? true : null,
            asc: false,
        }
    })

    console.log('Model Table Columns: ', columns)

    export let showSearchBar = true
    export let showCheckboxes = true

    // Toggled when clicking on record or on 'plus'
    let showForm = false

    export let filter = null

    let searchTerm = ''

    let backupRecords = [...records]

    export let onSearch = () => {
        // console.log('searchTerm: ', {searchTerm, searchCategory})

        records = [...backupRecords]

        if(searchCategory === 'all') {
            records = records.filter(record => {
                for(let i = 0; i < columns.length; i++) {
                    const col = columns[i]

                    console.log('record[col]: ', {record, col, searchTerm, searchCategory, value: record[col], format: format[col](record)})

                    const value = !!format[col] ? format[col](record) : record[col]

                    if(!!('' + value).toLowerCase().match(searchTerm.toLowerCase())) {
                        return record
                    }
                }
            })
        } else {
            records = records.filter(record => {
                const value = !!format[searchCategory] ? format[searchCategory](record) : record[searchCategory]
                if(!!('' + value).toLowerCase().match(searchTerm.toLowerCase())) {
                    return record
                }
            })
        }
    }

    const restoreRecords = () => {
        records = [...backupRecords]

        searchTerm = ''
    }

    let searchCategory = 'all'

    let checkedAll = false
    let checked = []

    let mode = 'create'

    if(!!filter && typeof filter === 'function') {
        records = records.filter(record => {
            return filter(record)
        })
    }

    console.log('Filtered records: ', records)

    const checkAll = () => {
        checked = []

        if(checkedAll === false) {
            records.forEach(record => {
                checked.push(record.id)
            })
        }

        checkedAll = !checkedAll

        console.log('checkAll: ', {checkedAll, checked})
    }

    const add = () => {
        console.log('Clicked add()')

        formData = {...formDataBackup}
        formMessage = null

        mode = 'create'

        showForm = true
    }

    const del = () => {
        console.log('Clicked delete()')

        formData.checked = checked
        mode = 'delete'

        onSubmit()
    }

    const edit = (record) => {
        console.log('Clicked edit(): ', record)

        formData = {...record}

        console.log('edit() formData: ', formData)

        formMessage = null

        mode = 'update'

        showForm = true
    }

    const onRowClick = (e, record) => {
        edit(record)
    }

    const conditionallyChecked = (node) => {
        if(node.value !== 'false') {
            node.setAttribute('checked', 'true')
        } else {
            node.removeAttribute('checked')
        }

        return {
            destroy() {
                node.removeAttribute('checked')
            }
        }
    }

    export let formData = {}
    let formSuccess = false
    let formMessage = null
    const formDataBackup = {...formData}

    console.log('export let formData: ', formData)

    const onSubmit = () => {
        const {
            route: laravelRoute,
            method,
            params = null
        } = actions[mode]

        let url = null

        if(!!params) {
            url = route(laravelRoute, params(formData))
        } else {
            url = route(laravelRoute)
        }

        axios({
            url,
            method,
            data: formData,
        })
        .then(res => {
            console.log('Form Response: ', {res, mode, url, route, formData})

            return res.data
        })
        .then(data => {
            formSuccess = true
            formMessage = data.message ?? `${modelName} ${mode}d Successfully`

            records = data.records ?? records
        })
        .catch(err => {
            console.error('Error: ', err, err.response.data)

            formSuccess = false
            formMessage = err.response.data.message ?? `Error ${mode}ing ${modelName}`
        })
    }

</script>

<div>
    <div class="row">
        <div class="d-flex justify-content-between align-items-center pb-3">
            <div>
                <h2>{title}</h2>
            </div>

            <div class="d-flex justify-content-around align-items-center actions" style="cursor: pointer;">

                <div class="action" style="cursor: pointer;" on:click={add}>
                    <i class="fa-solid fa-circle-plus"></i>
                </div>

                <div class="action" style="cursor: pointer;" on:click={del}>
                    <i class="fa-solid fa-trash-can"></i>
                </div>
            </div>
        </div>
    </div>

    {#if showForm}
        <div class="row mb-3" transition:slide>
            <div>
                <h2 class="text-center text-capitalize">{mode} {modelName}</h2>
            </div>
            <div class="w-100 p-4">
                <form on:submit|preventDefault={onSubmit}>

                    {#if formMessage}
                        <div class="row mb-3" transition:slide>
                            <div class={formSuccess ? 'alert alert-success' : 'alert alert-danger'}>
                                <span>{formMessage}</span>
                            </div>
                        </div>
                    {/if}

                    {#each columns as col}
                        {#if col.editable}
                            <div class="row mb-3">
                                {#if col.hidden}
                                    <input type="hidden" name={col.name} id={col.name} bind:value={formData[col.name]} required={col.required} />
                                {:else}
                                    <div>
                                        <label for={col.name} class="text-capitalize">{col.label}</label>

                                        {#if col.type === 'string' || col.type === 'varchar'}
                                            <input type="text" class="form-control" name={col.name} id={col.name} bind:value={formData[col.name]} disabled={col.disabled} required={col.required}>
                                        {/if}

                                        {#if col.type === 'password'}
                                            <div class="input-group p-0">
                                                {#if !col?.showPassword}
                                                    <input type="password" class="form-control" name={col.name} id={col.name} bind:value={formData[col.name]} disabled={col.disabled} required={col.required}>
                                                {:else}
                                                    <input type="text" class="form-control" name={col.name} id={col.name} bind:value={formData[col.name]} disabled={col.disabled} required={col.required}>
                                                {/if}

                                                <button class="btn btn-outline-secondary" type="button" on:click|preventDefault={() => col.showPassword = !col.showPassword}>
                                                    <i class="fa-solid fa-eye"></i>
                                                </button>
                                            </div>
                                        {/if}

                                        {#if col.type === 'text'}
                                            <textarea class="form-control" name={col.name} id={col.name} bind:value={formData[col.name]} cols="30" rows="10" disabled={col.disabled} required={col.required}></textarea>
                                        {/if}

                                        {#if col.type === 'url'}
                                            <input type="url" class="form-control" name={col.name} id={col.name} bind:value={formData[col.name]} disabled={col.disabled} required={col.required}>
                                        {/if}

                                        {#if col.type === 'color'}
                                            <input type="color" class="form-control p-0 m-2" name={col.name} id={col.name} bind:value={formData[col.name]} disabled={col.disabled} required={col.required}>
                                        {/if}

                                        {#if ['date', 'timestamp', 'datetime', 'datetime-local'].includes(col.type)}
                                            <input type="date" class="form-control" name={col.name} id={col.name} bind:value={formData[col.name]} disabled={col.disabled} required={col.required}>
                                        {/if}

                                        {#if col.type === 'select'}
                                            <select
                                                name={`select-${col.name}`}
                                                id={`select-${col.name}`}
                                                class="form-select"
                                                on:change={(e) => formTypes[col.name].onChange(e, formData[col.name], col)}
                                                bind:value={formData[col.name]}
                                                disabled={col.disabled}
                                                required={col.required}
                                            >
                                                {#each formTypes[col.name].options(formData, col) as option}
                                                    {#if formTypes[col.name].isSelected(formData, option) === true}
                                                        <option value={formTypes[col.name].getOptionKeyValue(option).value} selected>{formTypes[col.name].getOptionKeyValue(option).label}</option>
                                                    {:else}
                                                        <option value={formTypes[col.name].getOptionKeyValue(option).value}>{formTypes[col.name].getOptionKeyValue(option).label}</option>
                                                    {/if}
                                                {/each}
                                            </select>
                                        {/if}
                                    </div>
                                {/if}
                            </div>
                        {/if}
                    {/each}

                    <div class="row mb-3">
                        <button type="submit" class="btn btn-primary btn-block">
                            <span>{mode === 'update' ? 'Update' : 'Save'}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    {/if}

    {#if showSearchBar}
        <div class="row">
            <div class="input-group mb-2 p-0">
                <div class="input-group-text">Search By Field</div>
                <select bind:value={searchCategory} class="form-select" data-placeholder="Choose anything" id="prepend-text-multiple-field">
                    <option value="all">All Fields</option>

                    {#each columns as col}
                        {#if !col.exclude}
                            <option value="{col.name}">{col.label}</option>
                        {/if}
                    {/each}
                </select>
            </div>
            <div class="input-group mb-3 p-0">
                <span class="input-group-text">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input bind:value={searchTerm} on:input={onSearch} type="text" class="form-control" placeholder="Search">
                {#if searchTerm !== ''}
                    <span class="input-group-text" on:click={restoreRecords} style="cursor: pointer;">
                        <i class="fa-solid fa-xmark"></i>
                    </span>
                {/if}
            </div>
        </div>
    {/if}

    <div class="row" style="overflow-y: scroll;">
        <table>
            <thead>
            <tr class="text-capitalize">
                {#if showCheckboxes}
                    <th>
                        <input value={checkedAll} on:change={checkAll} type="checkbox" name="check-all" id="check-all">
                    </th>
                {/if}
                {#each columns as {exclude, label}}
                    {#if !exclude}
                        <th class="p-2">{label}</th>
                    {/if}
                {/each}
            </tr>
            </thead>

            <tbody>
            {#each records as record, i}
                <tr class="model-table-row">
                    {#if showCheckboxes}
                        <td>
                            <input bind:group={checked} on:change={() => console.log('checked: ', checked)} value={record.id} type="checkbox" name="check-row" id={`check-row-${record.id}`}>
                        </td>
                    {/if}
                    {#each columns as {name: col, exclude}}
                        {#if !exclude}
                            <td class="p-2" on:click={(e) => onRowClick(e, record)}>
                                {#if !!cellTypes[col]}
                                    {#if cellTypes[col].type === 'checkbox'}
                                        <input type="checkbox"
                                               name={`check-${col}-${record.id}`}
                                               id={`check-${col}-${record.id}`}
                                               on:change={(e) => cellTypes[col].onChange(e, record, col)}
                                               value={record[col]}
                                               use:conditionallyChecked
                                        >
                                    {:else if cellTypes[col].type === 'select'}
                                        <select
                                               name={`select-${col}-${record.id}`}
                                               id={`select-${col}-${record.id}`}
                                               on:change={(e) => cellTypes[col].onChange(e, record, col)}
                                        >
                                            {#each cellTypes[col].options(record, col) as option}
                                                {#if cellTypes[col].isSelected(record, option)}
                                                    <option value={cellTypes[col].getOptionKeyValue(option).value} selected>{cellTypes[col].getOptionKeyValue(option).label}</option>
                                                {:else}
                                                    <option value={cellTypes[col].getOptionKeyValue(option).value}>{cellTypes[col].getOptionKeyValue(option).label}</option>
                                                {/if}
                                            {/each}
                                        </select>
                                    {/if}
                                {:else}
                                    {@html !!format[col] ? format[col](record) : record[col]}
                                {/if}
                            </td>
                        {/if}
                    {/each}
                </tr>
            {:else}
                <tr class="model-table-row">
                    <td colspan={columns.filter(col => !col.exclude).length + (showCheckboxes ? 1 : 0)} class="text-center mt-3" style="font-weight: bold;">
                        No {modelName}s
                    </td>
                </tr>
            {/each}
            </tbody>
        </table>
    </div>
</div>

<style>
    tr {
        cursor: pointer;
    }

    .model-table-row {
        background-color: transparent;
        color: black;
    }

    .model-table-row:hover {
        background-color: #ffe380;
        /*color: white;*/
    }

    .action {
        padding: 1rem;
        font-size: 1.5rem;
    }
</style>
