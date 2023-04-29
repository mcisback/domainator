import route from "ziggy-js";

export function updateCurrentDomain (domain, domainRequests) {
    return domainRequests.filter(d => d.domain === domain.domain)[0]
}

export function verifyDomainOnSEDO (domain) {
    return axios.post(route('dashboard.domains.sedoVerify', {
        domain: domain.id,
        sedoAccount: domain.sedo_account.id
    }))
        .then(res => res.data)
}
