<?php
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
            
            $result = $client->adminInitiateAuth([
                'AuthFlow' => 'ADMIN_NO_SRP_AUTH',
                'ClientId' => COGNITO_APP_CLIENT_ID,
                'UserPoolId' => COGNITO_USER_POOL_ID,
                'AuthParameters' => [
                    'USERNAME' => $username,
                    'PASSWORD' => $password
                ]
            ]);
            
            return ['success' => true];
            
        } catch (AwsException $e) {
            // CHANGED: Return the ACTUAL error for debugging
            return [
                'success' => false,
                'error' => $e->getAwsErrorMessage(), // Show real error
                'error_code' => $e->getAwsErrorCode(), // Show error code
                'full_message' => $e->getMessage() // Full message
            ];
        }
    }
    
    // Remove getUserFriendlyError for now - we need to see real errors


    
    // private static function getUserFriendlyError($errorCode, $errorMessage) {
    //     switch ($errorCode) {
    //         case 'NotAuthorizedException':
    //             return 'Invalid username or password';
    //         case 'UserNotFoundException':
    //             return 'User not found';
    //         case 'UserNotConfirmedException':
    //             return 'Please verify your email address first';
    //         default:
    //             return 'Login failed. Please try again';
    //     }
    // }
}