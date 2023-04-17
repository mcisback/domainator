import route from "ziggy-js";

// Delete Domain
export function deleteDomainsRequest (domainRequest, spinners, form, domainRequests) {
    spinners.deleteDomain = true

    console.log('deleteDomain() Sending domain Reuquest: ', domainRequest)

    axios.delete(route('dashboard.domainRegistrationRequests.destroy', {
        domainRegistrationRequest: domainRequest.id
    }))
        .then(res => res.data)
        .then(data => {
            console.log('deleteDomainsRequest() Response data: ', data)

            spinners.deleteDomain = false
            form.success = data.success
            form.message = data.message
            domainRequests = data.domainRequests

            domainRequest = null
        })
        .catch(err => {
            spinners.deleteDomain = false

            form.success = false
            form.message = err.response.data.message

            console.log('Err: ', err.response.data)
        })
}
