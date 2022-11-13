function LaraUser(user) {
    return {
        ...user,

        hasPermissionTo(permission) {
            if(Array.isArray(this.permissions)) {
                return this.permissions.includes(permission)
            } else if(typeof this.permissions === 'object') {
                return this.permissions[permission] ?? false
            }
        },

        hasRole(role) {
            console.log('typeof this.roles: ', typeof this.roles)

            return this.roles.includes(role)
        },

        isAdmin() {
            return this.hasRole('admin')
        },

        isStaffMember() {
            return this.hasRole('staff')
        }
    }
}


export {
    LaraUser
}
