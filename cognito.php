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
    
        public static function register($email, $password, $first_name, $last_name, $user_role) {
        try {
            $client = new CognitoIdentityProviderClient([
                'region' => COGNITO_REGION,
                'version' => 'latest'
            ]);
            
            // 1. Sign up user (creates unconfirmed user)
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
            
            // 2. Immediately confirm the user (auto-confirm)
            $client->adminConfirmSignUp([
                'UserPoolId' => COGNITO_USER_POOL_ID,
                'Username' => $email
            ]);
            
            return [
                'success' => true,
                'message' => 'User created and confirmed successfully',
                'userSub' => $result['UserSub']
            ];
            
        } catch (AwsException $e) {
            return [
                'success' => false,
                'error' => $e->getAwsErrorMessage(),
                'error_code' => $e->getAwsErrorCode()
            ];
        }
    }
}