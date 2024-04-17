<?php session_set_cookie_params(3600);
session_start();
include('setting/readGaugeProcess.php');
include('readNetworkProcess.php');
if (isset($_SESSION['email']) && $_SESSION['email']!="") {
   
  }
else {
    $_SESSION['message']='Your are not authorized to access the page';
    header('location:login.php'); // Redirect to a welcome page
  }

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <!-- Including Head -->
  <?php include('layouts/head.php'); ?>
   <!-- Ending Include File --> 
  
  <body>
    <!-- Including Header -->
    <?php include('layouts/sidebar.php') ?>
    <!-- End include Header -->
    <!-- Content Section of the admin panel -->
    <section class="home-section section-padding">
            <div class="col-md-12">
                <h4 class="mt-1 border-warning pb-2 gauge-config">Gauge Configurations</h4>
            </div>
         
        <!-- *******Form Starts********-->
        <form action="setting/storeGaugeProcess.php" method="post">
            <div class="container-fluid" >
                <div class="col-md-12 border" style="padding:30px 10px 30px 10px">
                             <div class="row">
                                 <!-- ****Start Gauge Process -->
                                     <div class="col-md-3 mt-3">
                                             <p>Gauge Process</p>
                                             <div class="toggle">
                                                 <input type="hidden" name="process" value="0">
                                                 <input type="checkbox" name="process" value="<?php echo $process ?>" onclick="this.value = this.checked ? 1 : 0;" <?php echo $process==1?'checked':''; ?>/>
                                                 <label></label>
                                             </div>
                                     </div>   
                                 <!-- *****End Gauge Process -->
                                 <hr>
                                 <!-- ******Div For Enviroment *********** -->
                                     <div class="col-md-3 mt-3 mt-3">
                                             <p>Enviroment</p>
                                             <div class="toggle">
                                                 <input type="hidden" name="enviroment" value="0">
                                                 <input type="checkbox" name="enviroment" value="<?php echo $environment ?>" onclick="this.value = this.checked ? 1 : 0;" <?php echo $environment==1?'checked':''; ?>/>
                                                 <label></label>
                                             </div>
                                         
                                     </div>                        
                                 <hr>
                                 <!-- ******END Enviroment Div****** -->
                                 <!-- ******Gauge Start******* -->
                                 <div class="col-md-3 mt-3">
                                     <p>Gauge Type</p>
                                     <select id="type" name="type" class="form-select select gauge-select-full form-control" style="width:auto;" >
                                             <option value="EBW" <?php echo $type=='EBW'?'selected':'' ?>>EBW</option>
                                             <option value="TLS350" <?php echo $type=='TLS350'?'selected':'' ?>>TLS350</option>
                                             <option value="CENTERON" <?php echo $type=='CENTERON'?'selected':'' ?>>CENTERON</option>
                                             <option value="INCON" <?php echo $type=='INCON'?'selected':'' ?>>INCON</option>
                                             <option value="TLS250" <?php echo $type=='TLS250'?'selected':'' ?>>TLS250</option>               
                                     </select>

                                 </div>
                                 <!-- ******End Gauge******-->
                                                                  <!-- ******Gauge Start******* -->
                                 <div class="col-md-3" >
                                     <select id="type" name=" frequency" class="form-select select gauge-select-full form-control" style="width:auto;margin-top:47px;" >
                                             <option value="1200" <?php echo $frequency=='1200'?'selected':'' ?>>1200</option>
                                             <option value="9600" <?php echo $frequency=='9600'?'selected':'' ?>>9600</option>
                                             <option value="115200" <?php echo $frequency=='115200'?'selected':'' ?>>115200</option>
                                             <option value="57600" <?php echo $frequency=='57600'?'selected':'' ?>>57600</option>
                                             <option value="38400" <?php echo $frequency=='38400'?'selected':'' ?>>38400</option>               
                                     </select>

                                 </div>
                                 <!-- ******End Gauge******-->
                             </div>
                  </div>
            </div>
            <div class="col-md-12" style="margin-top:30px;">
                    <h4 class="gauge-port">Gauge Port Setup</h4>    
            </div>
              
            <div class="container-fluid" >
                <div class="col-md-12 border" style="padding:30px 10px 30px 10px">
                        <div class="row">
                            <div class="col-md-4 mt-3">
                                <p>Baud Rate</p>
                               
                                <select name="baudrate"  class="form-select select form-control" style="width:auto;">
                                            <option value="1200" <?php echo $baudrate=='1200'?'selected':'' ?>>1200</option>
                                            <option value="9600" <?php echo $baudrate=='9600'?'selected':'' ?>>9600</option>
                                            <option value="115200" <?php echo $baudrate=='115200'?'selected':'' ?>>115200</option>
                                            <option value="57600" <?php echo $baudrate=='57600'?'selected':'' ?>>57600</option>
                                            <option value="38400" <?php echo $baudrate=='38400'?'selected':'' ?>>38400</option>
                                            <option value="19200" <?php echo $baudrate=='19200'?'selected':'' ?>>19200</option>
                                </select>
                            </div>
                            <div class="col-md-4 mt-3" >
                                <p>Parity</p>
                                <select id="parity" name="parity" class="form-select select gauge-select form-control" style="width:auto;">
                                    <option value="Even" <?php echo $parity=='Even'?'selected':'' ?>>Even</option>
                                    <option value="ODD" <?php echo $parity=='ODD'?'selected':'' ?>>ODD</option>

                                        <?php
                                        foreach($parity as $val) {
                                            echo "<option value='$val'>$val</option>";
                                         }
                                        ?>
                                </select>
                            </div>
                            <div class="col-md-4 mt-3">
                                <p>Data Bits</p>
                                <select id="databits" name="databits" class="form-select select gauge-select form-control" style="width:auto;">
                                    <option value="7" <?php echo $databits=='7'?'selected':'' ?>>7</option>
                                    <option value="8" <?php echo $databits=='8'?'selected':'' ?>>8</option>
                                        
                                </select>
                            </div>    
                        </div>
                  
                    <div class="row mt-5">
                        <div class="col-md-12">
                            <center class="mt-3">
                                <button type="submit" name="submit" class="btn btn-primary col-md-4 mt-2 theme-background px-5 mt-3 ip-setting-save" style="">Save</button>
                            </center>
                                
                          
                        </div>
                    </div>
                </div>    
            </div>
            <br>
        </form>
        <!-- *******Form Ends*******-->
        <br>
        <br>
        <!-- ******Footer Start******* -->
            <?php  include('layouts/footer.php'); ?>
        <!-- *******Footer End********-->
    </section>
    <!-- End Content Section  -->
   <!-- Include foot file contain all the scripts -->
    <?php  include('layouts/foot.php') ?>
    <!-- End include footer file -->

    <script>
    const selectAllButton = document.getElementById('selectAll');
    const optionsSelect = document.getElementById('options');
    const form = document.querySelector('form');

    selectAllButton.addEventListener('click', function() {
      const selectedOption = optionsSelect.value;
      
      if (confirm(`Are you sure you want to apply "${selectedOption}" to all?`)) {
        // Submit the form (assuming form submission applies to all)
        form.submit();
      }
    });

    </script>
  </body>

</html>
