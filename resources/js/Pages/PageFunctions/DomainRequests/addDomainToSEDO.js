import route from "ziggy-js";
import {updateCurrentDomain} from "./verifyDomainOnSEDO";

export function addDomainToSEDO (
    domain,
    sedoCategoryIds,
    sedoLanguage,
    isForSale,
    price,
    minprice,
    fixedprice,
) {

    console.log('addDomainToSEDO() Sending domain: ', {domain, sedoCategoryIds, sedoLanguage})

    return axios.post(route('dashboard.domainRequest.addToSedo', {
        domain: domain.id,
        sedoAccount: currentSedoAccountId
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
