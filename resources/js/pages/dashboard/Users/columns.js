// ./columns.js


// export default _vs_ export const
export default [
    {
        name: 'id',
        label: 'Id',
        field: 'id',
        align: 'left',
        sortable: true
    },
    {
        name: 'gender',
        label: 'Gender',
        field: 'gender',
        align: 'left',
        sortable: true
    },
    {
        name: 'full_name',
        label: 'Full Name',
        field: (row) => `${row.first_name} ${row.last_name}`,
        align: 'left',
        sortable: true
    },
    {
        name: 'actions',
        label: 'Actions',
        align: 'center'
    },
    {
        name: 'status',
        label: 'Status',
        field: 'status',
        align: 'center'
    },
    {
        name: 'phone',
        label: 'Phone',
        field: 'phone',
        align: 'left',
        sortable: false
    },
    {
        name: 'first_name',
        label: 'First Name',
        field: 'first_name',
        align: 'left',
        sortable: true
    },
    {
        name: 'last_name',
        label: 'Last Name',
        field: 'last_name',
        align: 'left',
        sortable: true
    },
    {
        name: 'email',
        label: 'Email',
        field: 'email',
        align: 'left',
        sortable: true
    },
    {
        name: 'roles',
        label: 'Roles',
        field: 'roles',
        align: 'left',
        sortable: false
    },
    {
        name: 'employee',
        label: 'Employee',
        field: 'employee',
        align: 'center',
        sortable: true
    },
    {
        name: 'description',
        label: 'Description',
        field: 'description',
        align: 'left',
        sortable: false
    },
    {
        name: 'created_by',
        label: 'Created by',
        field: 'created_by',
        align: 'left',
        sortable: true
    },
    {
        name: 'created_at',
        label: 'Created',
        field: 'created_at',
        align: 'center',
        sortable: true
    },
    {
        name: 'updated_at',
        label: 'Updated at',
        field: 'updated_at',
        align: 'center',
        sortable: true
    },
    {
        name: 'deleted_at',
        label: 'Deleted at',
        field: 'deleted_at',
        align: 'center',
        sortable: false
    },
];
