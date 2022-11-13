<script>

    import { slide } from 'svelte/transition'

    import DashboardLayout from "../Layouts/DashboardLayout.svelte";
    import route from "ziggy-js";
    import {onMount} from "svelte";
    import {fetchApi} from "../../Api/fetchApi";
    import AlertBox from "../Components/Alerts/AlertBox.svelte";
    import TemplateSelector from "../Components/Select/TemplateSelector.svelte";

    const formData = {}
    let currentTemplate = null
    let currentLocale = null
    let baseLocale = 'en'
    // let _locales = []
    let locales = []
    let templates = []
    let allTranslations = {}
    let translations = {}
    let showSpinner = false
    let formSuccess = false
    let formResponse = null

    $: allTranslations, (_=>{
        console.log('allTranslations changed: ', allTranslations)
    })();

    $: currentLocale, (_=>{
        if(!currentLocale) {
            return
        }

        console.log('currentLocale changed: ', currentLocale)

        if(Object.keys(allTranslations).length <= 0) {
            return
        }

        translations = allTranslations[currentLocale]

        selectLocale()
    })();

    const selectLocale = () => {
        console.log('selectLocale: ', {currentTemplate, currentLocale, allTranslations})

        if(Object.keys(allTranslations).length > 0) {
            console.log('Changing Locale to: ', currentLocale, allTranslations[currentLocale])

            translations = allTranslations[currentLocale]
        }
    }

    $: currentTemplate, (_=>{
        if(!currentTemplate) {
            return
        }

        console.log('currentTemplate changed: ', currentTemplate)

        selectTemplate()
    })();

    const selectTemplate = () => {
        console.log('Getting Locales for: ', currentTemplate)

        getLocalesByTemplate(currentTemplate)
    }

    const addLocale = () => {
        const locale = prompt('Locale name (lowercase, 2 letters as in en, fr, it, hu, etc...): ')

        locales = [...locales, locale.toLowerCase()]

        allTranslations[locale.toLowerCase()] = {...allTranslations[baseLocale]}

        currentLocale = locale

        console.log('You entered locale: ', locale, locales)
    }

    const onSubmit = () => {
        showSpinner = true

        formData.allTranslations = allTranslations
        formData.locales = locales

        console.log('Sending formData: ', formData)

        axios.put(route('dashboard.translations.update', {
            template: currentTemplate,
        }), formData).then(res => {
            console.log('Res: ', res)

            showSpinner = false
            formSuccess = true

            return res.data.data
        })
        .then(data => {
            console.log('Response: ', data)

            formResponse = `Translations Saved For ${currentTemplate}`
        })
        .catch(err => {
            showSpinner = false
            formSuccess = false

            formResponse = err.response.data.message

            console.log('Err: ', err)
        })
    }

    const getTemplates = () => {
        fetchApi('getTemplates')
            .then(data => {
                templates = data.templates
                currentTemplate = templates[0]
                formData.template = currentTemplate

                getLocalesByTemplate(currentTemplate)

                console.log('templates: ', templates, currentTemplate, formData)
            })
    }

    const getLocalesByTemplate = (template) => {
        console.log('getLocalesByTemplate called')

        fetchApi('getLocalesByTemplate', {
            template
        })
        .then(data => {
            locales = data.locales

            console.log('Locales are: ', locales)

            currentLocale = locales[0]

            console.log('Changing Default Locale To: ', currentLocale)

            locales.forEach(async (locale) => {
                console.log('Getting Translations for: ', locale)

                await getTranslations(currentTemplate, locale)
            })

            selectLocale()
        })
    }

    let count = 0

    const getTranslations = async (template, locale) => {
        await fetchApi('getTranslations', {
            template,
            locale,
        })
        .then(data => {
            translations = data.translations
            allTranslations[locale] = translations

            console.log(`getTranslations#${count++}: `, {
                currentLocale,
                translations,
                allTranslations
            })
        })
    }

    const addEntryToLocale = (atIndex) => {
        let _trans = Object.entries(translations)

        _trans.splice( atIndex + 1, 0, [`English Phrase ${atIndex + 1}`, 'Translated Phrase'] )

        translations = Object.fromEntries(_trans)

        allTranslations[currentLocale] = translations

        console.log('addEntryToLocale _trans: ', {translations, _trans})
    }

    const removeEntryFromLocale = (atIndex) => {
        let _trans = Object.entries(translations)

        _trans.splice( atIndex, 1 )

        translations = Object.fromEntries(_trans)

        allTranslations[currentLocale] = translations

        console.log('removeEntryFromLocale _trans: ', {translations, _trans})
    }

    const updateTranslationKey = (oldKey, newKey, atIndex) => {
        const value = translations[oldKey]
        delete translations[oldKey]

        let _trans = Object.entries(translations)

        _trans.splice( atIndex, 0, [newKey, value] )

        translations[newKey] = value

        translations = Object.fromEntries(_trans)

        allTranslations[currentLocale] = translations

        console.log('updateTranslationKey: ', {locale: currentLocale, oldKey, newKey, value, atIndex, translations})
    }

    onMount(() => {
        getTemplates()
    })

</script>

<DashboardLayout>
    <div class="position-relative container mx-auto p-4" style="max-width: 50vh; ">
        <h1 class="text-center">Translations Creator</h1>

        <div class="mx-auto">
            <form on:submit|preventDefault={onSubmit}>

                <div class="position-fixed bg-white p-2"
                     style="bottom: 0; left: 0; width: 100%; right: 0; border: thin solid black;">
                    <div class={formSuccess ? "d-flex align-items-center justify-content-around" : "d-flex align-items-center justify-content-end"}>
                        {#if formResponse}
                            <AlertBox bind:success={formSuccess}>
                                <span>{formResponse}</span>
                            </AlertBox>
                        {/if}

                        <button class="btn btn-primary btn-red" type="submit">
                            {#if showSpinner}
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            {/if}

                            Save
                        </button>
                    </div>
                </div>

                <div class="row mb-3">
                    <TemplateSelector templates={templates} onSelectTemplate={(template) => currentTemplate = template} bind:currentTemplate={formData.template} />
                </div>

                {#if currentTemplate}
                    <div class="row mb-3" transition:slide>
                        <label for="default_locale">
                            Select Locale (Locales are in templates/{currentTemplate}/translations/)
                        </label>
                        <div class="input-group p-0">
                            <select name="default_locale" id="default_locale" bind:value={currentLocale} class="form-control">
                                {#each locales as locale}
                                    <option value={locale}>{locale}</option>
                                {/each}
                            </select>

                            <button class="btn btn-primary btn-red btn-block" on:click|preventDefault={addLocale}>
                                Add
                            </button>
                        </div>
                    </div>
                {/if}

                {#if currentLocale}
                    <div class="row mb-3" transition:slide>
                        <div class="row mb-3">
                            <div class="col-4">
                                <span class="text-uppercase" style="font-weight: bold;">{baseLocale}</span>
                            </div>

                            <div class="col-4">
                                <span class="text-uppercase" style="font-weight: bold;">{currentLocale}</span>
                            </div>
                        </div>
                        {#each Object.entries(translations) as [key, value], i}
                            <div class="row mb-3 align-items-center">
                                <div class="col-4" style="white-space: nowrap;">
                                    <input type="text" value={key} on:input={(e) => updateTranslationKey(key, e.target.value, i)}>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </div>

                                <div class="col-4">
                                    <input type="text" bind:value={translations[key]}>
                                </div>

                                <div class="col-1">
                                    <button class="btn btn-primary btn-red btn-block" on:click|preventDefault={() => {addEntryToLocale(i)}}>
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </div>

                                <div class="col-1">
                                    <button class="btn btn-primary btn-red btn-block" on:click|preventDefault={() => {removeEntryFromLocale(i)}}>
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        {/each}
                    </div>
                {/if}

            </form>
        </div>
    </div>
</DashboardLayout>
