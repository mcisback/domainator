<script>
export let domain = null
// export let currentDomainRequest = null
export let currentSedoAccountId = null
export let sedoAccounts = []
export let sedoCategories = []
export let sedoLanguage = 'en'
export let sedoLanguages = {}
export let sedoCategoryIds = [];
export let isForSale = false
export let fixedprice = false
export let minprice = false
export let price = 0

console.log('SedoSelectAccount sedoCategoryIds: ', sedoCategoryIds)
</script>

<div class="row mb-3">
    <label for={`sedo_account`}>
        Select SEDO Account
    </label>
    <select name={`sedo_account`} id={`sedo_account`} bind:value={currentSedoAccountId} class="form-select">
        {#each sedoAccounts as {id, name, partner_id}}
            <option value={id}>
                {name}:{partner_id}
            </option>
        {/each}
    </select>
</div>

<div class="row mb-3">
    <label for={`sedo_categories`}>
        SEDO Domain Categories
    </label>
    <select name={`sedo_categories`} id={`sedo_categories`} bind:value={sedoCategoryIds} class="form-select" multiple>
        {#each Object.entries(sedoCategories) as [id, category]}
            <option value={id}>
                {id} : {category}
            </option>
        {/each}
    </select>
</div>

<div class="row mb-3">
    <label for={`sedo_language`}>
        SEDO Domain Language
    </label>
    <select name={`sedo_language`} id={`sedo_language`} bind:value={sedoLanguage} class="form-select">
        {#each Object.entries(sedoLanguages) as [iso, language]}
            <option value={iso}>
                {iso} : {language}
            </option>
        {/each}
    </select>
</div>

