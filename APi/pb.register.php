<?php
require_once 'pb.config.php';
/**Your anger may wash over me, but it won't breach the fortress of my inner peace
Credits @Nuubkid & @CharlieDev34
**/
// Function to hash the password
// Function to get the client's real IP address
function getRealIpAddr() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        // Check for IP from shared Internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // Check for the IP address passed from a proxy or load balancer
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        // Get the remote IP address
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function _ip($n) {
    if (is_null($n)) {
        $n = '';
    }
    $n = preg_replace('/[^[:alnum:.]]/', '', $n);
    $n = trim($n);
    return $n;
}

function _username($n) {
    if (is_null($n)) {
        $n = '';
    }
    $n = preg_replace('/[^[:alnum:]]/', '', $n);
    $n = trim($n);
    $n = htmlspecialchars($n, ENT_QUOTES, 'UTF-8');
    return mb_strtolower($n);
}

$response = []; // Initialize a response array

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ip = getRealIpAddr();
    $txtUsername = _username(filter_input(INPUT_POST, 'username', FILTER_UNSAFE_RAW));
    $txtPass = encripitar(filter_input(INPUT_POST, 'pass', FILTER_UNSAFE_RAW)); // Hash the password

    // Check if the username and password meet the length requirements
    if (strlen($txtUsername) < 6 || strlen($txtPass) < 6) {
        $response['status'] = 'error';
        $response['message'] = 'Username and password must be at least 6 characters long';
    } else {
        // Generate CSRF token and store it in the session
        $csrfToken = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $csrfToken;

        // Check if the username already exists in the database
        $checkUsernameQuery = $pdo->prepare("SELECT COUNT(*) FROM players WHERE login = :username");
        $checkUsernameQuery->bindValue(":username", _username(mb_strtolower($txtUsername)));
        $checkUsernameQuery->execute();
        $usernameExists = (bool)$checkUsernameQuery->fetchColumn();

        if ($usernameExists) {
            $response['status'] = 'error';
            $response['message'] = 'Username already exists';
        } else {
            // Continue with the registration process if the username is not found
            $token = bin2hex(random_bytes(32));
            $current_date = date('Y-m-d');
            $current_time = date('H:i:s');
            $TABLE_SELECT = "login, password, token, lastip, registration_date"; // Fields to be inserted
            $TABLE_VALUE = ":login, :pass, :token, :ip, :reg_date"; // Values for insertion

            // Format the date and time in the expected format
            $formatted_datetime = $current_date . ' ' . $current_time;

            $REGISTER_ADD = $pdo->prepare("INSERT INTO players(" . $TABLE_SELECT . ") VALUES (" . $TABLE_VALUE . ")");
            $REGISTER_ADD->bindValue(":login", _username(mb_strtolower($txtUsername)));
            $REGISTER_ADD->bindValue(":pass", $txtPass); // Use the hashed password
            $REGISTER_ADD->bindValue(":token", _username($token));
            $REGISTER_ADD->bindValue(":ip", _ip($ip));

            // Bind the formatted date and time
            $REGISTER_ADD->bindValue(":reg_date", $formatted_datetime);

            if ($REGISTER_ADD->execute()) {
                $response['status'] = 'success';
                $response['message'] = 'Registration Successful';
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Registration Failed';
            }
        }
    }

    // Return the response as JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit(); // Exit to prevent further output
}

// If the code reaches here, there was no valid registration request
$response['status'] = 'error';
$response['message'] = 'Invalid registration request';

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
