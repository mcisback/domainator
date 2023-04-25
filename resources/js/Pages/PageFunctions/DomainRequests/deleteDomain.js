import route from "ziggy-js";

// Delete Domain
export function deleteDomain (domain, spinners, form, domainRequests) {
    console.log('deleteDomain() Sending domain: ', domain)

    return axios.delete(route('dashboard.domainRequest.destroy', {
        domain: domain.id
    }))
        .then(res => res.data)

}
