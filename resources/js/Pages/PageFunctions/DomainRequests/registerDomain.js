import route from "ziggy-js";

export function registerDomain (domain, domainRequest, enableWhoisProtection, spinners) {
    console.log('registerDomain() Sending domain: ', domain)

    return axios.post(route('dashboard.domains.register', {
        domain: domain.id,
    }), {
        enableWhoisProtection
    })
        .then(res => res.data)

}
