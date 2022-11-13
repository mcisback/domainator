<script>
    import route from 'ziggy-js';
    import {LaraUser} from '../../../user';

    import AdminLayout from '../../Layouts/AdminLayout.svelte'
    import ModelTable from "../../Components/ModelTable.svelte";


    export let users = {}
    export let permissions = {}
    export let roles = {}

    console.log('users: ', users)
    console.log('roles: ', roles)

    // console.log('permissions: ', permissions)
    // console.log('roles final: ', roles)

    users = users.map(user =>{
        const ret =  {
            ...user
        }

        permissions.forEach(({id, name}) => {
            ret[name] = user.permissions.includes(name)
        })

        // ret['allRoles'] = roles

        return ret
    })

    console.log('users: ', users)

    const types = {}

    permissions.forEach(({id, name}) => {
        types[name] = {
            type: 'checkbox',
            onChange: async (e, user, permission) => {
                console.log('Checked target: ', e.target.checked)

                await axios.post(route('api.admin.users.togglepermission', {
                    userId: user.id
                }) , {
                    permission,
                    value: e.target.checked
                }).then(res => console.log('api.admin.users.togglepermission res: ', res.data))
            }
        }
    })

    types['roles'] = {
        type: 'select',
        options: () => roles,
        onChange: (e) => {
            console.log('You selected: ', e.target)
        },
        getOptionKeyValue: (role => ({value: role.id, label: role.name})),
        isSelected: (record, option) => {
            // console.log('record, option: ', {roles: roles.map(role => role.id), option})
            return record.roles.map(role => role.id).includes(option.id)
        }
    }

    console.log('types: ', types)

</script>

<AdminLayout let:props let:currentUser>
    <div class="container w-100 p-5">
        <div>
            <ModelTable
                currentUser={currentUser}
                title={"Tabella Ruoli E Permessi"}
                records={users}
                showCheckboxes={false}
                aliases={{
                    firstName: 'Nome',
                    lastName: 'Cognome',
                }}
                exclude={[
                    'id',
                    'firstName',
                    'lastName',
                    'permissions',
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
                types={types}
            />
        </div>
    </div>
</AdminLayout>

<style>
    /*.main-section {*/
    /*    font-size: 2.5em;*/
    /*    background-color: white;*/
    /*    cursor: pointer;*/
    /*    padding: 5%;*/
    /*}*/
</style>
