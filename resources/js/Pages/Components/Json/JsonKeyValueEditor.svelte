<script>
    export let jsonObject = {}
    export let keysTitle = 'Keys'
    export let valuesTitle = 'Values'

    const updateKey = (oldKey, newKey, atIndex) => {
        const value = jsonObject[oldKey]
        delete jsonObject[oldKey]

        let _object = Object.entries(jsonObject)

        _object.splice( atIndex, 0, [newKey, value] )

        jsonObject[newKey] = value

        jsonObject = Object.fromEntries(_object)

        // alljsonObject[currentLocale] = jsonObject

        console.log('updateKey: ', {oldKey, newKey, value, atIndex, jsonObject})
    }

    const addEntry = (atIndex) => {
        let _trans = Object.entries(translations)

        _trans.splice( atIndex + 1, 0, [`English Phrase ${atIndex + 1}`, 'Translated Phrase'] )

        translations = Object.fromEntries(_trans)

        // allTranslations[currentLocale] = translations

        console.log('addEntry _trans: ', {translations, _trans})
    }

    const removeEntry = (atIndex) => {
        let _trans = Object.entries(translations)

        _trans.splice( atIndex, 1 )

        translations = Object.fromEntries(_trans)

        // allTranslations[currentLocale] = translations

        console.log('removeEntry _trans: ', {translations, _trans})
    }
</script>

<div class="row mb-3">
    <div class="col-4">
        <span class="text-uppercase" style="font-weight: bold;">{keysTitle}</span>
    </div>

    <div class="col-4">
        <span class="text-uppercase" style="font-weight: bold;">{valuesTitle}</span>
    </div>
</div>

{#each Object.entries(jsonObject) as [key, value], i}
    <div class="row mb-3 align-items-center">
        <div class="col-4" style="white-space: nowrap;">
            <input type="text" value={key} on:input={(e) => updateKey(key, e.target.value, i)}>
        </div>

        <div class="col-1">
            <i class="fa-solid fa-arrow-right"></i>
        </div>

        <div class="col-4">
            <input type="text" bind:value={jsonObject[key]}>
        </div>

        <div class="col-1">
            <button class="btn btn-primary btn-red btn-block" on:click|preventDefault={() => {addEntry(i)}}>
                <i class="fa-solid fa-plus"></i>
            </button>
        </div>

        <div class="col-1">
            <button class="btn btn-primary btn-red btn-block" on:click|preventDefault={() => {removeEntry(i)}}>
                <i class="fa-solid fa-trash"></i>
            </button>
        </div>
    </div>
{/each}
