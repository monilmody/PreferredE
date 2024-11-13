<?php
// Get salecode from the query string
$salecode = $_GET['salecode'];

// Define the file path based on the salecode
// Adjust the path as needed to point to the folder where CSV files are stored
$filePath = "./uploads/$salecode.csv";

// Check if the file exists
if (file_exists($filePath)) {
    // Set headers to prompt the download
    header('Content-Description: File Transfer');
    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filePath));

    // Read the file and output its contents
    readfile($filePath);
    exit;
} else {
        // Display a styled error message if the file is not found
        echo '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>File Not Found</title>
            <style>
                body {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    height: 100vh;
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    color: #333;
                }
                .message-box {
                    text-align: center;
                    padding: 20px;
                    border: 1px solid #ddd;
                    background-color: #fff;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                }
                h1 {
                    color: #e74c3c;
                    font-size: 24px;
                }
                p {
                    font-size: 16px;
                }
                a {
                    color: #3498db;
                    text-decoration: none;
                }
                a:hover {
                    text-decoration: underline;
                }
            </style>
        </head>
        <body>
            <div class="message-box">
                <h1>File Not Found</h1>
                <p>Sorry, the file you are looking for does not exist.</p>
                <p><a href="javascript:history.back()">Go back to the previous page</a></p>
            </div>
        </body>
        </html>';
}
?>
