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
    spinners,
    form,
    domainRequests
) {
    spinners.addDomainToSEDO = true

    console.log('addDomainToSEDO() Sending domain: ', {domain, sedoCategoryIds, sedoLanguage})

    axios.post(route('dashboard.domainRequest.addToSedo', {
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
        .then(data => {
            console.log('addDomainToSEDO() Response data: ', data)

            spinners.addDomainToSEDO = false
            form.success = data.success
            form.message = data.message
            domainRequests = data.domainRequests

            domain = updateCurrentDomain(domain, data.domainRequest)
        })
        .catch(err => {
            spinners.addDomainToSEDO = false

            form.success = false
            form.message = err.response.data.message

            console.log('Err: ', err.response.data)
        })
}
