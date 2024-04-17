<?php 

if(isset($_POST['submit']))
{

if($_POST['ipsetting']=='on') {
  
   // getting value from file 
    $data=[
      'ipsetting'=>$_POST['ipsetting'],
      'ip'=>$_POST['ip'],
      'subnetmask'=>$_POST['subnetmask'],
      'gateway'=>$_POST['gateway'],
       'dns1'=>$_POST['dns1'],
       'dns2'=>$_POST['dns2'],
      
    ];

   // Define the filename (replace with your desired filename)

     $filename = "readNetwork.json";
      
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
    $data= json_encode($data);
     file_put_contents($filename, $data);
     // foreach ($data as $key => $value) {
     //   if (is_array($value)) {
     //     $value = implode(",", $value);  // Join list elements with comma and space
     //   }
     //   $output_string .= "$key=$value\n";
     // }

     foreach ($data as $key => $value) {
       fwrite($file, "$key=$value\n");
     }
     
     // // Write the output string to the file (fseek ensures we write at the end)
     // fseek($file, 0, SEEK_END);  // Move file pointer to the end

     // Close the file
     fclose($file);

     // Prepare JavaScript alert message (replace with your desired message)
     $alert_message = "Data stored successfully!";

     // Redirect back to your page (replace with your actual page URL)
     echo "<script>alert('$alert_message');</script>";
     header('Location: network.php'); // Adjust the URL to your specific page
     exit(); // Stop further script execution after redirect

}
else {

    // Generate the command to get IP configuration
      $command = 'ipconfig';



      // Execute the command
      exec($command, $output);

      // Parse the output to extract IP address, subnet mask, and gateway
      $ipAddress = null;
      $subnetMask = null;
      $gateway = null;

      foreach ($output as $line) {
          // Match IPv4 address
          if (preg_match('/IPv4 Address[^\:]*:\s+([^\s]+)/', $line, $matches)) {
              $ipAddress = $matches[1];
          }
          // Match Subnet Mask
          if (preg_match('/Subnet Mask[^\:]*:\s+([^\s]+)/', $line, $matches)) {
              $subnetMask = $matches[1];
          }
          // Match Default Gateway
          if (preg_match('/Default Gateway[^\:]*:\s+([^\s]+)/', $line, $matches)) {
              $gateway = $matches[1];
          }
      }
      // Read the JSON file
      $jsonFile = 'readNetwork.json';
      $jsonData = file_get_contents($jsonFile);

      // Decode JSON data into an associative array
      $data = json_decode($jsonData, true);

       
      // Update the value of "ipsetting" to "off"
      $data['ipsetting'] = 'off';

      // Encode the updated data back to JSON
      $updatedJsonData = json_encode($data, JSON_PRETTY_PRINT);

      // Write the updated JSON data back to the file
      file_put_contents($jsonFile, $updatedJsonData);

     // Prepare JavaScript alert message (replace with your desired message)
     $alert_message = "Data stored successfully!";

      // Redirect back to your page (replace with your actual page URL)
     echo "<script>alert('$alert_message');</script>";
     header('Location: network.php'); // Adjust the URL to your specific page
     exit(); // Stop further script execution after redirect

}

}


 ?>
