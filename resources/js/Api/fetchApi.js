const fetchApi = (action, data = {}) => {
    return axios('/api', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        data: {
            action,
            ...data
        }
    })
    .then(res => res.data)
    .catch(err => {
        console.error('fetchAPI Error: ', err)

        return err
    })
}

export {
    fetchApi
}
