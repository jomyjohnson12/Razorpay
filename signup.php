<?php
header('Content-Type: application/json'); 
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Read and decode JSON input
    $jsonInput = file_get_contents("php://input");
    error_log("Raw input: " . $jsonInput); 

    $input = json_decode($jsonInput, true);

    if (!$input) {
        echo json_encode(["status" => "error", "message" => "Invalid JSON format."]);
        exit;
    }

    if (!isset($input['fullname'], $input['email'], $input['password'])) {
        echo json_encode(["status" => "error", "message" => "Missing required fields."]);
        exit;
    }

    $fullname = trim($input['fullname']);
    $email = trim($input['email']);
    $password = password_hash($input['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $checkEmail = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $checkEmail->store_result();

    if ($checkEmail->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Email already exists!"]);
    } else {
        // Insert user
        $insertUser = $conn->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
        $insertUser->bind_param("sss", $fullname, $email, $password);

        if ($insertUser->execute()) {
            echo json_encode(["status" => "success", "message" => "Signup successful!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
        }
        $insertUser->close();
    }

    $checkEmail->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>
