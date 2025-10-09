# Props from controller 
users: Paginated user collection with data array and pagination metadata 
users.data: Array of user objects for table rows 
users.data[].id: Unique user identifier 
users.data[].first_name: User's first name 
users.data[].last_name: User's last name 
users.data[].email: User's email address 
users.data[].gender: User's gender (male/female) 
users.data[].status: User active status (boolean) 
users.data[].created_at: User creation timestamp 
users.current_page: Current pagination page number 
users.last_page: Total number of pagination pages 
users.from: Starting record number of current page 
users.to: Ending record number of current page 
users.total: Total number of users 
users.per_page: Number of users per page 
filters: Current filter parameters from request 
filters.search: Search query string for filtering users
filters.status: Status filter value (null, '1', or '0') 

# Reactive state 
loading: Boolean flag for table loading state 
filters.search: Reactive search input model 
filters.status: Reactive status filter model 
columns: Array of table column configuration objects 
statusOptions: Array of status filter dropdown options 
pagination.page: Current pagination page number 

# Composables 
safeRoute: Helper function for safe route generation with parameter support 

# Methods 
search: Debounced function to trigger user search and filtering 
onPaginationChange: Handler for pagination page change events 
deleteRecords: Function to delete user with confirmation dialog





<!-- ----------------------------------------------------------------------- -->
<!--                                 columns                                 -->
<!-- ----------------------------------------------------------------------- -->
columns = [
    { name: 'id', label: 'ID', field: 'id', align: 'left', sortable: true },
    { name: 'name', label: 'Name', field: (row) => `${row.first_name} ${row.last_name}`, align: 'left', sortable: true },
    { name: 'email', label: 'Email', field: 'email', align: 'left', sortable: true },
    { name: 'gender', label: 'Gender', field: 'gender', align: 'center', sortable: true },
    { name: 'status', label: 'Status', field: 'status', align: 'center' },
    { name: 'created_at', label: 'Created', field: 'created_at', align: 'center', sortable: true },
    { name: 'actions', label: 'Actions', align: 'center' },
];
const columns = shallowRef(props.columns);

const columnsWithLabel = columns.map(col => ({
  ...col,
  label: titleCase(col.name)
}));


import columnsJson from './columns.json'
const columns = columnsJson.map(col => {
  if    ( 
            typeof col.field === 'string' &&
            (col.field.startsWith('function') || col.field.startsWith('(')) 
        ) {
    return {
      ...col,
      field: eval(`(${col.field})`)
    }
  }
  return col
})

<!-- ----------------------------------------------------------------------- -->
<!--                                 columns                                 -->
<!-- ----------------------------------------------------------------------- -->
        field: (row) => `${row.first_name} ${row.last_name}`,
        field: row => row.phones?.map(p => p.phone_no).join(', ') || '0000',
<!-- ----------------------------------------------------------------------- -->
<!--             اگر بخواهیم دیتای دریافتی از لاراول را فلت کنیم             -->
<!-- ----------------------------------------------------------------------- -->
const rows = computed(() => {
  return props.users.data.map(user => ({
    ...user,
    phone_numbers: user.phones?.map(p => p.number).join(', ') || '',
    role_names: user.roles?.map(r => r.name).join(', ') || '',
    employee_code: user.employee?.id || '',
  }))
})
<!-- ----------------------------------------------------------------------- -->
<!--                                  rows                                   -->
<!-- ----------------------------------------------------------------------- -->
const rows = ref([...props.users.data])
const rows = ref(props.users?.data ?? [])

const rows = ref(props.users?.data || []);
const rows = shallowRef(props.users?.data || []);
const rows = shallowRef(Array.isArray(props.users?.data) ? [...props.users.data] : [])
watch(() => props.users?.data, (newData) => {
    rows.value = Array.isArray(newData) ? [...newData] : [];
    // console.log(rows.value);
}, { immediate: true });


