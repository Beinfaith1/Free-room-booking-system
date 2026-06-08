<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Check if user is admin
function isAdmin($db, $userId) {
    $query = "SELECT is_admin FROM users WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":id", $userId);
    $stmt->execute();
    
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        return $row['is_admin'] == 1;
    }
    return false;
}

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'getStats':
            // Get total classrooms
            $query = "SELECT COUNT(*) as total FROM classrooms";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $classrooms = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Get active bookings
            $query = "SELECT COUNT(*) as total FROM bookings WHERE status = 'active'";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $activeBookings = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Get total users
            $query = "SELECT COUNT(*) as total FROM users";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            echo json_encode([
                'success' => true,
                'stats' => [
                    'totalClassrooms' => $classrooms,
                    'activeBookings' => $activeBookings,
                    'totalUsers' => $totalUsers
                ]
            ]);
            break;
            
        case 'getBookings':
            $date = $_GET['date'] ?? '';
            $status = $_GET['status'] ?? 'all';
            
            $query = "SELECT b.*, u.name as user_name, c.name as classroom_name 
                     FROM bookings b 
                     JOIN users u ON b.user_id = u.id 
                     JOIN classrooms c ON b.classroom_id = c.id 
                     WHERE 1=1";
            
            if ($date) {
                $query .= " AND b.date = :date";
            }
            if ($status !== 'all') {
                $query .= " AND b.status = :status";
            }
            
            $query .= " ORDER BY b.date DESC, b.period ASC";
            
            $stmt = $db->prepare($query);
            
            if ($date) {
                $stmt->bindParam(":date", $date);
            }
            if ($status !== 'all') {
                $stmt->bindParam(":status", $status);
            }
            
            $stmt->execute();
            $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'bookings' => $bookings
            ]);
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action'
            ]);
    }
} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    $action = $data->action ?? '';
    
    switch ($action) {
        case 'cancelBooking':
            $bookingId = $data->bookingId ?? 0;
            
            if (!$bookingId) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Booking ID is required'
                ]);
                exit;
            }
            
            // Update booking status to cancelled
            $query = "UPDATE bookings SET status = 'cancelled' WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(":id", $bookingId);
            
            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Booking cancelled successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to cancel booking'
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