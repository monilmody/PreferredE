<?php
/**
 * Simple Cognito Authentication
 * Place this file in your website root directory
 */

class SimpleCognitoAuth {
    
    /**
     * Authenticate user with AWS Cognito
     */
    public static function login($username, $password) {
        // Prepare the request to Cognito
        $url = "https://cognito-idp." . COGNITO_REGION . ".amazonaws.com/";
        
        $headers = [
            'Content-Type: application/x-amz-json-1.1',
            'X-Amz-Target: AWSCognitoIdentityProviderService.InitiateAuth'
        ];
        
        $data = [
            'AuthFlow' => 'USER_PASSWORD_AUTH',
            'ClientId' => COGNITO_APP_CLIENT_ID,
            'AuthParameters' => [
                'USERNAME' => $username,
                'PASSWORD' => $password
            ]
        ];
        
        // Send request to Cognito
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            return [
                'success' => false,
                'error' => 'Connection error: ' . curl_error($ch)
            ];
        }
        
        curl_close($ch);
        
        // Check response
        if ($httpCode == 200) {
            $result = json_decode($response, true);
            return [
                'success' => true,
                'data' => $result
            ];
        } else {
            // Parse error message
            $errorData = json_decode($response, true);
            $errorMsg = $errorData['message'] ?? 'Invalid username or password';
            
            // User-friendly error messages
            if (strpos($errorMsg, 'Incorrect username or password') !== false) {
                $errorMsg = 'Invalid username or password';
            } elseif (strpos($errorMsg, 'User is not confirmed') !== false) {
                $errorMsg = 'Please verify your email address first';
            }
            
            return [
                'success' => false,
                'error' => $errorMsg
            ];
        }
    }
    
    /**
     * Get user information from access token
     */
    public static function getUserInfo($accessToken) {
        $url = "https://cognito-idp." . COGNITO_REGION . ".amazonaws.com/";
        
        $headers = [
            'Content-Type: application/x-amz-json-1.1',
            'X-Amz-Target: AWSCognitoIdentityProviderService.GetUser',
            'Authorization: ' . $accessToken
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{}');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            curl_close($ch);
            return null;
        }
        
        curl_close($ch);
        
        if ($httpCode == 200) {
            $data = json_decode($response, true);
            
            // Extract user attributes
            $userInfo = ['username' => $data['Username']];
            foreach ($data['UserAttributes'] as $attr) {
                $userInfo[$attr['Name']] = $attr['Value'];
            }
            
            return $userInfo;
        }
        
        return null;
    }
}
?>