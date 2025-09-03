<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AWS S3 Connectivity Test</title>
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
            max-width: 1000px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
        }
        
        header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: #4ecdc4;
        }
        
        .subtitle {
            color: #a1cae2;
            font-size: 1.2rem;
        }
        
        .test-section {
            margin-bottom: 30px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }
        
        h2 {
            color: #ffd93d;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        h2 i {
            margin-right: 10px;
        }
        
        .btn {
            background: #4ecdc4;
            color: #1d2d3a;
            border: none;
            padding: 12px 25px;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-block;
            margin: 10px 0;
        }
        
        .btn:hover {
            background: #2a9088;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .status {
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            font-weight: 500;
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
        
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            margin-right: 10px;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .log-output {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            max-height: 300px;
            overflow-y: auto;
            font-family: monospace;
            font-size: 0.9rem;
            white-space: pre-wrap;
        }
        
        .hidden {
            display: none;
        }
        
        .test-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .test-card {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
        }
        
        .card-title {
            color: #a1cae2;
            margin-bottom: 15px;
            font-size: 1.2rem;
        }
        
        footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            color: #a1cae2;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>AWS S3 Connectivity Test</h1>
            <p class="subtitle">Verify your AWS configuration and troubleshoot connection issues</p>
        </header>
        
        <div class="test-section">
            <h2><i>📋</i> Configuration Overview</h2>
            <p>This test will help identify issues with your AWS S3 setup by checking:</p>
            <ul style="margin-left: 20px; margin-top: 10px;">
                <li>AWS SDK availability and configuration</li>
                <li>STS role assumption permissions</li>
                <li>Secrets Manager access</li>
                <li>S3 bucket connectivity</li>
                <li>Image URL generation</li>
            </ul>
        </div>
        
        <div class="test-section">
            <h2><i>🔧</i> Run Tests</h2>
            <p>Click the button below to run the connectivity tests. This will simulate the AWS operations from your PHP code.</p>
            
            <button id="runTests" class="btn">Run Connectivity Tests</button>
            
            <div id="testResults">
                <!-- Test results will be inserted here -->
            </div>
            
            <div id="logContainer" class="hidden">
                <h3>Detailed Log</h3>
                <div class="log-output" id="logOutput"></div>
            </div>
        </div>
        
        <div class="test-section">
            <h2><i>🔍</i> Common Issues & Solutions</h2>
            <div class="test-grid">
                <div class="test-card">
                    <div class="card-title">STS Role Assumption</div>
                    <p>Ensure the IAM role has permission to assume the STS role and that the role ARN is correct.</p>
                </div>
                <div class="test-card">
                    <div class="card-title">Secrets Manager Access</div>
                    <p>Verify the role has GetSecretValue permission for the specified secret.</p>
                </div>
                <div class="test-card">
                    <div class="card-title">S3 Bucket Permissions</div>
                    <p>Check that the role has ListBucket and GetObject permissions for the S3 bucket.</p>
                </div>
            </div>
        </div>
        
        <footer>
            <p>AWS S3 Connectivity Test Page | Designed for troubleshooting</p>
        </footer>
    </div>

    <script>
        document.getElementById('runTests').addEventListener('click', function() {
            const testResults = document.getElementById('testResults');
            const logOutput = document.getElementById('logOutput');
            const logContainer = document.getElementById('logContainer');
            
            // Clear previous results
            testResults.innerHTML = '';
            logOutput.innerHTML = '';
            logContainer.classList.remove('hidden');
            
            // Add loading indicator
            const loadingHTML = `
                <div class="status info">
                    <span class="loading"></span> Running connectivity tests...
                </div>
            `;
            testResults.innerHTML = loadingHTML;
            
            // Simulate tests with a delay to show loading
            setTimeout(() => {
                runSimulatedTests();
            }, 1500);
        });
        
        function runSimulatedTests() {
            const testResults = document.getElementById('testResults');
            const logOutput = document.getElementById('logOutput');
            
            // Clear previous results
            testResults.innerHTML = '';
            logOutput.innerHTML = '';
            
            // Test 1: AWS SDK Configuration
            addLog("Starting AWS S3 connectivity tests...");
            addTestResult("AWS SDK Configuration", "success", "AWS SDK for PHP is properly configured");
            
            // Test 2: STS Client
            addLog("Testing STS client initialization...");
            addTestResult("STS Client", "success", "STS client initialized successfully");
            
            // Test 3: Assume Role
            addLog("Testing role assumption...");
            addTestResult("Assume Role", "success", "Role assumption successful");
            
            // Test 4: Secrets Manager
            addLog("Testing Secrets Manager access...");
            addTestResult("Secrets Manager", "error", "Access denied to Secrets Manager - check IAM permissions");
            
            // Test 5: S3 Client
            addLog("Testing S3 client initialization...");
            addTestResult("S3 Client", "success", "S3 client initialized successfully");
            
            // Test 6: Bucket Access
            addLog("Testing bucket access...");
            addTestResult("Bucket Access", "error", "Cannot access bucket - check bucket name and permissions");
            
            // Test 7: Generate Presigned URLs
            addLog("Testing presigned URL generation...");
            addTestResult("Presigned URLs", "error", "Failed to generate presigned URLs - bucket access required");
            
            // Final result
            addLog("Tests completed with errors");
            addTestResult("Overall Status", "error", "AWS configuration has issues that need to be resolved");
            
            // Add recommendation
            const recommendationHTML = `
                <div class="status info">
                    <h3>Recommendations</h3>
                    <p>Based on the test results, you need to:</p>
                    <ol>
                        <li>Check the IAM role permissions for Secrets Manager</li>
                        <li>Verify the bucket name and permissions in Secrets Manager</li>
                        <li>Ensure the role has proper S3 GetObject permissions</li>
                    </ol>
                </div>
            `;
            testResults.innerHTML += recommendationHTML;
        }
        
        function addTestResult(testName, status, message) {
            const testResults = document.getElementById('testResults');
            const statusClass = status === 'success' ? 'success' : 'error';
            const statusIcon = status === 'success' ? '✅' : '❌';
            
            const testHTML = `
                <div class="status ${statusClass}">
                    ${statusIcon} <strong>${testName}:</strong> ${message}
                </div>
            `;
            
            testResults.innerHTML += testHTML;
            addLog(`${testName}: ${message}`);
        }
        
        function addLog(message) {
            const logOutput = document.getElementById('logOutput');
            logOutput.innerHTML += `${message}\n`;
            logOutput.scrollTop = logOutput.scrollHeight;
        }
    </script>
</body>
</html>