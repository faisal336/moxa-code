<?php  session_set_cookie_params(3600);
session_start();
include('readNetworkProcess.php');
// if (isset($_SESSION['email']) && $_SESSION['email']!="") {
   
//   }
//   else {
//     $_SESSION['message']='Your are not authorized to access the page';
//     header('location:login.php'); // Redirect to a welcome page
//   }
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
    <section class="home-section  section-padding">
        
            <div class="col-md-12">
                <h2 class="mt-1">Network Setting</h2>
            </div>
            <div class="container-fluid mt-3" >
                <div class="col-md-12 border" style="padding:30px 10px 30px 10px;">
                        <h5>IP Settings (Automatic/Manual)</h5>
                        <form  action="storeNetworkProcess.php" method="POST">
                            <div class="row">
                                <div class="form-check" style="margin-left: 15px;">
                                    <input class="form-check-input" type="radio" name="ipsetting" value="off" id="dhcpRadio" <?php 
                                    if($network_type=='off') {
                                         echo 'checked'; 
                                    }
                                    else {
                                        echo ''; 
                                    }
                                    

                                ?>>
                                    <label class="form-check-label" for="dhcpRadio" >
                                        DHCP (Automatic)
                                    </label>
                                </div>

                                <div class="form-check" style="margin-left: 15px;">
                                    <input class="form-check-input" type="radio" value="on" name="ipsetting"  id="staticRadio" <?php 
                                    if($network_type=='on') {
                                         echo 'checked'; 
                                    }
                                    else {
                                        echo ''; 
                                    }
                                    

                                ?>>
                                    <label class="form-check-label" for="staticRadio">
                                        Static (Manual)
                                    </label>
                                </div>    
                            </div>

                            <div id="manualSettings" class="d-none">
                                <!-- Manual IP settings with Bootstrap classes -->
                                <div class="static-network-settings">
                                    <div class="row mt-2">
                                        <div class="col-md-4">
                                            <label class="strong-text">IP Address</label>
                                            <input type="text" class="form-control" placeholder="Enter IP Address (e.g., 123.99.99.99)" name="ip" id="ip" pattern="^[0-9]{1}[0-9]{2}\.[0-9]{2}\.[0-9]{2}\.[0-9]{2}$"  oninput="formatIpAddress(this)">
                                            <small class="form-text text-muted">Format: 123.99.99.99</small>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="strong-text">Subnetwork Mask</label>
                                            <input type="text" class="form-control" placeholder="Enter Subnetwork Mask" name="subnetmask" id="subnetmask"  oninput="formatIpAddress(this)">
                                            <small class="form-text text-muted">Format: 123.99.99.99</small>
                                        </div>
                                        <div class="col-md-4">
                                                <label class="strong-text">Gateway</label>
                                                <input type="text" class="form-control" placeholder="Enter Gateway" name="gateway" id="gateway"  oninput="formatIpAddress(this)">
                                                <small class="form-text text-muted">Format: 123.99.99.99</small>
                                        </div>
                                        <!-- Add more columns for additional settings -->
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-4">
                                            <label class="strong-text">DNS 1</label>
                                            <input type="text" class="form-control" placeholder="Enter DNS 1" name="dns1" id="ip" >
                                            <small class="form-text text-muted">Format: exmaple1.simplelogix.com</small>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="strong-text">DNS 2</label>
                                            <input type="text" class="form-control" placeholder="Enter DNS 2" name="dns2" id="subnetmask"  >
                                            <small class="form-text text-muted">Format: exmaple2.simplelogix.com</small>
                                        </div>
                                        
                                        <!-- Add more columns for additional settings -->
                                    </div>
                                </div>
                            </div>

                            <button type="submit" name="submit" class="btn btn-primary theme-background px-5 mt-3 ip-setting-save">Save</button>
                        </form>
                </div>
            </div>
           
            <div class="container-fluid mt-3" >
                    <div class="col-md-12 " >
                        <h2 class="mt-1 border-warning pb-2">Saved Network Configurations Settings</h2>
                    </div>
            </div>

            <div class="container-fluid mb-5" >
                    <div class="col-md-12 border" style="padding:30px 10px 30px 10px;">
                        <div class="row">
                            <div class="col-md-3">
                               <p class="network-config-p">Network Configurations Type</p>
                               <span class="network-config"><?php

                                    if($network_type=='on') {
                                         echo 'Static Manual'; 
                                    }
                                    else {
                                        echo "DHCP (Automatic)"; 
                                    }
                                    ?></span> 
                            </div>
                            <div class="col-md-3">
                               <p class="ip-config-p">IP Address</p>
                               <span class="ipaddress">
                                
                                
                                        <?php
                                        if($network_type=='on') {
                                            echo $ip;
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

                                            echo $ipAddress;   
                                        }

                                           
                     
                                ?>

                                    
                                </span> 
                            </div>
                            <div class="col-md-3">
                                <p class="subnetmask-config-p">Subnetwork Mask</p>
                                <span class="subnetmask">
                                    <?php 
                                    if($network_type=='on') {
                                         echo $subnetmask; 
                                    }
                                    else {
                                        echo $subnetMask; 
                                    }
                                    ?>
                                    </span>
                            </div>
                            <div class="col-md-3">
                                <p class="network-config-p">Gateway</p>
                                <span class="gateway">
                                    <?php 
                                    if($network_type=='on') {
                                      echo $gateway; 
                                    }
                                    else { 
                                        echo $gateway; 
                                    }
                                    ?>
                                    </span>
                            </div>  
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-3">
                               <p class="network-config-p">DNS 1</p>
                               <span class="network-config"><?php

                                    if($network_type=='on') {
                                         echo $dns1; 
                                    }
                                    else {
                                        echo "-"; 
                                    }
                                    ?></span> 
                            </div>
                            <div class="col-md-3">
                               <p class="ip-config-p">DNS 2</p>
                               <span class="ipaddress">
                                <?php if($network_type=='on') {
                                         echo $dns2; 
                                    }
                                    else {
                                        echo "-"; 
                                    }
                                    ?></span>
                                </span> 
                            </div>
                            
                        </div>
                    </div>
            </div>
         </div>
     
		<br>
        <br>

        <!-- ******Footer Start******* -->
            <?php  include('layouts/footer.php'); ?>
        <!-- *******Footer End********-->

        <script>
            // Function to toggle the visibility of manual settings
            function toggleManualSettings() {
                const manualSettings = document.getElementById('manualSettings');
                const staticRadio = document.getElementById('staticRadio');

                manualSettings.classList.toggle('d-none', !staticRadio.checked);
            }

            // Add event listeners for both radio buttons
            document.getElementById('staticRadio').addEventListener('change', toggleManualSettings);
            document.getElementById('dhcpRadio').addEventListener('change', toggleManualSettings);

            // Initial call to set the initial state based on the default checked status
            toggleManualSettings();

            // Function to format IP address
            function formatIpAddress(input) {
                let value = input.value.replace(/\D/g, '');
                if (value.length > 2) {
                    value = `${value.slice(0, 3)}.${value.slice(3, 5)}.${value.slice(5, 7)}.${value.slice(7, 9)}`;
                }
                input.value = value;
            }
        </script>
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
