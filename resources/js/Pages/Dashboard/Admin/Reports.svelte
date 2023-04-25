<script>
    import route from 'ziggy-js';
    import moment from 'moment';

    import DashboardLayout from '../../Layouts/DashboardLayout.svelte'
    import ModelTable from "../../Components/ModelTable.svelte";
    import {onMount} from "svelte";

    import {LaraUser} from '../../../user';
    import {color} from "quill/ui/icons";

    export let users = {}
    export let columns = {}
    export let roles = {}
    // export let permissions = {}

    // permissions.forEach(({id, name}) => {
    //     types[name] = {
    //         type: 'checkbox',
    //         onChange: async (e, user, permission) => {
    //             console.log('Checked target: ', e.target.checked)
    //
    //             await axios.post(route('api.admin.users.togglepermission', {
    //                 userId: user.id
    //             }) , {
    //                 permission,
    //                 value: e.target.checked
    //             }).then(res => console.log('api.admin.users.togglepermission res: ', res.data))
    //         }
    //     }
    // })

    columns['roles'] = 'select'

    console.log('users: ', users)
    console.log('columns: ', columns)
    console.log('roles: ', roles)

</script>

<DashboardLayout let:props let:sections let:currentUser>
    <div class="container w-100 p-5">
        <div>
            <ModelTable
                modelName={"User"}
                title={"Reports"}
                records={users}
                columns={columns}

                formData={{
                    roles: roles.filter(role => role.name === 'staff').map(role => role.id)[0]
                }}

                formTypes={{
                    password: {
                        type: 'password'
                    },
                    roles:  {
                        type: 'select',
                        options: (user, col) => roles,
                        onChange: (e) => {
                            console.log('You selected: ', e.target)
                        },
                        getOptionKeyValue: role => ({value: role.id, label: role.name}),
                        isSelected: (user, role) => {
                            console.log('isSelected(): ', {user, role})

                            if(!user || Object.keys(user).length === 0) {
                                console.log('isSelected() user empty')
                                console.log(`role.name(${role.name}) === "staff"`, role.name === 'staff')

                                return role.name === 'staff'
                            }

                            if(Number.isInteger(user.roles)) {
                                console.log('isSelected() user.roles is number')
                                console.log(`user.roles(${user.roles}) === role.id(${role.id})`, user.roles === role.id)

                                return user.roles === role.id
                            }

                            const hasRole = user.roles.map(role => role.name).includes(role.name)

                            console.log(`isSelected() user.roles.map(role => role.name).includes(${role.name}): `, user.roles.map(role => role.name), hasRole)

                            if(hasRole) {
                                user.roles = role.id
                            }

                            return hasRole
                        }
                    }
                }}

                actions={{
                    create: {
                        route: 'dashboard.users.store',
                        method: 'post',
                    },
                    update: {
                        route: 'dashboard.users.update',
                        method: 'put',
                        params: (record) => ({user: record.id}),
                    },
                    delete: {
                        route: 'dashboard.users.destroy',
                        method: 'delete',
                        params: (formData) => ({user: formData.checked.join(',')}),
                    },
                }}

                aliases={{
                    firstName: 'First Name',
                    lastName: 'Last Name',
                    created_at: 'Joined At',
                    updated_at: 'Last Update',
                    deleted_at: 'Deleted At',
                }}

                exclude={[
                    'id',
                    'email_verified_at',
                    'remember_token',
                    'password',
                    'deleted_at',
                ]}

                editable={[
                    'id',
                    'firstName',
                    'lastName',
                    'email',
                    'username',
                    'password',
                    'roles',
                ]}

                required={[
                    'id',
                    'firstName',
                    'lastName',
                    'email',
                    'username',
                    'password',
                    'roles',
                ]}

                hidden={[
                    'id',
                ]}

                filter={user => {
                    if(currentUser.isAdmin()){
                        return true
                    } else {
                        const _user = {...user}

                        _user.roles = _user.roles.map(role => role.name)

                        return !LaraUser(_user).isAdmin()
                    }
                }}

                format={{
                    created_at: ({created_at: date}) => moment((new Date(date))).format("DD/MM/YYYY H:MM"),
                    updated_at: ({updated_at: date}) => moment((new Date(date))).format("DD/MM/YYYY H:MM"),
                    roles: ({roles: userRoles}) => {
                        console.log('format() userRoles: ', userRoles)

                        return userRoles.map(role => role.name).join(', ')
                    }
                }}
            />
        </div>
    </div>
</DashboardLayout>

<style>
    /*.main-section {*/
    /*    font-size: 2.5em;*/
    /*    background-color: white;*/
    /*    cursor: pointer;*/
    /*    padding: 5%;*/
    /*}*/
</style>
