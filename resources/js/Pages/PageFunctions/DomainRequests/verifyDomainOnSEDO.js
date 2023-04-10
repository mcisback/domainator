import route from "ziggy-js";

export function updateCurrentDomain (domain, domainRequests) {
    return domainRequests.filter(d => d.domain === domain.domain)[0]
}

export function verifyDomainOnSEDO (domain, spinners, form, domainRequests) {
    spinners.verifyDomainOnSEDO = true

    axios.post(route('dashboard.domainRequest.sedoVerify', {
        domain: domain.id,
        sedoAccount: domain.sedo_account.id
    }))
        .then(res => res.data)
        .then(data => {
            console.log('verifyDomainOnSEDO() Response data: ', data)

            spinners.verifyDomainOnSEDO = false
            form.success = data.success
            form.message = data.message
            domainRequests = data.domainRequests

            domain = updateCurrentDomain(domain, data.domainRequests)
        })
        .catch(err => {
            spinners.verifyDomainOnSEDO = false

            form.success = false
            form.message = err.response.data.message

            console.log('Err: ', err.response.data)
        })
}
