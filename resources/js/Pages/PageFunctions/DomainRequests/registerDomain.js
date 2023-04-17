import route from "ziggy-js";

export function registerDomain (domain, domainRequest, enableWhoisProtection, spinners, form, domainRequests) {
    spinners.registerDomain = true
    spinners.domainsSpinner[domain.domain] = true

    console.log('registerDomain() Sending domain: ', domain)

    axios.post(route('dashboard.domains.register', {
        domain: domain.id,
    }), {
        enableWhoisProtection
    })
        .then(res => res.data)
        .then(data => {
            console.log('registerDomain() Response data: ', data)

            spinners.domainsSpinner[domain.domain] = false
            form.success = data.success
            form.message = data.message
            domainRequests = data.domainRequests

            domain.registered = data.success
        })
        .catch(err => {
            spinners.domainsSpinner[domain.domain] = false

            form.success = false
            form.message = err.response.data.message

            console.log('Err: ', err.response.data)
        })
}
