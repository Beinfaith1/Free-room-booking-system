<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    $action = $data->action ?? '';
    
    switch ($action) {
        case 'login':
            $email = $data->email ?? '';
            $password = $data->password ?? '';
            
            if (empty($email) || empty($password)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Email and password are required'
                ]);
                exit;
            }
            
            $query = "SELECT id, name, email, password, is_admin FROM users WHERE email = :email";
            $stmt = $db->prepare($query);
            $stmt->bindParam(":email", $email);
            $stmt->execute();
            
            if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if (password_verify($password, $user['password'])) {
                    // Remove password from response
                    unset($user['password']);
                    echo json_encode([
                        'success' => true,
                        'message' => 'Login successful',
                        'user' => $user
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid password'
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'User not found'
                ]);
            }
            break;
            
        case 'register':
            $name = $data->name ?? '';
            $email = $data->email ?? '';
            $password = $data->password ?? '';
            
            if (empty($name) || empty($email) || empty($password)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'All fields are required'
                ]);
                exit;
            }
            
            // Check if email already exists
            $query = "SELECT id FROM users WHERE email = :email";
            $stmt = $db->prepare($query);
            $stmt->bindParam(":email", $email);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Email already exists'
                ]);
                exit;
            }
            
            // Create new user
            $query = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
            $stmt = $db->prepare($query);
            
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $hashedPassword);
            
            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Registration successful'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Registration failed'
                ]);
            }
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action'
            ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
}
?> 