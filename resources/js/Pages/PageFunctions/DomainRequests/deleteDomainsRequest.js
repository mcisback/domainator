import route from "ziggy-js";

// Delete Domain
export function deleteDomainsRequest (domainRequest) {
    console.log('deleteDomain() Sending domain Reuquest: ', domainRequest)

    return axios.delete(route('dashboard.domainRegistrationRequests.destroy', {
        domainRegistrationRequest: domainRequest.id
    }))
        .then(res => res.data)
}
