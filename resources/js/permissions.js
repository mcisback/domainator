function hasPermissionTo(user, permission) {
    return Object.entries(user.permissions).map(permission => permission[0]).includes(permission)
}

function hasRole(user, role) {
    return role.includes(role)
}

export {
    hasPermissionTo,
    hasRole
}
