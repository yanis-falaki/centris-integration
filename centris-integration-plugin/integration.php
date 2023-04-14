<?php
class InscriptionData {
  public $title;
  public $content;
  public $price;
  public $size;
  public $land;
  public $bedrooms;
  public $bathrooms;
  public $year;
  public $postal;
  public $address;
  public $showCalculator;
  public $lat;
  public $long;
  public $lat_long;
}

// For development purposes an absolute path in windows style notation must be used as the plugin is a symlink and LocalWP is using a windows based shell
$path = "D:\Local Repo\local-sites\jb-staging\app\public\wp-load.php";
//$path = "/mnt/d/Local Repo/local-sites/jb-staging/app/public/wp-load.php";

// Load WordPress
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


function PropertyData($NO_INSCRIPTION){
  global $var_tables;
  $row = -1;
  $property = new InscriptionData();

  foreach ($var_tables["INSCRIPTIONS"] as $index => $record) {
    if ($record["NO_INSCRIPTION"] == $NO_INSCRIPTION && $row = -1) $row = $index;
    else if ($record["NO_INSCRIPTION"] == $NO_INSCRIPTION && $row != -1) echo "Duplicate of $NO_INSCRIPTION found in INSCRIPTIONS table";
  }
  if ($row == -1) "No entry for $NO_INSCRIPTION found while looping through PropertyData()";
  $rowData = $var_tables["INSCRIPTIONS"][$row]

  
}


function InsertProperty($NO_INSCRIPTION) 
{
  $property = new InscriptionData();

  $post_data = [
    'post_title' => '',
    'post_content' => '',
    'post_status' => 'publish',
    'post_author' => 1,
    'post_type' => 'property'
  ];

  $meta_data = [
    'fave_property_price' => $property->price,
    'fave_property_size' => $property->size,
    'fave_property_size_prefix' => 'sqft',
    'fave_property_land' => $property->land,
    'fave_property_land_postfix' => 'sqft',
    'fave_property_bedrooms' => $property->bedrooms,
    'fave_property_bathrooms' => $property->bathrooms,
    'fave_property_year' => $property->year,
    'fave_property_id' => $NO_INSCRIPTION,
    'fave_property_zip' => $property->postal,
    'fave_additional_features_enable' => 'enable',
    'fave_featured' => 1,
    'fave_loggedintoview' => 0,
    'fave_mortgage_cal' => $property->showCalculator,
    'fave_payment_status' => 'not_paid',
    'fave_agent_display_option' => 'author_info',
    'fave_property_map_address' => $property->address,
    'fave_property_address' => $property->address,
    'houzez_geolocation_lat' => $property->lat,
    'houzez_geolocation_long' => $property->long,
    'fave_property_location' => $property->lat_long,
    'fave_property_map' => 1,
  ];

  $post_id = wp_insert_post($post_data);


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


SetTables($var_tables, $const_tables);
PropertyData(27976636);
?>