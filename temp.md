$filterables = [];

        $options = Month::query()
            ->select('name')         // Select only the name column
            ->distinct()             // Remove duplicate names
            ->orderBy('name')        // Sort names alphabetically
            ->pluck('name')          // Return a Collection of names
            ->map(fn($name, $index) => [   // Transform each name with its index into a dropdown-friendly array
                'label' => $name,          // Use name as label
                'value' => $index + 1         // Use array index as value
            ])
            ->toArray();             // Convert to a plain PHP array
        $filterables['month'] = ['multiple' => true, 'options' => $options];

        $options = Cut::query()
            ->select('maximum_batch_size')
            ->distinct()
            ->orderBy('maximum_batch_size')
            ->pluck('maximum_batch_size')
            ->map(fn($name, $index) => [
                'label' => $name,
                'value' => $index + 1
            ])
            ->toArray();
        $filterables['maximum_batch_size'] = ['multiple' => true, 'options' => $options];

        $options = ['Active', 'Inactive'];
        $options = array_map(
            fn($name, $index) => ['label' => $name, 'value' => $index + 1],
            ['Active', 'Inactive'], //` each $name: fn-arg1
            array_keys($options)  //` each $index: fn-arg2
        );
        $filterables['status'] = ['multiple' => false, 'options' => $options];
<!-- ----------------------------------------------------------------------- -->
<!--                                   ---                                   -->
<!-- ----------------------------------------------------------------------- -->
