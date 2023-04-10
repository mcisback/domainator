import route from "ziggy-js";

export function deleteDomain (domain, spinners, form, domainRequests) {
    spinners.deleteDomain = true

    console.log('deleteDomain() Sending domain: ', domain)

    axios.delete(route('dashboard.domainRequest.destroy', {
        domain: domain.id
    }))
        .then(res => res.data)
        .then(data => {
            console.log('deleteDomain() Response data: ', data)

            spinners.deleteDomain = false
            form.success = data.success
            form.message = data.message
            domainRequests = data.domainRequests

            domain = null
        })
        .catch(err => {
            spinners.deleteDomain = false

            form.success = false
            form.message = err.response.data.message

            console.log('Err: ', err.response.data)
        })
}
