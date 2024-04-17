<?php

// Assuming the file is named "readNetwork.json"

// Check if the file exists
if (file_exists("readNetwork.json")) {

  // Open the file for reading in text mode (r)
  $file = fopen("readNetwork.json", "r") or die("Unable to open file!");

  // Read the entire contents of the file into a string
  $json_data = fread($file, filesize("readNetwork.json"));

  // Close the file
  fclose($file);

  // Decode the JSON string into a PHP object
  $data = json_decode($json_data, true); // Set associative array (true)

  // Now you can access data using keys:
  $network_type = $data['ipsetting'];
  $ip = $data['ip'];
  $subnetmask = $data['subnetmask'];
  $gateway = $data['gateway'];
  $dns1 = $data['dns1'];
  $dns2 = $data['dns2'];

  // Or access all data using the variable:
  $all_data = $data;
     // ... (your logic using the data)

} else {
  // Handle the case where the file doesn't exist
  echo "Error: File 'readNetwork.json' not found!";
}

?>