<?php
// session_start();
require_once("config.php");
?>
<head>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-18763673-4"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-18763673-4');
</script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap" rel="stylesheet">
    <title>Preferred Equine</title>
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" href="assets\images\favicon.ico" type="image/x-icon">
</head>

    
    
    <!-- ***** Header Area Start ***** -->
    <header class="header-area header-sticky" style="background-color:#2E4053;position: fixed;">
    	<div style= "margin:5px 30px 30px 30px;">
                <div class="col-12">
                    <nav class="main-nav">
                        <!-- ***** Logo Start ***** -->
                        <a href="index.php" class="logo">Preferred <em> Equine - AWS</em></a>
                        <!-- ***** Logo End ***** -->
                        <!-- ***** Menu Start ***** -->
                        <ul class="nav">
                            <li><a href="index.php" class="active">Home</a></li>
                            <?php 
                            if ($_SESSION["UserName"] != "") {
                                if ($_SESSION["UserRole"] == "A" || $_SESSION["UserRole"] == "S" || $_SESSION["UserRole"] == "ST") {
                            ?>
                            <li class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Standardbred</a>
                              
                                <div class="dropdown-menu" style="background-color:black;" >
                                    <a class="dropdown-item" href="dam_search.php">Horse Search Report</a>
                                    <a class="dropdown-item" href="sire_analysis.php">Sire Analysis</a>
                                    <a class="dropdown-item" href="sire_analysis_summary.php">Sire Analysis Summary</a>
                                    <a class="dropdown-item" href="buyers_report.php">Buyer's Report</a>
                                    <a class="dropdown-item" href="sales_report.php">Sales Report</a>
                                    <a class="dropdown-item" href="auction_report.php">Auction Report</a>
                                    <a class="dropdown-item" href="top_buyers.php">Top Yearling Buyers</a>
                                    <a class="dropdown-item" href="individual_sales_report.php">Individual Horse Sales Report</a>
                                    <a class="dropdown-item" href="broodmares_report.php">Broodmares Report</a>
                                    <a class="dropdown-item" href="cons_analysis.php">Consignor Analysis</a>
                                </div>
                            </li>
                            <?php 
                                }
                                if ($_SESSION["UserRole"] == "A" || $_SESSION["UserRole"] == "T" || $_SESSION["UserRole"] == "ST") {
                            ?>
                            <li class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Thoroughbred</a>
                              
                                <div class="dropdown-menu" style="background-color:black;" >
                                    <a class="dropdown-item" href="horse_search_tb.php">Horse Search Report</a>
                                    <a class="dropdown-item" href="sire_analysis_tb.php">Sire Analysis</a>
                                    <a class="dropdown-item" href="sire_analysis_summary_tb.php">Sire Analysis Summary</a>
                                    <a class="dropdown-item" href="buyers_report_tb.php">Buyer's Report</a>
                                    <a class="dropdown-item" href="sales_report_tb.php">Sales Report</a>
                                    <a class="dropdown-item" href="auction_report_tb.php">Auction Report</a>
                                    <a class="dropdown-item" href="top_buyers_tb.php">Top Yearling Buyers</a>
                                    <a class="dropdown-item" href="weanling-report.php">Weanlings Report</a>
                                    <a class="dropdown-item" href="individual_sales_report_tb.php">Individual Horse Sales Report</a>
                                    <a class="dropdown-item" href="broodmares_report_tb.php">Broodmares Report</a>
                                </div>
                            </li>
                            <?php 
                                }
                                if ($_SESSION["UserRole"] == "A") {
                            ?>
<!--                             <li><a href="fleet.php">Fleet</a></li> -->
                            <li class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">File Upload</a>
                              
                                <div class="dropdown-menu" style="background-color:black;" >
                                    <a class="dropdown-item" href="sales_file_upload.php">Standardbred</a>
                                	<a class="dropdown-item" href="sales_file_upload_tb.php">Thoroughbred</a>
                                    <a class="dropdown-item" href="http://18.209.103.60:8000/">File Upload Python</a>
                                	<a class="dropdown-item" href="manage_data.php">Manage File Upload Data</a>
                                	<a class="dropdown-item" href="file_upload_rating_update.php">Standardbred Rating Update</a>
                                	<a class="dropdown-item" href="file_upload_et_update.php">Standardbred ET Update</a>
                                </div>
                            </li>
                            
                            <?php 
                                }
                                if ($_SESSION["UserRole"] == "A") {
                            ?>
                           <li class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">USERS</a>
                              
                                <div class="dropdown-menu" style="background-color:black;" >
                                
                                
                                	<a class="dropdown-item" href="user_authorization.php">AUTHORIZE USERS</a>
                                
                                </div>
                            </li>
                            <?php 
                                }
                            }
                                ?>
<!--                             <li class="dropdown"> -->
<!--                                 <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">About</a> -->
                              
<!--                                 <div class="dropdown-menu"> -->
<!--                                     <a class="dropdown-item" href="about.php">About Us</a> -->
<!--                                     <a class="dropdown-item" href="blog.php">Blog</a> -->
<!--                                     <a class="dropdown-item" href="team.php">Team</a> -->
<!--                                     <a class="dropdown-item" href="testimonials.php">Testimonials</a> -->
<!--                                     <a class="dropdown-item" href="faq.php">FAQ</a> -->
<!--                                     <a class="dropdown-item" href="terms.php">Terms</a> -->
<!--                                 </div> -->
<!--                             </li> -->
<!--                             <li><a href="registration.php">Register</a></li>  -->  

                            
                            <?php
                            if ($_SESSION["UserName"] == "") {
                                echo '<li><a href="registration.php" class="active">REGISTER</a></li>';
                                echo '<li><a href="login.php">Login</a></li>';
                            }else
                            {
                                echo '<li class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">'.$_SESSION["UserName"].'</a>
                              
                                <div class="dropdown-menu" style="background-color:black;" >
                                
                                
                                	<a class="dropdown-item" href="logout.php">Logout</a>
                                
                                </div>
                            </li>';
                            }
                            ?>
                            
							
                            
                        </ul>   
                        <!-- ***** Menu End ***** -->
                    </nav>
                </div>
            </div>
    </header>
    <!-- ***** Header Area End ***** -->
    <br>
    <br>
    <br>