<?php

namespace App\Helpers;

use Illuminate\Support\Str;


class QColumnsHelper
{
    /**
     * Generate Quasar columns array from a given fields array
     *
     * @param array $fields
     * @return array
     */
    public static function QColumns(array $fields): array
    {
        $columns = array_map(
            function ($field) {
                $field = subString($field,' as ', 2);
                if (strtolower($field) == 'actions') {
                    $name = 'actions';
                    $label = 'Actions';
                    $align = 'center';
                    $sortable = false;
                } else {
                    $name = (string) $field;
                    $label = ucwords(str_replace('_', ' ', $name));
                    $align = strlen($name) < 6 ? 'center' : 'left';
                    $nonSortables = ['description', 'actions'];
                    $sortable = !in_array(strtolower($name), $nonSortables);
                }

                // return "{ name: '$name', label: '$label', field: '$name', align: '$align', sortable: '$sortable' },";
                return [
                    'name' => $name,
                    'label' => $label,
                    'field' => $name,
                    'align' => $align,
                    'sortable' => $sortable,
                ];
            }, $fields
        );


        return $columns;
    }
}

/*
$columns = User::QColumns($fields);

<script> 
const columns = @json($columns); 
// اضافه کردن ستون "name" با ترکیب first_name و last_name
columns.forEach(col => {
    if (col.name === 'first_name') {
        col.field = row => `${row.first_name} ${row.last_name}`;
        col.label = 'Name';
    }
});
</script>

$column = "{ name: '$name', label: '$lable', field: '$field', align: '$align', sortable: '$sortable' },";
return $column;


*/