import route from "ziggy-js";

export function registerDomain (domain, domainRequest, enableWhoisProtection, spinners, form, domainRequests) {
    spinners.registerDomain = true
    spinners.domainsSpinner[domain.domain] = true

    console.log('registerDomain() Sending domain: ', domain)

    return axios.post(route('dashboard.domains.register', {
        domain: domain.id,
    }), {
        enableWhoisProtection
    })
        .then(res => res.data)

}
