import route from "ziggy-js";

// Delete Domain
export function deleteDomain (domain) {
    console.log('deleteDomain() Sending domain: ', domain)

    return axios.delete(route('dashboard.domains.destroy', {
        domain: domain.id
    }))
        .then(res => res.data)

}
