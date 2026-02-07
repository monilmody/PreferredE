<?php
// cognito.php - ADD register function to your existing file
require_once 'vendor/autoload.php';

use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Aws\Exception\AwsException;

class CognitoAuth {
    
    public static function authenticate($username, $password) {
        try {
            $client = new CognitoIdentityProviderClient([
                'region' => COGNITO_REGION,
                'version' => 'latest'
            ]);
            
            $result = $client->initiateAuth([
                'AuthFlow' => 'USER_PASSWORD_AUTH',
                'ClientId' => COGNITO_APP_CLIENT_ID,
                'AuthParameters' => [
                    'USERNAME' => $username,
                    'PASSWORD' => $password
                ]
            ]);
            
            return ['success' => true];
            
        } catch (AwsException $e) {
            return [
                'success' => false,
                'error' => 'Invalid username or password'
            ];
        }
    }
    
    // ADD THIS NEW FUNCTION:
    public static function register($email, $password, $first_name, $last_name, $user_role) {
        try {
            $client = new CognitoIdentityProviderClient([
                'region' => COGNITO_REGION,
                'version' => 'latest'
            ]);
            
            // Create user in Cognito
            $result = $client->signUp([
                'ClientId' => COGNITO_APP_CLIENT_ID,
                'Username' => $email,
                'Password' => $password,
                'UserAttributes' => [
                    ['Name' => 'email', 'Value' => $email],
                    ['Name' => 'given_name', 'Value' => $first_name],
                    ['Name' => 'family_name', 'Value' => $last_name]
                ]
            ]);
            
            // Auto-confirm user
            $client->adminConfirmSignUp([
                'UserPoolId' => COGNITO_USER_POOL_ID,
                'Username' => $email
            ]);
            
            return ['success' => true];
            
        } catch (AwsException $e) {
            return [
                'success' => false,
                'error' => 'Registration failed'
            ];
        }
    }
}