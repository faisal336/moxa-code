  <head>
    <meta charset="UTF-8" />
    <title>HARMS OIL | <?php echo $title ?></title>
    <link rel="stylesheet" href="style.css" />
    <link href="assets/boxicons.min.css" rel="stylesheet" />
    <link href="assets/style.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/bootstrap.min.css">
    <link href="fonts/font.min.css" rel="stylesheet"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  </head>
  <style type="text/css">
    @font-face {
     font-family: Roboto;
     src: url(assets/roboto-regular.woff);
    }
      .section-padding {
        padding-top: 30px;
      }
      nav {
        width: 100%;
        height: 73px;
        top: 89px;
        left: 335px;
        gap: 0px;
        border-radius: 15px 0px 15px 15px;
        opacity: 0px;
        background-color: #000000;  /* Light gray background for the menu */
        display: flex;  /* Make the navigation bar horizontal */
        align-items: center;  /* Align menu items vertically within the bar */
        padding: 10px;  /* Add some padding inside the menu */
      }

      nav ul {
        list-style: none;  /* Remove default bullet points */
        margin: 0;  /* Remove default margin */
        padding: 0;  /* Remove default padding */
      }

      nav li {

        margin-right: 20px;  /* Add space between menu items horizontally */
        display: inline-block;  /* Make list items display side-by-side */
      }

      nav a {
        margin-left: -10px;
        font-family: Roboto;
        font-size: 20px;
        font-weight: 600;
        line-height: 23.44px;
        text-align: left;
        color: #D38A01B2;  /* Text color for menu items */
        text-decoration: none !important;  /* Remove underline from links */
        padding: 24px;
        transition: background-color 0.2s ease;  /* Smooth background color change on hover */
      }

      nav a.active {
        font-family: 'Roboto';
        font-size: 20px;
        font-weight: 600;
        line-height: 23.44px;
        position: relative;
        left: 0px;
        padding: 24px;
        border-radius: 15px 1px 15px 15px;
        color: #000000;
        background: linear-gradient(180deg, #D38A01 0%, #D38A01 100%);
        
      }

      nav a:hover {
        font-family: Roboto;
        font-size: 20px;
        font-weight: 600;
        line-height: 23.44px;
        padding: 24px;
        border-radius: 15px 1px 15px 15px;
        color: #000000;
        background: linear-gradient(180deg, #D38A01 0%, #D38A01 100%);
      }
        /*New code*/
     .home-heading {
        font-family: Roboto;
        font-size: 24px;
        font-weight: 700;
        line-height: 28.13px;
        text-align: left;
        margin-left: 20px;
        font-size: 22px;
        font-weight: 900;
     }
    
     .button-container {
       display: inline-block;
       margin: 5px; /* Add some margin for spacing */
     }

     .button-container input[type="radio"] {
        background-color:  #ddd; /* Style the selected button */
       display: none; /* Hide the default radio button */
     }

     .button-container label {
       display: inline-block;
       padding: 10px 20px;
       border: 1px solid #ccc;
       border-radius: 5px;
       cursor: pointer; /* Simulate button behavior */
     }

     .button-container label:hover {
       background-color: #ddd; /* Change background on hover */
       color: black;
     }

     .button-container input[type="radio"]:checked + label {
       color: white;
       background-color: #d38a01; /* Style the selected button */
     }
     .select {
       width: 50% !important;
     }
     .nav-list .active {
      background:    color: #D38A01;;
     } 
     .nav ul li{
        display: inline-block;
     }
    
     /*For Sidebar Code*/
     .sidebar .active {
      width: 220px;
      height: 61px;
      color: #000000 !important;
      gap: 27px;
      border-radius: 15px 15px 15px 15px;
      background: linear-gradient(180deg, #D38A01 0%, rgba(211, 138, 1, 0.65) 100%);
    }
    .sidebar .active a .links_name{
          color: #000000 !important;
          
    }
    .sidebar .active i {
          color: #000000 !important;
          
    }

    .black {
        display: none; /* Ensure visibility */
    }
    .brown {
        display: block; /* Ensure visibility */
    }
  /* Brown image on hover */
  li:hover .black {
      display: block !important;
  }

  li:hover .brown {
      display: none !important;
  }
      
     } 
     /**********Sidebar CSS Code*********/
     .sidebar-bottom {
      height: auto;
      margin-top: 200px !important;
      padding: 10px;

     }
     .sidebar-fp {

        margin-left: 10px;
        color: #7a7a7a !important;
        font-family: Gilroy-Regular;
        font-size: 14px;
        line-height: 16.41px;
        font-weight: 400;
        text-align: left;

     }
    .sidebar-time {
      color: #D38A01;
      font-size: 12px !important;
      margin-left: 10px ;
     }

     .logoutBtn {
      color: white;
      color: white;
      text-decoration: none;
      
     }

     .logoutDiv {
        margin-left: 15px;
     }
     .logoutBtn:hover{
      text-decoration:none ;
      color: white;
      font-size: 600;

     }
     .logoutImg{
        width: 28px;
        height: 25px;
     }
    /*********Active Icon Hide Show code***********/
    .black-show {
      display: block !important;
    }
    .brown-hide {
      display: none !important;
    }
    /*********End Active Icons Hide Show**********/

    /*************Show Status****************/
    .show-border {
        border: 1px solid #dee2e6 ;
    }
    /*********End Show Status**********/
    .box {
        width: 300px;
        padding: 20px;

    }
    .box p{
        font-weight: 500;
    }
     .box span{
      background: #D38A0126;

      padding: 5px 10px 5px 10px !important;
      font-family: Roboto;
      font-size: 12px;
      font-weight: 600;
      line-height: 20px;
      letter-spacing: 0.005em;
      text-align: center;
      padding: 5px;
      border-radius: 4px;
    }
    .gauge-reading{
        font-family: Roboto;
        color: #404D61;
        font-size: 12px;
        font-weight: 500;
        line-height: 11.72px;
        text-align: left;

    }
    .reading-box  {
      margin-top: 10px;
      margin-left: 20px;
      margin-right: 20px;
      background-color: #D38A0126;
      padding: 5px 5px 5px 5px !important;
      font-family: Roboto;
      font-size: 12px;
      font-weight: 600;
      line-height: 20px;
      letter-spacing: 0.005em;
      text-align: center;
      border-radius: 4px;

    }
    .reading-box p {
      margin: 5px 5px 5px 5px;
    }
    .save {
      background: #d38a01;border: 1px solid #d38a01;
    }
    .save:hover {
      background: #b27400;border: 1px solid #d38a01;
    }
    .ip-padding {
      padding: 20px 20px 20px 20px;
    }
    .ip-setting-save {
      color: black;
      background: white;
      border:1px solid #d38a01;
    }
    .ip-setting-save:hover {
      color: white;
      background: #d38a01;
      border:1px solid #d38a01;
    }
    .ip-setting-save:focus {     
       background: #d38a01 !important;
       box-shadow: 1px #d38a01 !important;    
    }

    .ip-padding {
      padding: 20px 20px 20px 20px;
    }

    /*********Network Config Class ***********/
      .network-config-p {
        color:#404D61;
        font-family: Roboto;
        font-size: 16px;
        font-weight: 500;
        line-height: 18.75px;
        text-align: left;

      }
      .ip-config-p {
        color:#404D61;
        font-family: Roboto;
        font-size: 16px;
        font-weight: 500;
        line-height: 18.75px;
        text-align: left;
      }
      .subnetmask-config-p {
        color:#404D61;
        font-family: Roboto;
        font-size: 16px;
        font-weight: 500;
        line-height: 18.75px;
        text-align: left;
      }
      .network-config-p {
        color:#404D61;
        font-family: Roboto;
        font-size: 16px;
        font-weight: 500;
        line-height: 18.75px;
        text-align: left;
      }
      .network-config {
        background: #D38A0126;
        padding: 5px 10px 5px 10px !important;
        font-family: Roboto;
        font-size: 12px;
        font-weight: 600;
        line-height: 20px;
        letter-spacing: 0.005em;
        text-align: center;
        padding: 5px;
        border-radius: 4px;
      }
      .ipaddress {
        background: #D38A0126;
        padding: 5px 10px 5px 10px !important;
        font-family: Roboto;
        font-size: 12px;
        font-weight: 600;
        line-height: 20px;
        letter-spacing: 0.005em;
        text-align: center;
        padding: 5px;
        border-radius: 4px;
      }
      .subnetmask {
        background: #D38A0126;
        padding: 5px 10px 5px 10px !important;
        font-family: Roboto;
        font-size: 12px;
        font-weight: 600;
        line-height: 20px;
        letter-spacing: 0.005em;
        text-align: center;
        padding: 5px;
        border-radius: 4px;
      }
      .gateway {
        background: #D38A0126;
        padding: 5px 10px 5px 10px !important;
        font-family: Roboto;
        font-size: 12px;
        font-weight: 600;
        line-height: 20px;
        letter-spacing: 0.005em;
        text-align: center;
        padding: 5px;
        border-radius: 4px;
      }
    /*********Network Confg classes***********/
    .toggle {
      position: relative;
      box-sizing: border-box;
    }
    .toggle input[type="checkbox"] {
      position: absolute;
      left: 0;
      top: 0;
      z-index: 10;
      width: 14%;
      height: 100%;
      cursor: pointer;
      opacity: 0;
    }
    .toggle label {
      position: relative;
      display: flex;
      align-items: center;
      box-sizing: border-box;
    }
    .toggle label:before {
      content: '';
      width: 65px;
      height: 32px;
      background: #ccc;
      position: relative;
      display: inline-block;
      border-radius: 46px;
      box-sizing: border-box;
      transition: 0.2s ease-in;
    }
    .toggle label:after {
      content: '';
      position: absolute;
      width: 25px;
      height: 25px;
      border-radius: 100%;
      left: 2px;
      top: 2px;
      z-index: 2;
      background: #fff;
      box-sizing: border-box;
      transition: 0.2s ease-in;
    }
    .toggle input[type="checkbox"]:checked + label:before {
      background: #d38a01;
    }
    .toggle input[type="checkbox"]:checked + label:after {
      left: 35px;
    } 

    .gauge-port {
      font-family: Roboto;
      font-size: 24px;
      font-weight: 700;
      line-height: 28.13px;
      text-align: left;
    }
    .gauge-config {
      font-family: Roboto;
      font-size: 24px;
      font-weight: 700;
      line-height: 28.13px;
      text-align: left;

    }
    /*********************Admin Panel Media Query for Mobile Devices**************/
    @media only screen and (max-width: 600px) {
       .box p {
        text-align: left;
       }
       .box span {
        text-align: left;
        margin-left: 5px !important;
       }
       .sidebar.open ~ .home-section     {
            left: 250px;
            width: 1200px !important; 
        }
        .home-section {
            position: relative;
            background: #ffffff;
            min-height: 100vh;
            top: 0;
            left: 78px;
            width: 1200px !important;
            transition: all 0.5s ease;
            z-index: 2;
        }
        nav {
          width: 1200px !important; 
        }
      }

    /*********************End Admin Panel Media Query for Mobile Devices*/

  </style>
