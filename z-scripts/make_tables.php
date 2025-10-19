<?php
// Example CLI execution:
// php z-scripts/make_tables.php zz-database/Excells/NES.xlsm products --with-common-fields

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

// -----------------------------
// 1) Get CLI arguments
// -----------------------------
if ($argc < 3) {
    echo "Usage: php make_tables.php <excel-file> <table-name> [--with-common-fields]\n";
    exit;
}

$excelFile = $argv[1];
$tableName = $argv[2];
$withCommonFields = in_array('--with-common-fields', $argv);

// -----------------------------
// 2) Load Excel file
// -----------------------------
$spreadsheet = IOFactory::load($excelFile);
$sheet = $spreadsheet->getSheetByName($tableName); // دقیقاً شیت با نام جدول

if (!$sheet) {
    echo "Sheet '{$tableName}' not found!\n";
    exit;
}

$highestColumn = $sheet->getHighestColumn();
$highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

// -----------------------------
// 3) Read header row (row 1)
// -----------------------------
$headerArray = $sheet->rangeToArray('A1:' . $highestColumn . '1', null, true, true, true);
$headerRow = $headerArray[1]; // ردیف اول
echo "Header row:\n";
foreach ($headerRow as $col => $value) {
    echo "Column {$col} => Value: {$value}\n";
}

// -----------------------------
// 4) Read type row (row 2)
// -----------------------------
$typeArray = $sheet->rangeToArray('A2:' . $highestColumn . '2', null, true, true, true);
$typeRow = $typeArray[2]; // ردیف دوم
echo "\nType row:\n";
foreach ($typeRow as $col => $value) {
    $colLetter = Coordinate::stringFromColumnIndex(Coordinate::columnIndexFromString($col));
    echo "Column {$colLetter}2 => Value: {$value}\n";
}

// -----------------------------
// 5) (Optional) You can continue here to generate SQL based on headerRow & typeRow
// -----------------------------
