<?php 
if(isset($_POST['submit']))
{


// getting value from file 
	$data=[
		'process'=>$_POST['process'],
		'enviroment'=>$_POST['enviroment'],
		'type'=>$_POST['type'],
		'baudrate'=>$_POST['baudrate'],
		'parity'=>$_POST['parity'],
		'databits'=>$_POST['databits'],
	];
// Define the filename (replace with your desired filename)

  $filename = "setting/gaugeRead.rsp";

  // Open the file for writing in text mode (a+)
  // - 'a+' opens the file for reading and writing.
  // - If the file doesn't exist, it creates a new file.
  // Check if the file already exists
    if (!file_exists($filename)) {
      // Create the new file with write mode (w)
      $file = fopen($filename, "w") or die("Unable to create file!");
    } else {
      // Open the existing file for writing (w) to clear its contents
      $file = fopen($filename, "w") or die("Unable to open file!");
    }

  // Construct the output string
  $output_string = "";
  foreach ($data as $key => $value) {
    if (is_array($value)) {
      $value = implode(",", $value);  // Join list elements with comma and space
    }
    $output_string .= "$key=$value\n";
  }

  // Write the output string to the file (fseek ensures we write at the end)
  fseek($file, 0, SEEK_END);  // Move file pointer to the end

  fwrite($file, $output_string);

  // Close the file
  fclose($file);

  // Prepare JavaScript alert message (replace with your desired message)
  $alert_message = "Data stored successfully!";

  // Redirect back to your page (replace with your actual page URL)
  echo "<script>alert('$alert_message');</script>";
  header('Location: gauge.php'); // Adjust the URL to your specific page
  exit(); // Stop further script execution after redirect


}

?>