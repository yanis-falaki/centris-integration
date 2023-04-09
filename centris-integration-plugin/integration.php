<?php
// For development purposes an absolute path in windows style notation must be used as the plugin is a symlink and LocalWP is using a windows based shell
$path = "D:\Local Repo\local-sites\jb-staging\app\public\wp-load.php";

// Load WordPress
define( 'SHORTINIT', true );
require_once($path);

$var_tables = [
  "INSCRIPTIONS" => [],
  "ADDENDA" => [],
  "PHOTOS" => []
];
$const_tables = [
  "MUNICIPALITES" => [],
  "QUARTIERS" => [],
  "REGIONS" => []];


// To access a value in a row in a particular table, the syntax is var/const_tables[Table[Row[Field]]]
function SetTables(&$var_tables, &$const_tables) {
  // Pulling each variable table (data that changes) into memory
  foreach(array_keys($var_tables) as $table) {
    // Open data file for particular table
    $data_file = fopen("databases/WEBARADIGITAL20230407/$table.TXT", "r");
    // Collect column names
    $header = fgetcsv(fopen("databases/unfilled_txts/$table.TXT", "r"));

    // Array will contain associative arrays which correspond table columns to value for each index/row
    $data = [];
    while (($row = fgetcsv($data_file)) !== false) {
      $data[] = array_combine($header, $row);
    }

    // insert data into var_tables array
    $var_tables[$table] = $data;
  }

  // Pulling each contant table (data that remains constant) into memory
  foreach(array_keys($const_tables) as $table) {
    // Open data file for particular table
    $data_file = fopen("databases/unfilled_txts/$table.TXT", "r");
    // Collect column names
    $header = fgetcsv($data_file);

    // Array will contain associative arrays which correspond table columns to value for each index/row
    $data = [];
    while (($row = fgetcsv($data_file)) !== false) {
      $data[] = array_combine($header, $row);
    }

    // insert data into var_tables array
    $const_tables[$table] = $data;
  }
}


/* Began building a query function, realized it wasn't necessary so stopped working on it
function Query(&$table, $return_columns, $where_columns){
  reset($table);
  next($table);
  $return_data = [];

  while(($row = key($table)) != null) 
  {
    print(key($table));
    $append = true;

    if ($where_columns != null)
    {
      foreach($where_columns as $column => $value) {
        if ($table[$row][$column] != $value) $append = false; break;
      }
    }

    // If a row's columns doesn't match the values in the where columns, move on to the next row
    if ($append == false) next($table); continue;

    $return_row = [];
    foreach($return_columns as $column) {
      $return_row[] = [$column => $table[$row][$column]];
    }

    $return_data[] = $return_row;
    next($table);
  }
  //print_r($return_data);
}
*/


print('hellow world');
SetTables($var_tables, $const_tables);
?>