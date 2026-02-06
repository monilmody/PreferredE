<?php
require_once 'vendor/autoload.php';

use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Aws\Exception\AwsException;

class CognitoAuth {
    
    public static function authenticate($username, $password) {
        try {
            // NO CREDENTIALS NEEDED - Uses EC2 IAM Role automatically
            $client = new CognitoIdentityProviderClient([
                'region' => COGNITO_REGION,
                'version' => 'latest'
                // AWS SDK automatically uses EC2 instance profile
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
            // Check if it's a credentials error
            if (strpos($e->getMessage(), 'No credentials') !== false) {
                return [
                    'success' => false,
                    'error' => 'System configuration error. Please contact administrator.'
                ];
            }
            
            $errorCode = $e->getAwsErrorCode();
            $errorMessage = $e->getAwsErrorMessage();
            
            return [
                'success' => false,
                'error' => self::getUserFriendlyError($errorCode, $errorMessage)
            ];
        }
    }
    
    private static function getUserFriendlyError($errorCode, $errorMessage) {
        switch ($errorCode) {
            case 'NotAuthorizedException':
                return 'Invalid username or password';
            case 'UserNotFoundException':
                return 'User not found';
            case 'UserNotConfirmedException':
                return 'Please verify your email address first';
            default:
                return 'Login failed. Please try again';
        }
    }
}
?>