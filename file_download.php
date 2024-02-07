<?php

$bred_param =$_GET['bred'];
$url = "";
if ($bred_param == "S") {
    $url = "http://www.preferredequineresults.com/PreferredE/sampleFileUpload.csv";
}elseif ($bred_param == "T")
{
   $url = "http://www.preferredequineresults.com/PreferredE/sampleFileUpload_T.csv";
}
// echo "Your file is being checked. <br>";

// Use basename() function to return
// the base name of file
$file_name = basename($url);
//echo $file_name;
$info = pathinfo($file_name);
// $file = "https://raw.githubusercontent.com/Vishal-Goyal/Preferred_SampleFile/main/sampleFileUpload.csv";
// DownloadFile($file);
// function DownloadFile($file) { // $file = include path
//     if(file_exists($file)) {
//         header('Content-Description: File Transfer');
//         header('Content-Type: application/octet-stream');
//         header('Content-Disposition: attachment; filename='.basename($file));
//         header('Content-Transfer-Encoding: binary');
//         header('Expires: 0');
//         header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
//         header('Pragma: public');
//         header('Content-Length: ' . filesize($file));
//         ob_clean();
//         flush();
//         readfile($file);
//         exit;
//     }
    
// }

// Checking if the file is a
// CSV file or not
if ($info["extension"] == "csv") {
    
    /* Informing the browser that
     the file type of the concerned
     file is a MIME type (Multipurpose
     Internet Mail Extension type).
     Hence, no need to play the file
     but to directly download it on
     the client's machine. */
    header("Content-Description: File Transfer");
    header("Content-Type: application/octet-stream");
    header(
        "Content-Disposition: attachment; filename=\""
        . $file_name . "\"");
    //echo "File downloaded successfully";
    readfile ($url);
}

//else echo "Sorry, that's not a CSV file";

exit();

?>