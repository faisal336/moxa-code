<?php session_set_cookie_params(3600);
    session_start();
  include('readStatus.php');
  include('functions.php');
  if (isset($_SESSION['email']) && $_SESSION['email']!="") {
   
  }
  else {
    $_SESSION['message']='Your are not authorized to access the page';
    header('location:login.php'); // Redirect to a welcome page
  }
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <!--Getting page title in a variable title  -->
  <?php $title="Show Status";?> 

   <!-- Including Head -->
  <?php include('layouts/head.php'); ?>
   <!-- Ending Include File --> 
  
  <body>
    <!-- Including Header -->
    <?php include('layouts/sidebar.php') ?>
    <!-- End include Header -->
   <!-- Content Section of the admin panel -->
    <section class="home-section ">
      
      <div class="page-content section-padding ">
        
        <!-- **********Tabs Bar ********** -->
        <div class="container-fluid">
            <h2 class="home-heading">FTMS (Fuel Tanks Monitoring System) Web Admin Interface</h2>
            <div class="col-md-12">
                <nav>
                  <ul>
                    <li><a href="#" data-section="status" onclick="showContent('status')">Show Status</a></li>
                    <li><a href="#" data-section="history" onclick="showContent('history')">Read History</a></li>
                    <li><a href="#" data-section="shutdown" onclick="showContent('shutdown')">Shut Down</a></li>
                    <li><a href="#" data-section="restart" onclick="window.location.reload()">Restart</a></li>
                  </ul>
                </nav>
            </div>
        </div>
          
         <!--*******End Tabs Menu********-->

          <!-- ******** Content Section***** -->
          <div class="container-fluid"> 
            <div class="col-md-12"> 

                  <div id="statusContent" class="content-section" >

                      <div class="show-border" style="margin-top: -10px;border-top: none; margin-bottom: 10px; padding-top: 20px;" >
                                    <h2 style="margin-left:20px;">Read Status</h2>
                                        <!-- *****Top Reading****** -->
                                        <div class="row">
                                           <div class="col-md-2">
                                             <div class="box">
                                                  <p style="color:#404D61;">Raspberry Pi Device Status</p>
                                                  <span style="margin-left: 100px;">
                                                    <?php 
                                                     function ping($host) {
                                                  $output = array();
                                                  $result = exec("ping -n 1 -w 1 $host", $output, $return_var);

                                                  // Check if the command executed successfully (return code of 0)
                                                  if ($return_var === 0) {
                                                    return true; // Host is online
                                                  } else {
                                                    // Handle potential errors (e.g., failed execution, host unreachable)
                                                    // Check the specific return code for more granular error handling
                                                    // (e.g., https://www.tldp.org/HOWTO/Bash-Prog-Lang/html/bash-exit.html)
                                                    return false; // Generic "unreachable" or error occurred
                                                  }
                                                }

                                                $IP =  $_SERVER['SERVER_ADDR']; 

                                                if (ping($IP)) {
                                                  echo "Online";
                                                } else {
                                                  echo "Offline";
                                                  // Optional: Consider logging the specific error code for debugging
                                                }
                                                     ?>
                                                  </span>
                                             </div>
                                           </div>
                                           <div class="col-md-2">
                                             
                                           </div>
                                           <div class="col-md-2">
                                                <div class="box">
                                                  <p style="color:#404D61;">IP Address</p>
                                                  <span style=""><?php echo $IP ?></span>
                                                </div>
                                           </div>
                                           <div class="col-md-2">
                                             
                                           </div>
                                           <div class="col-md-2">
                                                <div class="box">
                                                  <p style="color:#404D61;">Gauge Readings</p>
                                                  <p class="gauge-reading">HARMSOIL-INVMANAGED-1103.simplelogix.com</p>
                                                </div>
                                           </div>
                                        </div>
                                        <!-- *****End Top Readin**** -->
                                        
                                        <!--******Reading Boxes Row***** -->
                                
                                        <!-- ****End Reading Boxes Row***** -->
                                       <br>      
                        </div>
                        <div style="margin-top:50px;">
                            <?php  echo $html_table ?>
                        </div>
                          
             
                  </div>
                  <div id="historyContent" class="content-section" >
                    <div class="show-border" style="margin-top: -10px; border-top: none; margin-bottom: 10px; padding-top: 20px;" >
                                     <!-- *****Top Reading****** -->
                                     <h2 style="margin-left:20px;">Read History</h2>
                                     <div class="row">

                                        <div class="col-md-2">

                                          <div class="box">
                                               <p style="color:#404D61;">Raspberry Pi Device History</p>
                                               <span style="margin-left: 100px;">
                                                 <?php 
                                            

                                             $IP =  $_SERVER['SERVER_ADDR']; 

                                             if (ping($IP)) {
                                               echo "Online";
                                             } else {
                                               echo "Offline";
                                               // Optional: Consider logging the specific error code for debugging
                                             }
                                                  ?>
                                               </span>
                                          </div>
                                        </div>
                                        <div class="col-md-2">
                                          
                                        </div>
                                        <div class="col-md-2">
                                             <div class="box">
                                               <p style="color:#404D61;">IP Address</p>
                                               <span style=""><?php echo $IP ?></span>
                                             </div>
                                        </div>
                                        <div class="col-md-2">
                                          
                                        </div>
                                        <div class="col-md-2">
                                             <div class="box">
                                               <p style="color:#404D61;">Gauge Readings</p>
                                               <p class="gauge-reading">HARMSOIL-INVMANAGED-1103.simplelogix.com</p>
                                             </div>
                                        </div>
                                     </div>
                                     <!-- *****End Top Readin**** -->
                                     
                                     <!--******Reading Boxes Row***** -->
                                    
                                     <!-- ****End Reading Boxes Row***** -->
                                    <br>       
                          </div> 
                          <div style="margin-top: 50px;">
                            <?php  echo $html_table ?>  
                          </div>  
                          <br>
                          <br>
                          <br>
                            
                       </div>
                  <div id="shutdownContent" class="content-section" >
                      
                        <table class="table">
                            <tr>
                                <th>PID</th>
                                <th>Name</th>
                                <th>Time</th>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>New Tank</td>
                                <td>12:00PM</td>
                            </tr>
                        </table>
                  </div>
                  <div id="shutdownContent" class="content-section" >
                      
                        <table class="table">
                            <tr>
                                <th>PID</th>
                                <th>Name</th>
                                <th>Time</th>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>New Tank</td>
                                <td>12:00PM</td>
                            </tr>
                        </table>
                  </div>
            </div>         
            
          
          </div>
          
           <!-- *****End Nav Section******* --> 

      </div>
      
      <!-- ******Footer Start******* -->
            <?php  include('layouts/footer.php'); ?>
        <!-- *******Footer End********-->
    </section> 
   <!-- Include foot file contain all the scripts -->
    <?php  include('layouts/foot.php') ?>
    <!-- End include footer file -->
    <script>
        $(".content-section").hide();
      function showContent(section) {
    
          
          $(".content-section").hide();
          
          $("#" + section + "Content").show();

          // Remove the 'active' class from all tabs
          $(".nav li").removeClass("active");

          // Add the 'active' class to the clicked tab using the data attribute
          $("li[data-section='" + section + "']").addClass("active");
      }
  </script>
  </body>

</html>
