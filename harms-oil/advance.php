<?php session_start(); 
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
      <div class="container mt-1">
        <div class="row">
            <div class="col-12">
                <h4>Common Settings for Raspberry Pi Gauge Monitor</h4>
                <h5 class="mt-3">Gauge Monitor Maintenance Mode (On/Off)</h5>

                <form>
                    <div class="row" > 
                        <div class="form-check " style="margin-left:20px">
                            <input class="form-check-input" type="radio" value="0" id="modeOff" name="maintenance_mode">
                            <label class="form-check-label" for="modeOff">
                                Off
                            </label>
                        </div>
                        <div class="form-check " style="margin-left: 10px">
                            <input class="form-check-input" type="radio" value="1" id="modeOn" name="maintenance_mode">
                            <label class="form-check-label" for="modeOn">
                               On
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary theme-background px-5 mt-3 ip-setting-save">Save</button>
                </form>
            </div>
        </div>
    </div>
    <!-- ******Footer Start******* -->
            <?php  include('layouts/footer.php'); ?>
        <!-- *******Footer End********-->
    </section>
    <!-- End Content Section  -->

   <!-- Include foot file contain all the scripts -->
    <?php  include('layouts/foot.php') ?>
    <!-- End include footer file -->
  </body>

</html>
