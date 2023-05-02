<?php
class Current {
  private $NO_INSCRIPTION;
  public function set_number($new_number) {
    $this->NO_INSCRIPTION = $new_number;
    return $this->NO_INSCRIPTION;
  }
  public function get_number() {
    return $this->NO_INSCRIPTION;
  }
}

class InscriptionData {
  public $mls_number;
  public $propertyStatus;
  public $propertyType;
  public $address;
  public $content;
  public $price;
  public $size;
  public $land;
  public $bedrooms;
  public $bathrooms;
  public $year;
  public $postal;
  public $showCalculator;
  public $lat;
  public $long;
  public $lat_long;
}

function LogError($message, $short) {
  $time = date('Y-m-d H:i:s');
  $file = fopen('log_file.csv', 'a'); // Open the log file in append mode
  $log = "Error,$message,$time\n"; // Separate fields with commas and end line with a new line character

  fwrite($file, $log); // Write the log entry to the file
  fclose($file); // Close the file
}

function LogWarning($message, $short) {
  $time = date('Y-m-d H:i:s');
  $file = fopen('log_file.csv', 'a'); // Open the log file in append mode
  $log = "Warning,$message,$time\n"; // Separate fields with commas and end line with a new line character

  fwrite($file, $log); // Write the log entry to the file
  fclose($file); // Close the file
}

function LogMessage($message, $short) {
  $time = date('Y-m-d H:i:s');
  $file = fopen('log_file.csv', 'a'); // Open the log file in append mode
  $log = "Message,$message,$time\n"; // Separate fields with commas and end line with a new line character

  fwrite($file, $log); // Write the log entry to the file
  fclose($file); // Close the file
}

function CreateTitle($rowData){
  $NO_CIVIQUE_DEBUT = $rowData["NO_CIVIQUE_DEBUT"];
  $NO_CIVIQUE_FIN = $rowData["NO_CIVIQUE_FIN"];
  $NOM_RUE_COMPLET = $rowData["NOM_RUE_COMPLET"];
  $APPARTEMENT = $rowData["APPARTEMENT"];
  $title = "";

  // Add address number
  if ($NO_CIVIQUE_DEBUT != "" && $NO_CIVIQUE_DEBUT != NULL) {
    $title .= $NO_CIVIQUE_DEBUT . " ";
  } else if ($NO_CIVIQUE_FIN != "" && $NO_CIVIQUE_FIN != NULL) {
    $title .= $NO_CIVIQUE_FIN . " ";
  } else LogWarning("No address number found", "meta_error");

  // Add street name
  if ($NOM_RUE_COMPLET != "" && $NOM_RUE_COMPLET != NULL){
    $title .= $NOM_RUE_COMPLET . " ";
  } else LogWarning("No street name found", "meta_error");

  // Add apartment number
  if ($APPARTEMENT != "" && $APPARTEMENT != NULL) {
    $title .= $APPARTEMENT;
  }
  return $title;
}

// second element in array returns 0 if property for sale and 1 if for rent
function CreatePrice($rowData) {
  if ($rowData["PRIX_DEMANDE"] != "" && $rowData["PRIX_DEMANDE"] != NULL) {
    return [$rowData["PRIX_DEMANDE"], 0];
  } else if ($rowData["PRIX_LOCATION_DEMANDE"] != "" && $rowData["PRIX_LOCATION_DEMANDE"] != NULL) {
    return [$rowData["PRIX_LOCATION_DEMANDE"], 1]; 
  }
  // If not returned by now log warning that there's no price found
  LogWarning("No purchase or rent price found", "meta_error");
  return [NULL, 1];
}

function createSize($rowData, $returnType) {
  $livingSize = $rowData["SUPERFICIE_HABITABLE"];
  $lotSize = $rowData["SUPERFICIE_TERRAIN"];
  $otherSize = $rowData["SUPERFICIE_BATIMENT"];

  $primarySize = NULL;
  $secondarySize = NULL;

  if (!empty($livingSize)) {
    $primarySize = $livingSize;
  }
  if (!empty($lotSize)) {
    if (isset($primarySize)) $secondarySize = $lotSize;
    else $primarySize = $lotSize;
  }
  if (!empty($otherSize)) {
    if (!isset($primarySize)) $primarySize = $otherSize;
  }

  if (!isset($primarySize)) LogWarning("No area size found", "meta_error");
  return ($returnType == "primary") ? $primarySize : $secondarySize;
}

function CreateType($rowData) {
  if ($rowData["GENRE_PROPRIETE"] == 'T') {
    return 164;
  } else return 249;
  // 249 is the code for residential and 164 is the code for land
}
?>