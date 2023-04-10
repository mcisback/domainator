import route from "ziggy-js";

export function registerDomain (domain, enableWhoisProtection, spinners, form, domainRequests) {
    spinners.registerDomain = true

    console.log('registerDomain() Sending domain: ', domain)

    axios.post(route('dashboard.domainRequest.register', {
        domain: domain.id
    }), {
        enableWhoisProtection
    })
        .then(res => res.data)
        .then(data => {
            console.log('registerDomain() Response data: ', data)

            spinners.registerDomain = false
            form.success = data.success
            form.message = data.message
            domainRequests = data.domainRequests

            domain.registered = data.success
        })
        .catch(err => {
            spinners.registerDomain = false

            form.success = false
            form.message = err.response.data.message

            console.log('Err: ', err.response.data)
        })
}
