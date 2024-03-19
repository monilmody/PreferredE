<?php
require_once("config.php");

//Prevent the user visiting the logged in page if he/she is already logged in

// if(isUserLoggedIn()) {
// 	header("Location: myaccount.php");
// 	die();
// }

// call to fetchallblogs function from functions.php
// $allblogs = fetchAllBlogs();
// require_once("header.php");
?>

<!DOCTYPE html>
<html lang="en">
  <head><!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-18763673-4"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-18763673-4');
</script>

    <meta charset="utf-8">    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">    <meta name="description" content="">    <meta name="author" content="">    <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap" rel="stylesheet">    <title>Preferred Equine</title>    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.css">    <link rel="stylesheet" href="assets/css/style.css"></head>
    
    <body>
    
    <!-- ***** Preloader Start ***** -->
    <div id="js-preloader" class="js-preloader">
      <div class="preloader-inner">
        <span class="dot"></span>
        <div class="dots">
          <span></span>
          <span></span>
          <span></span>
        </div>
      </div>
    </div>
    <!-- ***** Preloader End ***** -->
    
    
    <!-- ***** Header Area Start ***** -->
    <?php
// include("./header.php");
    ?>
    <header class="header-area header-sticky">
	<div style= "margin:5px 30px 30px 30px;">
            <div class="row">
                <div class="col-12">
                    <nav class="main-nav">
                        <!-- ***** Logo Start ***** -->
                        <a href="index.php" class="logo">Preferred <em> Equine</em></a>
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
                                	<a class="dropdown-item" href="manage_data.php">Manage File Upload Data</a>
                                	<a class="dropdown-item" href="file_upload_rating_update.php">Standardbred Rating Update</a>
                                	<a class="dropdown-item" href="file_upload_et_update.php">Standardbred ET Update</a>
                                </div>
                            </li>
                            
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
                                }
                                if ($_SESSION["UserRole"] == "A" || $_SESSION["UserRole"] == "T" || $_SESSION["UserRole"] == "ST") {
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
                        <a class='menu-trigger'>
                            <span>Menu</span>
                        </a>
                        <!-- ***** Menu End ***** -->
                    </nav>
                </div>
            </div>
        </div>
    </header>
    <!-- ***** Header Area End ***** -->

    <!-- ***** Main Banner Area Start ***** -->
    <div class="main-banner" id="top">
        <video autoplay muted loop id="bg-video">
            <source src="assets/images/video.mp4" type="video/mp4" />
        </video>

        <div class="video-overlay header-text">
            <div class="caption">
                <h6>Put the power and the network of the world's </h6>
                <h6>#1 Standardbred sales agency to work for you.</h6>
                <h2><em>There's a reason we're</em> Preferred!</h2>
                <div class="main-button">
                    <a href="contact.php">Contact Us</a>
                </div>
            </div>
        </div>
    </div>
    <!-- ***** Main Banner Area End ***** -->

   

<!--     <section class="section section-bg" id="schedule" style="background-image: url(assets/images/about-fullscreen-1-1920x700.jpg)">
<!--         <div class="container"> -->
<!--             <div class="row"> -->
<!--                 <div class="col-lg-6 offset-lg-3"> -->
<!--                     <div class="section-heading dark-bg"> -->
<!--                         <h2>Read <em>About Us</em></h2> -->
<!--                         <img src="assets/images/line-dec.png" alt=""> -->
<!--                         <p>Nunc urna sem, laoreet ut metus id, aliquet consequat magna. Sed viverra ipsum dolor, ultricies fermentum massa consequat eu.</p> -->
<!--                     </div> -->
<!--                 </div> -->
<!--             </div> -->
<!--             <div class="row"> -->
<!--                 <div class="col-lg-12"> -->
<!--                     <div class="cta-content text-center"> -->
<!--                         <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Labore deleniti voluptas enim! Provident consectetur id earum ducimus facilis, aspernatur hic, alias, harum rerum velit voluptas, voluptate enim! Eos, sunt, quidem.</p> -->

<!--                         <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto nulla quo cum officia laboriosam. Amet tempore, aliquid quia eius commodi, doloremque omnis delectus laudantium dolor reiciendis non nulla! Doloremque maxime quo eum in culpa mollitia similique eius doloribus voluptatem facilis! Voluptatibus, eligendi, illum. Distinctio, non!</p> -->
<!--                     </div> -->
<!--                 </div> -->
<!--             </div> -->
<!--         </div> -->
<!--     </section> -->

    <!-- ***** Blog Start ***** -->
<!--     <section class="section" id="our-classes"> -->
<!--         <div class="container"> -->
<!--             <div class="row"> -->
<!--                 <div class="col-lg-6 offset-lg-3"> -->
<!--                     <div class="section-heading"> -->
<!--                         <h2>Read our <em>Blog</em></h2> -->
<!--                         <img src="assets/images/line-dec.png" alt=""> -->
<!--                         <p>Nunc urna sem, laoreet ut metus id, aliquet consequat magna. Sed viverra ipsum dolor, ultricies fermentum massa consequat eu.</p> -->
<!--                     </div> -->
<!--                 </div> -->
<!--             </div> -->
<!--             <div class="row" id="tabs"> -->
<!--               <div class="col-lg-4"> -->
<!--                 <ul> -->
<!--                   <li><a href='#tabs-1'>Lorem ipsum dolor sit amet, consectetur adipisicing.</a></li> -->
<!--                   <li><a href='#tabs-2'>Aspernatur excepturi magni, placeat rerum nobis magnam libero! Soluta.</a></li> -->
<!--                   <li><a href='#tabs-3'>Sunt hic recusandae vitae explicabo quidem laudantium corrupti non adipisci nihil.</a></li> -->
<!--                   <div class="main-rounded-button"><a href="blog.php">Read More</a></div> -->
<!--                 </ul> -->
<!--               </div> -->
<!--               <div class="col-lg-8"> -->
<!--                 <section class='tabs-content'> -->
<!--                   <article id='tabs-1'> -->
<!--                     <img src="assets/images/blog-image-1-940x460.jpg" alt=""> -->
<!--                     <h4>Lorem ipsum dolor sit amet, consectetur adipisicing.</h4> -->

<!--                     <p><i class="fa fa-user"></i> John Doe &nbsp;|&nbsp; <i class="fa fa-calendar"></i> 27.07.2020 10:10 &nbsp;|&nbsp; <i class="fa fa-comments"></i>  15 comments</p> -->

<!--                     <p>Phasellus convallis mauris sed elementum vulputate. Donec posuere leo sed dui eleifend hendrerit. Sed suscipit suscipit erat, sed vehicula ligula. Aliquam ut sem fermentum sem tincidunt lacinia gravida aliquam nunc. Morbi quis erat imperdiet, molestie nunc ut, accumsan diam.</p> -->
<!--                     <div class="main-button"> -->
<!--                         <a href="blog-details.php">Continue Reading</a> -->
<!--                     </div> -->
<!--                   </article> -->
<!--                   <article id='tabs-2'> -->
<!--                     <img src="assets/images/blog-image-2-940x460.jpg" alt=""> -->
<!--                     <h4>Aspernatur excepturi magni, placeat rerum nobis magnam libero! Soluta.</h4> -->
<!--                     <p><i class="fa fa-user"></i> John Doe &nbsp;|&nbsp; <i class="fa fa-calendar"></i> 27.07.2020 10:10 &nbsp;|&nbsp; <i class="fa fa-comments"></i>  15 comments</p> -->
<!--                     <p>Integer dapibus, est vel dapibus mattis, sem mauris luctus leo, ac pulvinar quam tortor a velit. Praesent ultrices erat ante, in ultricies augue ultricies faucibus. Nam tellus nibh, ullamcorper at mattis non, rhoncus sed massa. Cras quis pulvinar eros. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</p> -->
<!--                     <div class="main-button"> -->
<!--                         <a href="blog-details.php">Continue Reading</a> -->
<!--                     </div> -->
<!--                   </article> -->
<!--                   <article id='tabs-3'> -->
<!--                     <img src="assets/images/blog-image-3-940x460.jpg" alt=""> -->
<!--                     <h4>Sunt hic recusandae vitae explicabo quidem laudantium corrupti non adipisci nihil.</h4> -->
<!--                     <p><i class="fa fa-user"></i> John Doe &nbsp;|&nbsp; <i class="fa fa-calendar"></i> 27.07.2020 10:10 &nbsp;|&nbsp; <i class="fa fa-comments"></i>  15 comments</p> -->
<!--                     <p>Fusce laoreet malesuada rhoncus. Donec ultricies diam tortor, id auctor neque posuere sit amet. Aliquam pharetra, augue vel cursus porta, nisi tortor vulputate sapien, id scelerisque felis magna id felis. Proin neque metus, pellentesque pharetra semper vel, accumsan a neque.</p> -->
<!--                     <div class="main-button"> -->
<!--                         <a href="blog-details.php">Continue Reading</a> -->
<!--                     </div> -->
<!--                   </article> -->
<!--                 </section> -->
<!--               </div> -->
<!--             </div> -->
<!--         </div> -->
<!--     </section> -->
    <!-- ***** Blog End ***** -->

    <!-- ***** Call to Action Start ***** -->
<!--    <section class="section section-bg" id="call-to-action" style="background-image: url(assets/images/banner-image-1-1920x500.jpg)">
<!--         <div class="container"> -->
<!--             <div class="row"> -->
<!--                 <div class="col-lg-10 offset-lg-1"> -->
<!--                     <div class="cta-content"> -->
<!--                         <h2>Send us a <em>message</em></h2> -->
<!--                         <p>Ut consectetur, metus sit amet aliquet placerat, enim est ultricies ligula, sit amet dapibus odio augue eget libero. Morbi tempus mauris a nisi luctus imperdiet.</p> -->
<!--                         <div class="main-button"> -->
<!--                             <a href="contact.php">Contact us</a> -->
<!--                         </div> -->
<!--                     </div> -->
<!--                 </div> -->
<!--             </div> -->
<!--         </div> -->
<!--     </section> -->
    <!-- ***** Call to Action End ***** -->

    <!-- ***** Testimonials Item Start ***** -->
<!--     <section class="section" id="features"> -->
<!--         <div class="container"> -->
<!--             <div class="row"> -->
<!--                 <div class="col-lg-6 offset-lg-3"> -->
<!--                     <div class="section-heading"> -->
<!--                         <h2>Read our <em>Testimonials</em></h2> -->
<!--                         <img src="assets/images/line-dec.png" alt="waves"> -->
<!--                         <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Voluptatem incidunt alias minima tenetur nemo necessitatibus?</p> -->
<!--                     </div> -->
<!--                 </div> -->
<!--                 <div class="col-lg-6"> -->
<!--                     <ul class="features-items"> -->
<!--                         <li class="feature-item"> -->
<!--                             <div class="left-icon"> -->
<!--                                 <img src="assets/images/features-first-icon.png" alt="First One"> -->
<!--                             </div> -->
<!--                             <div class="right-content"> -->
<!--                                 <h4>John Doe</h4> -->
<!--                                 <p><em>"Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dicta numquam maxime voluptatibus, impedit sed! Necessitatibus repellendus sed deleniti id et!"</em></p> -->
<!--                             </div> -->
<!--                         </li> -->
<!--                         <li class="feature-item"> -->
<!--                             <div class="left-icon"> -->
<!--                                 <img src="assets/images/features-first-icon.png" alt="second one"> -->
<!--                             </div> -->
<!--                             <div class="right-content"> -->
<!--                                 <h4>John Doe</h4> -->
<!--                                 <p><em>"Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dicta numquam maxime voluptatibus, impedit sed! Necessitatibus repellendus sed deleniti id et!"</em></p> -->
<!--                             </div> -->
<!--                         </li> -->
<!--                     </ul> -->
<!--                 </div> -->
<!--                 <div class="col-lg-6"> -->
<!--                     <ul class="features-items"> -->
<!--                         <li class="feature-item"> -->
<!--                             <div class="left-icon"> -->
<!--                                 <img src="assets/images/features-first-icon.png" alt="fourth muscle"> -->
<!--                             </div> -->
<!--                             <div class="right-content"> -->
<!--                                 <h4>John Doe</h4> -->
<!--                                 <p><em>"Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dicta numquam maxime voluptatibus, impedit sed! Necessitatibus repellendus sed deleniti id et!"</em></p> -->
<!--                             </div> -->
<!--                         </li> -->
<!--                         <li class="feature-item"> -->
<!--                             <div class="left-icon"> -->
<!--                                 <img src="assets/images/features-first-icon.png" alt="training fifth"> -->
<!--                             </div> -->
<!--                             <div class="right-content"> -->
<!--                                 <h4>John Doe</h4> -->
<!--                                 <p><em>"Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dicta numquam maxime voluptatibus, impedit sed! Necessitatibus repellendus sed deleniti id et!"</em></p> -->
<!--                             </div> -->
<!--                         </li> -->
<!--                     </ul> -->
<!--                 </div> -->
<!--             </div> -->

<!--             <br> -->

<!--             <div class="main-button text-center"> -->
<!--                 <a href="testimonials.php">Read More</a> -->
<!--             </div> -->
<!--         </div> -->
<!--     </section> -->
    <!-- ***** Testimonials Item End ***** -->
    
    <!-- ***** Footer Start ***** -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <p>
                        Copyright © 2020 Preferred Equine
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- jQuery -->
    <script src="assets/js/jquery-2.1.0.min.js"></script>

    <!-- Bootstrap -->
    <script src="assets/js/popper.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>

    <!-- Plugins -->
    <script src="assets/js/scrollreveal.min.js"></script>
    <script src="assets/js/waypoints.min.js"></script>
    <script src="assets/js/jquery.counterup.min.js"></script>
    <script src="assets/js/imgfix.min.js"></script> 
    <script src="assets/js/mixitup.js"></script> 
    <script src="assets/js/accordions.js"></script>
    
    <!-- Global Init -->
    <script src="assets/js/custom.js"></script>

  </body>
</html>



