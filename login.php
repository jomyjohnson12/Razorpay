<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once './db_connection.php';

// Ensure request is POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
    exit();
}

// Decode JSON input
$input = json_decode(file_get_contents("php://input"), true);

// Validate input
if (!isset($input['email'], $input['password'])) {
    echo json_encode(["status" => "error", "message" => "Email and password are required."]);
    exit();
}

$email = $input['email'];
$password = $input['password'];

// Prepare SQL statement
$sql = "SELECT id, password FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    if (password_verify($password, $row['password'])) {
        $_SESSION['user_id'] = $row['id'];

        echo json_encode([
            "status" => "success",
            "message" => "Login successful",
            "redirect" => "index.php"
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid password."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "No user found with this email."]);
}

// Close connection
$stmt->close();
$conn->close();
?>
