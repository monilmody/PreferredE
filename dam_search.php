<?php
include("./header.php");
require_once("config.php");

//Prevent the user visiting the logged in page if he/she is already logged in

// if(isUserLoggedIn()) {
// 	header("Location: myaccount.php");
// 	die();
// }

// call to fetchallblogs function from functions.php
//$allblogs = fetchAllBlogs();
//require_once("header.php");
//$horseList = fetchHorseList();
?>
<head>
  <script src="assets/js/script.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
  
</head>
    <!-- ***** Header Area End ***** -->
    <br>
	<h1 style="text-align:center;color:#D98880;">HORSE SEARCH REPORT</h1>
    <section class="section" id="call-to-action" style="background-color:C7FAF7)">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <form name="dam" action="process_dam_search.php" method="post">
                        <hr>
                         <input type="text" name="dam" id="dam" placeholder="horse name" onkeyup="this.value = this.value.toUpperCase();" required/>
                        <!--<select id="dam" style="width:200px;" class="operator" onkeyup="this.value = this.value.toUpperCase();showHint(this.value);"> 
                                 <option value="" onkeyup="this.value = this.value.toUpperCase();">Select a Horse...</option>
                                 <?php 
//                                  foreach($horseList as $row) {
//                             	    echo '<option>'.$row[Horse].'</option>';
//                                 } ?>
                          </select>-->
                        
<!--                          <select class="custom-select1" id="dam" onchange="this.value = this.value.toUpperCase();showHint(this.value);"> -->
<!--                         	<option value="">Horse Name</option> -->
<!--                         	<option value="">Horse search</option> -->
<!--                         </select> -->
                        
                        <hr>
                        <div class="btn-block">
                            <button type="submit" href="/">Search Horse</button>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </section>

   

    <!-- ***** Footer Start ***** -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <p>
                        Copyright © 2020 Company Name
                        - Preferred Equine
                    </p>
                </div>
            </div>
        </div>
    </footer>

   
	<script>
        window.onload = function() {
            document.getElementById("dam").focus();
        }
        
        $(document).ready(function () {
        //change selectboxes to selectize mode to be searchable
           $("select").select2();
        });
    </script>
    <script>
        //alert("aa");
        
    </script>