<?php
require_once 'pb.config.php';
/**Your anger may wash over me, but it won't breach the fortress of my inner peace
Credits @Nuubkid & @CharlieDev34
**/
try {
    $response = array();

    // Retrieve and sanitize input data
    $login = filter_input(INPUT_POST, '_username', FILTER_SANITIZE_STRING);
    $senha = filter_input(INPUT_POST, '_password', FILTER_SANITIZE_STRING);
    $hwid = filter_input(INPUT_POST, '_hwid', FILTER_SANITIZE_STRING);

    if (!$login || !$senha ) {
        $response['Status'] = false;
        $response['Message'] = 'Invalid input data. Please provide valid values for _username, _password';
    } else {
        // Prepare and execute the login query
        $check_login = $pdo->prepare('SELECT * FROM players WHERE login = :username AND password = :password LIMIT 1');
        $check_login->bindValue(':username', $login);
        $check_login->bindValue(':password', encripitar($senha));
        $check_login->execute();

        $result = $check_login->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $token = bin2hex(random_bytes(15));
            $updater_login = $pdo->prepare("UPDATE players SET token = :token, hwid = :hwid WHERE player_id = :id");
            $updater_login->bindValue(':id', $result['player_id']);
            $updater_login->bindValue(':token', $token);
            $updater_login->bindValue(':hwid', _uname($hwid));
            $updater_login->execute();

            $response['Status'] = true;
            $response['Player_token'] = $token;
			
			
			$player_name_query = $pdo->prepare("SELECT player_name FROM players WHERE player_id = :id");
    $player_name_query->bindValue(':id', $result['player_id']);
    $player_name_query->execute();
    $player_result = $player_name_query->fetch(PDO::FETCH_ASSOC);

    $response['Status'] = true;
    $response['Player_token'] = $token;
    $response['Player_name'] = $player_result['player_name'];
        } else {
            $response['Status'] = false;
            $response['Message'] = 'Invalid login credentials';
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
} catch (PDOException $e) {
    // Handle database-related errors
    $response = array(
        'Status' => false,
        'Message' => 'Database error: ' . $e->getMessage()
    );

    header('Content-Type: application/json');
    echo json_encode($response);
}
?>
