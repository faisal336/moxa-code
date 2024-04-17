<?php session_start();
  include('readStatus.php');
  // Set the duration for which the message should be displayed (in seconds)
$messageDuration = 60; // Change this to your desired duration

// Set the timestamp when the message was set
$_SESSION['warning_time'] = time();
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <!--Getting page title in a variable title  -->
  <?php $title="Show Status";?> 

   <!-- Including Head -->
  <?php include('layouts/head.php'); ?>
   <!-- Ending Include File --> 
  <style>
    @media only screen and (max-width: 600px) {
      .login-input {
        width: 250px  !important;
        height: 60px;
      }
      .login-logo {
        width: 200px  !important;
        height: 60px;
      }
    }
    .btn-color {
        background-color: #000000;
    }
     
    .row {
        justify-content: center; /* Center elements horizontally within the row */
      align-items: center; /* Center elements vertically within the row */
    }
    .login-input {
      width: 600px;
      height: 60px;
    }
    .login-footer {
      font-size: 22px;
      text-align: center;
      margin-top: 80px;
    }
    .remember-me {
      font-size: 28px;
      text-align: center;
      margin-top: 20px;
    }
    .remember {
      height: 20px;
      width: 20px;
    }
    .copy-right {
      font-size: 23px;
      padding-top: 15px;
      margin-right: 5px;
    }
  </style>
  <body>
    <div class="container-fluid align-items-center" >
      <!-- Login Form -->
        <div class="container  ">
           <div class="row "  style="margin-top:20%;">
              <div class="col-md-12 d-flex justify-content-center">
                 <form action="loginProcess.php" method="post"  >
                      <?php 
                      $message="";
                      $currentTime = time();
                             $messageTime = $_SESSION['warning_time'];
                      if (($currentTime - $messageTime) < $messageDuration) { /*checking the Session Message Time*/
                      if(isset($_SESSION['warning'])) {
                        
                        if($_SESSION['warning']!=''){
                          echo "<div class='alert alert-danger' role='alert' >";
                            $message=$_SESSION['warning'];
                            echo $message;
                             echo "</div>";
                        }
                        else {

                        }
                      
                      }
                      else {

                      }

                    } 
                    /*Session Time Edn*/   
                      ?>
                      <center>
                          <img class="mt-2 mb-2 login-logo" src="images/logo.png" alt="" >
                      </center> 
                      
                      <div class="form-groups login-input">
                          <input type="text" id="login" name="email" value="admin@gmail.com" class="form-control fadeIn h-100 mt-3" name="login" placeholder="Email" >  
                      </div>
                      <div class="form-groups login-input">
                          <input type="password" id="password" name="password" class="form-control fadeIn mt-3 h-100" value="admin123" name="password" placeholder="password">  
                      </div>
                      <div class="remember-me">
                            <!-- Add Remember Me checkbox -->
                            <input type="checkbox" name="remember" class="remember"> Remember Me
                      </div>
                      <div >
                          <input type="submit" class="fadeIn fourth btn btn-primary w-100 btn-color mt-3 " value="Log In" style="height: 60px;">    
                      </div>
                      <div  class="login-footer">
                          <p><span class="copy-right">&copy;</span>Harms Oil. All rights Reserved.</p>
                          <p>(Powered by <a href="https://www.simplelogix.com/">SimpleLogix LCC)</a></p>
                      </div>

                </form>
              </div>                 
                   
               
           </div>
        </div>  
           
    </div>
    <style>
        .footer {
        background-color: #000000;
        color: white;
        padding: 10px;
        text-align: center;
        margin-top: auto;
        position: absolute;
        bottom: 0;
        width: 100%;
        margin-bottom: 0px;
    }
    </style>    
     <!-- ******Footer Start******* -->
            <!-- <footer class="footer">
                <p>© 2024 Harms Oil. All rights reserved. |
                    Designed by <a href="https://www.simplelogix.com/" target="_blank">Simple Logix</a></p>
            </footer> -->
        <!-- *******Footer End********-->
  </body>
  
</html>
