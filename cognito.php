<?php
// cognito.php
require_once 'vendor/autoload.php';

use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Aws\Exception\AwsException;

class CognitoAuth
{

    public static function authenticate($username, $password)
    {
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

            return [
                'success' => true,
                'result' => $result
            ];
        } catch (AwsException $e) {
            $errorCode = $e->getAwsErrorCode();

            if ($errorCode === 'UserNotConfirmedException') {
                return [
                    'success' => false,
                    'error' => 'UserNotConfirmedException',
                    'message' => 'Please verify your email before logging in'
                ];
            }

            return [
                'success' => false,
                'error' => 'Invalid username or password'
            ];
        }
    }

    public static function register($email, $password, $first_name, $last_name, $user_role)
    {
        try {
            $client = new CognitoIdentityProviderClient([
                'region' => COGNITO_REGION,
                'version' => 'latest'
                // IAM role handles credentials
            ]);

            // Sign up user (creates unconfirmed user)
            $result = $client->signUp([
                'ClientId' => COGNITO_APP_CLIENT_ID,
                'Username' => $email,
                'Password' => $password,
                'UserAttributes' => [
                    ['Name' => 'email', 'Value' => $email],
                    ['Name' => 'given_name', 'Value' => $first_name],
                    ['Name' => 'family_name', 'Value' => $last_name],
                    ['Name' => 'custom:userrole', 'Value' => $user_role]
                ]
            ]);

            return [
                'success' => true,
                'message' => 'User created. Please check your email for verification code.',
                'user_sub' => $result['UserSub'] ?? null,
                'requires_verification' => true
            ];
        } catch (AwsException $e) {
            $errorCode = $e->getAwsErrorCode();
            $errorMessage = $e->getAwsErrorMessage() ?? $e->getMessage();

            // Handle specific errors
            if ($errorCode === 'UsernameExistsException') {
                return [
                    'success' => false,
                    'error' => 'UsernameExistsException',
                    'message' => 'An account with this email already exists'
                ];
            } elseif ($errorCode === 'InvalidPasswordException') {
                return [
                    'success' => false,
                    'error' => 'InvalidPasswordException',
                    'message' => 'Password does not meet security requirements. Must be at least 8 characters with uppercase, lowercase, number, and special character.'
                ];
            } elseif ($errorCode === 'InvalidParameterException') {
                return [
                    'success' => false,
                    'error' => 'InvalidParameterException',
                    'message' => 'Invalid email format or user attributes'
                ];
            }

            return [
                'success' => false,
                'error' => $errorCode ?? 'UnknownError',
                'message' => $errorMessage
            ];
        }
    }

    public static function confirmSignUp($email, $confirmation_code)
    {
        try {
            $client = new CognitoIdentityProviderClient([
                'region' => COGNITO_REGION,
                'version' => 'latest'
            ]);

            $result = $client->confirmSignUp([
                'ClientId' => COGNITO_APP_CLIENT_ID,
                'Username' => $email,
                'ConfirmationCode' => $confirmation_code
            ]);

            return [
                'success' => true,
                'message' => 'Email verified successfully'
            ];
        } catch (AwsException $e) {
            $errorCode = $e->getAwsErrorCode();

            if ($errorCode === 'CodeMismatchException') {
                return [
                    'success' => false,
                    'error' => 'CodeMismatchException',
                    'message' => 'Invalid verification code. Please check and try again.'
                ];
            } elseif ($errorCode === 'ExpiredCodeException') {
                return [
                    'success' => false,
                    'error' => 'ExpiredCodeException',
                    'message' => 'Verification code has expired. Please request a new one.'
                ];
            } elseif ($errorCode === 'UserNotFoundException') {
                return [
                    'success' => false,
                    'error' => 'UserNotFoundException',
                    'message' => 'User not found. Please register again.'
                ];
            }

            return [
                'success' => false,
                'error' => $errorCode ?? 'UnknownError',
                'message' => $e->getAwsErrorMessage() ?? $e->getMessage()
            ];
        }
    }

    public static function resendConfirmationCode($email)
    {
        try {
            $client = new CognitoIdentityProviderClient([
                'region' => COGNITO_REGION,
                'version' => 'latest'
            ]);

            $result = $client->resendConfirmationCode([
                'ClientId' => COGNITO_APP_CLIENT_ID,
                'Username' => $email
            ]);

            return [
                'success' => true,
                'message' => 'Confirmation code resent successfully. Please check your email.'
            ];
        } catch (AwsException $e) {
            $errorCode = $e->getAwsErrorCode();

            if ($errorCode === 'UserNotFoundException') {
                return [
                    'success' => false,
                    'error' => 'UserNotFoundException',
                    'message' => 'User not found. Please register again.'
                ];
            } elseif ($errorCode === 'LimitExceededException') {
                return [
                    'success' => false,
                    'error' => 'LimitExceededException',
                    'message' => 'Too many attempts. Please try again later.'
                ];
            }

            return [
                'success' => false,
                'error' => $errorCode ?? 'UnknownError',
                'message' => $e->getAwsErrorMessage() ?? $e->getMessage()
            ];
        }
    }

    public static function isUserConfirmed($email)
    {
        try {
            $client = new CognitoIdentityProviderClient([
                'region' => COGNITO_REGION,
                'version' => 'latest'
            ]);

            $result = $client->adminGetUser([
                'UserPoolId' => COGNITO_USER_POOL_ID,
                'Username' => $email
            ]);

            $userStatus = $result['UserStatus'] ?? '';

            return [
                'success' => true,
                'confirmed' => ($userStatus === 'CONFIRMED'),
                'status' => $userStatus
            ];
        } catch (AwsException $e) {
            return [
                'success' => false,
                'confirmed' => false,
                'error' => $e->getAwsErrorCode(),
                'message' => $e->getAwsErrorMessage() ?? $e->getMessage()
            ];
        }
    }

    public static function forgotPassword($email)
    {
        try {
            $client = new CognitoIdentityProviderClient([
                'region' => COGNITO_REGION,
                'version' => 'latest'
            ]);

            $result = $client->forgotPassword([
                'ClientId' => COGNITO_APP_CLIENT_ID,
                'Username' => $email
            ]);

            return [
                'success' => true,
                'message' => 'Password reset code sent to your email'
            ];
        } catch (AwsException $e) {
            return [
                'success' => false,
                'error' => $e->getAwsErrorCode(),
                'message' => $e->getAwsErrorMessage() ?? $e->getMessage()
            ];
        }
    }

    public static function confirmForgotPassword($email, $confirmation_code, $new_password)
    {
        try {
            $client = new CognitoIdentityProviderClient([
                'region' => COGNITO_REGION,
                'version' => 'latest'
            ]);

            $result = $client->confirmForgotPassword([
                'ClientId' => COGNITO_APP_CLIENT_ID,
                'Username' => $email,
                'ConfirmationCode' => $confirmation_code,
                'Password' => $new_password
            ]);

            return [
                'success' => true,
                'message' => 'Password reset successfully'
            ];
        } catch (AwsException $e) {
            return [
                'success' => false,
                'error' => $e->getAwsErrorCode(),
                'message' => $e->getAwsErrorMessage() ?? $e->getMessage()
            ];
        }
    }
}
