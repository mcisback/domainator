export function localStorageHelper(key) {
    return {
        get [key]() {
            try {
                return JSON.parse(localStorage.getItem(key)) ?? null
            } catch (e) {
                localStorage.getItem(key) ?? null
            }
        },

        set [key](data) {
            localStorage.setItem(key, JSON.stringify(data))
        },

        has(key) {
            return key in localStorage
        },

        del(key) {
            return localStorage.removeItem(key)
        }
    }
}
