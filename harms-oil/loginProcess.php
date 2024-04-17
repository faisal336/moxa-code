<?php session_set_cookie_params(1800);    
session_start();
// Open the file for reading in text mode (r)

if (file_exists("readNetwork.json")) {

  $file = fopen("login.json", "r") or die("Unable to open file!");

  // Read the entire contents of the file into a string
  $json_data = fread($file, filesize("login.json"));

  // Close the file
  fclose($file);

  // Decode the JSON string into a PHP object
  $data = json_decode($json_data, true); // Set associative array (true)
	/*Getting email from user*/


  $user_email=$_POST['email'];
  $password=$_POST['password'];
  /*checking the user emails*/
  if($user_email==$data['email'] && $password==$data['password']) {
  		$_SESSION['email']=$user_email;
  		$_SESSION['password']=$password;
      $_SESSION['warning']=''; 
  		header('location:index.php');
  }
  else {
     	$_SESSION['warning']='Wrong Credentials';
      header('location:login.php');
  }

  // Now you can access data using keys:

  
  

}
else {
	header('location:login.php');
}
?>