<?php
include './functions.php';

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

  $current = new Current();


// To access a value in a row in a particular table, the syntax is var/const_tables[Table[Row[Field]]]
// If you're wondering why I'm storing this as a multidimensional array rather than just simply storing it in  a db and
//  using a library/extension to query it, it's because there's no certainty that all servers will have the necessary extensions
//  And I want to keep this plugin self contained.
function SetTables() {
  global $var_tables;
  global $const_tables;

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

function PropertyData(){
  global $var_tables;
  global $current;
  $row = NULL;
  $property = new InscriptionData();

  foreach ($var_tables["INSCRIPTIONS"] as $index => $record) {
    if ($record["NO_INSCRIPTION"] == $current->get_number() && !isset($row)) $row = $index;
    else if ($record["NO_INSCRIPTION"] == $current->get_number() && isset($row)) LogError("Duplicate property id found in INSCRIPTIONS table, skipping...", "insert_error");
  }
  if (!isset($row)) {
    LogError("No entry found while looping through PropertyData(), skipping property", "insert_error");
    return NULL;
  }
  $rowData = $var_tables["INSCRIPTIONS"][$row];

  $property->mls_number = $rowData["NO_INSCRIPTION"];
  // property status is for rent if value is 1 and for sale if value is 0
  $property->propertyStatus = CreatePrice($rowData)[1];
  $property->propertyType = CreateType($rowData);
  $property->address = CreateTitle($rowData);
  $property->content = sanitize_text_field($rowData["ADDENDA_COMPLET_A"] . "<br>" . $rowData["ADDENDA_COMPLET_F"]);
  $property->price = sanitize_text_field(CreatePrice($rowData)[0]);
  $property->size = sanitize_text_field(CreateSize($rowData, "primary"));
  $property->land = sanitize_text_field(CreateSize($rowData, "secondary"));
  $property->bedrooms = sanitize_text_field($rowData["NB_CHAMBRES"]);
  $property->bathrooms = sanitize_text_field($rowData["NB_SALLES_BAINS"]);
  $property->year = sanitize_text_field($rowData["ANNEE_CONTRUCTION"]);
  $property->postal = $rowData["CODE_POSTAL"];
  // display calculator if property is for sale
  $property->showCalculator = CreatePrice($rowData)[1];

  return $property;
}


function InsertProperty() 
{
  $property = PropertyData();
  // If property is null, skip and move on to the next
  if (!isset($property)) return 1;

  $post_data = [
    'post_title' => $property->address,
    'post_content' => $property->content,
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
    'fave_property_id' => $property->mls_number,
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

  // insert post in wp database
  $post_id = wp_insert_post($post_data);
  LogMessage("Inserted property, post id is: $post_id", "inserted_property");
 
  // insert post metadata in database
  foreach ($meta_data as $key => $value) {
    update_post_meta($post_id, $key, $value, true);
  }

  // Add property status
  if ($property->propertyStatus == 0) {
    $result = wp_set_object_terms( $post_id, 122, 'property_status' );
    if (is_wp_error($result)) {
      logError($result->get_error_message(), 'wp_error');
  }
  } else if ($property->propertyStatus == 1) {
    $result = wp_set_object_terms( $post_id, 121, 'property_status' );
    if (is_wp_error($result)) {
      logError($result->get_error_message(), 'wp_error');
  }
  } else LogWarning('No property status set', 'meta_error');

  // Add property type
  $result = wp_set_object_terms( $post_id, $property->propertyType, 'property_type' );
  if (is_wp_error($result)) {
    logError($result->get_error_message(), 'wp_error');
}

}

SetTables($var_tables, $const_tables);
$current->set_number(19656583);
InsertProperty();
//PropertyData(27976636);
?>