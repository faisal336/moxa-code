<?php 
// Read the ASP file
$asp_content = file_get_contents('setting/gaugeRead.rsp');

// Explode the content into lines

// $asp_content=trim($asp_content);

$lines = explode("\n", $asp_content);
// $lines=implode("=", $lines);
// Loop through each line

foreach ($lines as $line) {
    // Skip empty lines
    if (empty($line)) {
        continue;
    }

    // Split the line into key and value
    list($key, $value) = explode('=', $line, 2);

    // Trim whitespace from key and value
    $key = trim($key);
    $value = trim($value);

    // Store key-value pair in the data array
    $data[$key] = $value;

    }

// $environment = explode("=",$lines[1]);
// var_dump($process['process']);

// var_dump($environment[1]);

// Initialize variables to store data

if (!empty($data)) {
$process=$data['process'];
$environment =$data['enviroment'];
$type=$data['type'];
$frequency=$data['frequency'];
$baudrate = $data['baudrate'];
$parity =$data['parity'];
$databits = $data['databits'];

}
else {
    die('Something Went Wrong! No Data Found in Read Gauge File');
}

?>