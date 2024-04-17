<div class="sidebar" style="width: 250px;">
      <div class="logo-details">
          <!--<i class="bx bxl-c-plus-plus icon"></i>-->
        <div class="logo_name" style="transform: scale(0.5);margin-left: -100px;"><img src="images/logo.png"  alt=""/></div>
        <!-- <i class="bx bx-fire" id="btn"><img src="images/favicon-harms-oil-32x32.png" alt=""></i> -->
      </div>
      <ul class="nav-list">
       
        <li>
          <a  href="index.php">
            <!-- <i class="bx bx-home"></i> -->
            <img class="brown" src="images/home-brown.png" >
             <img class="black" src="images/home-black.png" >
            <span class="links_name">Home</span>
          </a>
          <span class="tooltip">Home</span>
        </li>
        <li>
          <a href="network.php">
            <img class="brown" src="images/network-brown.png" >
            <img class="black" src="images/network-black.png" >
            <!-- <i class="bx bx-network-chart"></i> -->
            <span class="links_name">Network</span>
          </a>
          <span class="tooltip">Network</span>
        </li>
        <li>
          <a href="gauge.php">
            <!-- <i class="bx bx-tachometer"></i> -->
            <img class="brown" src="images/gauge-brown.png"  style="width: 29px;height: 17px;">
            <img class="black" src="images/gauge-black.png" style="width: 29px;height: 17px;">
            <span class="links_name">Gauge</span>
          </a>
          <span class="tooltip">Gauge</span>
        </li>
        <li>
          <a href="advance.php">
            <img class="brown" src="images/advance-brown.png" >
            <img class="black" src="images/advance-black.png" >
            <!-- <i class="bx bx-cog"></i> -->
            <span class="links_name">Advance</span>
          </a>
          <span class="tooltip">Advance</span>
        </li>
      </ul>

      <div  class="sidebar-bottom" style="margin-top: 100px;margin-bottom: 1000px;">
        <?php
          $serverIp = $_SERVER['SERVER_ADDR'];
  
        ?>
        <p class="sidebar-fp">FTMS (Fuel Tanks Monitoring System) 
          <br>Web Admin: <span style="color: #D38A01;font-size: 10px !important;"><?php echo $serverIp; ?></span></p>
        <?php 
          $currentTime = date('Y-m-d H:i:s'); // Format: YYYY-MM-DD HH:MM:S  
          $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $currentTime); // Parse into object
          $formattedTime = $dateTime->format('l F: H:i:s P T'); // Format as desired


        ?>
        <p class="sidebar-fp">Server Time:</p>
        <p class="sidebar-time"><?php  

      $chicago_time = new DateTime('now', new DateTimeZone('America/Chicago'));
      echo '' . $chicago_time->format('l F jS, Y  h:i:s A');
      ?> </p>
        <div class="logoutDiv">
          <span><img class="logoutImg" src="images/logout.png" ></span><a class="logoutBtn" href="logout.php">Logout</a>
        </div>
      </div>
      <br>
      <br>
      <br>
      <br>
      
    </div>