<?php
include("./header.php");
include("./session_page.php");
require_once("config.php");
require_once("db-settings.php");
require_once("cognito.php");

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Handle Unauthorize
    if (isset($_POST['USER_ID_UNAUTH']) && !empty($_POST['USER_ID_UNAUTH'])) {
        $result = unauthorizeUser($_POST['USER_ID_UNAUTH']);
        echo "<script>
            alert('" . $result . "');
            window.location.href='user_authorization.php';
        </script>";
        exit();
    }
    
    // Handle Authorize
    if (isset($_POST['USER_ID_AUTH']) && !empty($_POST['USER_ID_AUTH'])) {
        $result = authorizeUser($_POST['USER_ID_AUTH']);
        echo "<script>
            alert('" . $result . "');
            window.location.href='user_authorization.php';
        </script>";
        exit();
    }
    
    // Handle Delete User (from both database and Cognito)
    if (isset($_POST['USER_ID_DELETE']) && !empty($_POST['USER_ID_DELETE'])) {
        $user_id = $_POST['USER_ID_DELETE'];
        $user_email = $_POST['USER_EMAIL'] ?? '';
        
        $result = deleteUserCompletely($user_id, $user_email);
        echo "<script>
            alert('" . $result . "');
            window.location.href='user_authorization.php';
        </script>";
        exit();
    }
}

// Get user data
$resultFound = getUserData();
?>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="assets/css/table.css">
  
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
  <script src="assets/js/script.js"></script>
  
  <style>
    .action-buttons {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }
    .btn-auth {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 3px;
        cursor: pointer;
        font-size: 12px;
    }
    .btn-unauth {
        background-color: #ffc107;
        color: black;
        border: none;
        padding: 5px 10px;
        border-radius: 3px;
        cursor: pointer;
        font-size: 12px;
    }
    .btn-delete {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 3px;
        cursor: pointer;
        font-size: 12px;
    }
    .btn-auth:hover, .btn-unauth:hover, .btn-delete:hover {
        opacity: 0.8;
    }
    .user-email {
        font-size: 11px;
        color: #666;
        margin-top: 2px;
    }
  </style>
</head>

<br>

<div style="margin:5px 30px 30px 30px;">
    <h1 style="text-align:center;color:#D98880;">User Authorization</h1>
    <hr>
    
    <div style="max-height: calc(96.2vh - 96.2px);overflow:auto;">
        <div class="table" style="width: device-width;">
            <div class="row header blue" style="line-height: 25px;font-size: 12px;position: sticky;top: 0;">
                <div class="cell" style="width: device-width;">No.</div>
                <div class="cell" style="width: device-width;">USER_ID</div>
                <div class="cell" style="width: device-width;">USERNAME</div>
                <div class="cell" style="width: device-width;">FIRST NAME</div>
                <div class="cell" style="width: device-width;">LAST NAME</div>
                <div class="cell" style="width: device-width;">EMAIL</div>
                <div class="cell" style="width: device-width;">ACTIVE</div>
                <div class="cell" style="width: device-width;">USER ROLE</div>
                <div class="cell" style="width: device-width;">COGNITO VERIFIED</div>
                <div class="cell" style="width: device-width;">ACTIONS</div>
            </div>
          
            <?php
            setlocale(LC_MONETARY,"en_US");
            $number = 0;  
            foreach($resultFound as $row) {
                $number++;
                echo "<div class='row'>";
                echo "<div class='cell'>".$number."</div>";
                
                // Display user data
                $user_id = '';
                $user_email = '';
                foreach($row as $key => $value) {
                    if ($key === 'USER_ID') $user_id = $value;
                    if ($key === 'EMAIL') $user_email = $value;
                    echo "<div class='cell'>".htmlspecialchars($value)."</div>";
                }
            ?>
            
            <div class='cell'>
                <div class="action-buttons">
                    <!-- Authorize Button -->
                    <form method="POST" style="display:inline;" onsubmit="return confirmAuthorize('<?php echo $user_id; ?>')">
                        <input type="hidden" name="USER_ID_AUTH" value="<?php echo $user_id; ?>">
                        <button type="submit" class="btn-auth">Authorize</button>
                    </form>
                    
                    <!-- Unauthorize Button -->
                    <form method="POST" style="display:inline;" onsubmit="return confirmUnauthorize('<?php echo $user_id; ?>')">
                        <input type="hidden" name="USER_ID_UNAUTH" value="<?php echo $user_id; ?>">
                        <button type="submit" class="btn-unauth">Unauthorize</button>
                    </form>
                    
                    <!-- Delete Button (from both DB and Cognito) -->
                    <form method="POST" style="display:inline;" onsubmit="return confirmDelete('<?php echo $user_id; ?>', '<?php echo htmlspecialchars($user_email); ?>')">
                        <input type="hidden" name="USER_ID_DELETE" value="<?php echo $user_id; ?>">
                        <input type="hidden" name="USER_EMAIL" value="<?php echo htmlspecialchars($user_email); ?>">
                        <button type="submit" class="btn-delete">Delete</button>
                    </form>
                </div>
                <div class="user-email"><?php echo htmlspecialchars($user_email); ?></div>
            </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>

<script>
// Confirmation dialogs
function confirmAuthorize(userId) {
    return confirm("Are you sure you want to AUTHORIZE user: " + userId + "?");
}

function confirmUnauthorize(userId) {
    return confirm("Are you sure you want to UNAUTHORIZE user: " + userId + "?");
}

function confirmDelete(userId, email) {
    return confirm("⚠️ WARNING: Are you sure you want to PERMANENTLY DELETE user: " + userId + " (" + email + ")?\n\nThis will delete the user from BOTH the database AND Cognito. This action CANNOT be undone!");
}
</script>

<?php
// ONLY keep the deleteUserCompletely function here
// REMOVE unauthorizeUser and authorizeUser functions since they're in functions.php

// Function to completely delete user from both database and Cognito
function deleteUserCompletely($user_id, $user_email) {
    global $mysqli;
    
    // Start transaction
    $mysqli->begin_transaction();
    
    try {
        // First, get user details if email not provided
        if (empty($user_email)) {
            $stmt = $mysqli->prepare("SELECT EMAIL, cognito_verified FROM users WHERE USER_ID = ?");
            $stmt->bind_param("s", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $user_email = $user['EMAIL'] ?? '';
            $cognito_verified = $user['cognito_verified'] ?? 0;
            $stmt->close();
        } else {
            // Get cognito_verified status
            $stmt = $mysqli->prepare("SELECT cognito_verified FROM users WHERE USER_ID = ?");
            $stmt->bind_param("s", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $cognito_verified = $user['cognito_verified'] ?? 0;
            $stmt->close();
        }
        
        // 1. Delete from Cognito if user was verified
        $cognito_delete_success = true;
        $cognito_message = "";
        
        if ($cognito_verified == 1 && !empty($user_email)) {
            try {
                require_once 'vendor/autoload.php';
                
                // Check if constants are defined
                if (!defined('COGNITO_REGION') || !defined('COGNITO_USER_POOL_ID')) {
                    throw new Exception("Cognito configuration constants not defined");
                }
                
                $client = new Aws\CognitoIdentityProvider\CognitoIdentityProviderClient([
                    'region' => COGNITO_REGION,
                    'version' => 'latest'
                ]);
                
                // Delete user from Cognito
                $client->adminDeleteUser([
                    'UserPoolId' => COGNITO_USER_POOL_ID,
                    'Username' => $user_email
                ]);
                
                error_log("Cognito user deleted by admin: $user_email");
                $cognito_message = " and Cognito";
                
            } catch (Exception $e) {
                $error_message = $e->getMessage();
                error_log("Cognito delete failed for user {$user_email}: " . $error_message);
                
                // Check if user doesn't exist in Cognito (already deleted)
                if (strpos($error_message, 'UserNotFoundException') !== false) {
                    $cognito_message = " (Cognito user not found)";
                } else {
                    $cognito_delete_success = false;
                    throw new Exception("Cognito delete failed: " . $error_message);
                }
            }
        } else {
            $cognito_message = " (not in Cognito)";
        }
        
        // 2. Delete from database
        $delete_stmt = $mysqli->prepare("DELETE FROM users WHERE USER_ID = ?");
        $delete_stmt->bind_param("s", $user_id);
        
        if (!$delete_stmt->execute()) {
            throw new Exception("Database delete failed: " . $delete_stmt->error);
        }
        
        $affected_rows = $delete_stmt->affected_rows;
        $delete_stmt->close();
        
        if ($affected_rows === 0) {
            throw new Exception("User not found in database");
        }
        
        // Commit transaction
        $mysqli->commit();
        
        return "User $user_id successfully deleted from database$cognito_message!";
        
    } catch (Exception $e) {
        $mysqli->rollback();
        error_log("User deletion failed for ID {$user_id}: " . $e->getMessage());
        return "Error deleting user: " . $e->getMessage();
    }
}
?>