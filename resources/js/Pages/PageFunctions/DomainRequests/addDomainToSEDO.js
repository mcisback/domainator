import route from "ziggy-js";
export function addDomainToSEDO (
    domain,
    sedoAccountId,
    sedoCategoryIds,
    sedoLanguage,
    isForSale,
    price,
    minprice,
    fixedprice,
) {

    console.log('addDomainToSEDO() Sending domain: ', {domain, sedoAccountId, sedoCategoryIds, sedoLanguage})

    return axios.post(route('dashboard.domains.addToSedo', {
        domain: domain.id,
        sedoAccount: sedoAccountId
    }), {
        sedoCategoryIds,
        sedoLanguage,
        isForSale,
        price,
        minprice,
        fixedprice,
    })
        .then(res => res.data)
}
