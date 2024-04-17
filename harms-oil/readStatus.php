<?php
$asp_content = file_get_contents('setting/showLastGaugeRead.rsp');

// Extract lines of data from ASP content
$lines = explode("\n", $asp_content);

// Initialize an empty array to store tank data
$tank_data = [];
$first_iteration = true;

foreach ($lines as $line) {
  if ($first_iteration) {
    // Perform actions specific to the first iteration (optional)
    $first_iteration = false; // Set flag to false for subsequent iterations
  } else {
    // Remove extra whitespaces and split data by spaces
    $data = preg_split('/\s+/', trim($line));

    // If the data line contains information (not header or empty line)
    if (count($data) >= 6) {
      // Extract tank details
      $tank = [
        'tank' => $data[0],
        'product' => $data[1],
        'gallons' => $data[2],
        'inches' => $data[3],
        'water' => $data[4],
        'temperature' => $data[5],
        'ullage' => $data[6]
      ];

      // Add tank details to tank data array
      $tank_data[] = $tank;
    }
  }
}

// Generate HTML table
$html_table = '<table border="1" class="table small border mb-3">';
$html_table .= '<tr>
  <th>TANK</th>  
  <th>PRODUCT</th>
  <th>GALLONS</th>
  <th>INCHES</th>
  <th>WATER</th>
  <th>DEG F</th>
  <th>ULLAGE</th>
</tr>';

foreach ($tank_data as $tank) {
  $html_table .= '<tr>';
  $html_table .= '<td>' . $tank['tank'] . '</td>';
  $html_table .= '<td>' . $tank['product'] . '</td>';
  $html_table .= '<td>' . $tank['gallons'] . '</td>';
  $html_table .= '<td>' . $tank['inches'] . '</td>';
  $html_table .= '<td>' . $tank['water'] . '</td>';
  $html_table .= '<td>' . $tank['temperature'] . '</td>';
  $html_table .= '<td>' . $tank['ullage'] . '</td>';
  $html_table .= '</tr>';
}
$html_table .= '</table>';

// Display HTML table
// echo $html_table;

?>  