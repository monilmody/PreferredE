<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AWS Role Assumption Error Diagnostic</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            color: #fff;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }
        
        header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        h1 {
            font-size: 2.8rem;
            margin-bottom: 10px;
            color: #e74c3c;
        }
        
        .subtitle {
            color: #a1cae2;
            font-size: 1.3rem;
            margin-bottom: 20px;
        }
        
        .alert-banner {
            background: rgba(231, 76, 60, 0.3);
            border: 1px solid #e74c3c;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .test-section {
            margin-bottom: 30px;
            padding: 25px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }
        
        h2 {
            color: #ffd93d;
            margin-bottom: 20px;
            font-size: 1.8rem;
        }
        
        h3 {
            color: #4ecdc4;
            margin: 20px 0 15px;
            font-size: 1.4rem;
        }
        
        .btn {
            background: #4ecdc4;
            color: #1d2d3a;
            border: none;
            padding: 14px 28px;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-block;
            margin: 15px 0;
            font-size: 1.1rem;
        }
        
        .btn:hover {
            background: #2a9088;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .status {
            padding: 18px;
            border-radius: 8px;
            margin: 18px 0;
            font-weight: 500;
            font-size: 1.1rem;
        }
        
        .success {
            background: rgba(46, 204, 113, 0.2);
            border-left: 5px solid #2ecc71;
            color: #2ecc71;
        }
        
        .error {
            background: rgba(231, 76, 60, 0.2);
            border-left: 5px solid #e74c3c;
            color: #e74c3c;
        }
        
        .info {
            background: rgba(52, 152, 219, 0.2);
            border-left: 5px solid #3498db;
            color: #3498db;
        }
        
        .log-output {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            max-height: 400px;
            overflow-y: auto;
            font-family: monospace;
            font-size: 1rem;
            white-space: pre-wrap;
            line-height: 1.5;
        }
        
        .issue-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-top: 25px;
        }
        
        .issue-card {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            padding: 25px;
        }
        
        .card-title {
            color: #a1cae2;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }
        
        .solution-list {
            margin-left: 25px;
            margin-bottom: 15px;
        }
        
        .solution-list li {
            margin-bottom: 10px;
            line-height: 1.5;
        }
        
        .code-block {
            background: rgba(0, 0, 0, 0.4);
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
            overflow-x: auto;
            font-family: monospace;
            font-size: 0.95rem;
        }
        
        footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 25px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            color: #a1cae2;
            font-size: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>AWS Role Assumption Error Diagnostic</h1>
            <p class="subtitle">Identifying the "Unexpected>" error in your PHP backend</p>
        </header>
        
        <div class="alert-banner">
            <h2>Error Location: PHP Backend (get_horse_details.php)</h2>
            <p>The "Unexpected>" error is happening in your PHP code during the AWS role assumption process, not in your frontend JavaScript.</p>
        </div>
        
        <div class="test-section">
            <h2>Debugging Your PHP Code</h2>
            
            <h3>1. Add Detailed Error Logging</h3>
            <p>Modify your PHP code to add more detailed error logging:</p>
            <div class="code-block">
// Add this to your PHP code before the try-catch block
error_log("Starting getHorseDetails for: " . $horseId);

// Inside your try block, add more detailed logging
error_log("Initializing STS client");
$stsClient = new StsClient([
    'region' => $region,
    'version' => 'latest',
]);

error_log("Attempting to assume role: " . $roleArn);
try {
    $assumeRoleResult = $stsClient->assumeRole([
        'RoleArn' => $roleArn,
        'RoleSessionName' => $sessionName,
        'DurationSeconds' => 3600,
    ]);
    error_log("Role assumption successful");
} catch (Exception $e) {
    error_log("Role assumption failed: " . $e->getMessage());
    throw $e;
}
            </div>
            
            <h3>2. Check for JSON Encoding Issues</h3>
            <p>The "Unexpected>" error might be from trying to JSON encode binary data:</p>
            <div class="code-block">
// Replace this line:
error_log("AssumeRole result: " . json_encode($assumeRoleResult->toArray()));

// With these lines:
error_log("AssumeRole result received");
error_log("Access Key ID: " . $assumeRoleResult['Credentials']['AccessKeyId']);
error_log("Session Token: " . substr($assumeRoleResult['Credentials']['SessionToken'], 0, 20) . "...");
            </div>
            
            <h3>3. Test AWS Connectivity Directly</h3>
            <p>Create a simple test script to isolate the AWS issue:</p>
            <div class="code-block">
// create test_aws.php
&lt;?php
require 'vendor/autoload.php';

use Aws\Sts\StsClient;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

$region = 'us-east-1';
$roleArn = 'arn:aws:iam::211125609145:role/python-website-logs';
$sessionName = 'TestSession';

try {
    error_log("Testing STS client");
    $stsClient = new StsClient([
        'region' => $region,
        'version' => 'latest',
    ]);
    
    error_log("Testing assumeRole");
    $result = $stsClient->assumeRole([
        'RoleArn' => $roleArn,
        'RoleSessionName' => $sessionName,
    ]);
    
    error_log("AssumeRole successful");
    error_log("Access Key: " . $result['Credentials']['AccessKeyId']);
    
    // Test S3 access
    $s3 = new S3Client([
        'region' => $region,
        'version' => 'latest',
        'credentials' => [
            'key'    => $result['Credentials']['AccessKeyId'],
            'secret' => $result['Credentials']['SecretAccessKey'],
            'token'  => $result['Credentials']['SessionToken']
        ]
    ]);
    
    error_log("S3 client created successfully");
    echo "AWS connectivity test passed!";
    
} catch (Exception $e) {
    error_log("Test failed: " . $e->getMessage());
    echo "Test failed: " . $e->getMessage();
}
            </div>
        </div>
        
        <div class="test-section">
            <h2>Common Solutions</h2>
            
            <div class="issue-grid">
                <div class="issue-card">
                    <div class="card-title">Check IAM Permissions</div>
                    <div class="solution-list">
                        <li>Verify the EC2 instance role has sts:AssumeRole permission</li>
                        <li>Check the trust relationship on the target role</li>
                        <li>Ensure the target role has S3 permissions</li>
                    </div>
                </div>
                
                <div class="issue-card">
                    <div class="card-title">AWS SDK Issues</div>
                    <div class="solution-list">
                        <li>Check your AWS SDK version</li>
                        <li>Verify PHP version compatibility</li>
                        <li>Ensure all required AWS PHP packages are installed</li>
                    </div>
                </div>
                
                <div class="issue-card">
                    <div class="card-title">PHP Configuration</div>
                    <div class="solution-list">
                        <li>Check PHP error logs for more details</li>
                        <li>Verify allow_url_fopen is enabled</li>
                        <li>Check SSL certificate configuration</li>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="test-section">
            <h2>Next Steps</h2>
            <ol class="solution-list">
                <li>Create the test_aws.php script and run it directly</li>
                <li>Check your PHP error logs for more detailed error messages</li>
                <li>Verify your IAM role permissions in AWS console</li>
                <li>Test with a simpler AWS operation to isolate the issue</li>
            </ol>
            
            <button class="btn" onclick="runTest()">Run AWS Test Script</button>
            <div id="testResult" class="log-output" style="display: none;"></div>
        </div>
        
        <footer>
            <p>AWS Role Assumption Error Diagnostic | Designed to help fix PHP backend issues</p>
        </footer>
    </div>

    <script>
        function runTest() {
            const testResult = document.getElementById('testResult');
            testResult.style.display = 'block';
            testResult.innerHTML = "Running test script...";
            
            // This would normally make an AJAX call to your test script
            setTimeout(() => {
                testResult.innerHTML = "To test your AWS connectivity:\n\n1. Create a file called test_aws.php with the code provided\n2. Run it directly from your server: php test_aws.php\n3. Check your PHP error logs for detailed output";
            }, 1000);
        }
    </script>
</body>
</html>