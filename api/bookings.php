<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['user_id'])) {
        getUserBookings($db, $_GET['user_id']);
    } else if (isset($_GET['date'])) {
        getAvailableClassrooms($db, $_GET['date']);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    createBooking($db, $data);
} else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    deleteBooking($db, $data);
}

function getAvailableClassrooms($db, $date) {
    $query = "SELECT c.*, 
              GROUP_CONCAT(b.period) as booked_periods
              FROM classrooms c
              LEFT JOIN bookings b ON c.id = b.classroom_id AND b.booking_date = :date
              GROUP BY c.id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(":date", $date);
    $stmt->execute();
    
    $classrooms = array();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $booked_periods = $row['booked_periods'] ? explode(',', $row['booked_periods']) : array();
        $row['booked_periods'] = $booked_periods;
        $classrooms[] = $row;
    }
    
    echo json_encode($classrooms);
}

function getUserBookings($db, $user_id) {
    $query = "SELECT b.*, c.name as classroom_name, c.capacity
              FROM bookings b
              JOIN classrooms c ON b.classroom_id = c.id
              WHERE b.user_id = :user_id
              ORDER BY b.booking_date DESC, b.period ASC";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(":user_id", $user_id);
    $stmt->execute();
    
    $bookings = array();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $bookings[] = $row;
    }
    
    echo json_encode($bookings);
}

function createBooking($db, $data) {
    if (!isset($data->user_id) || !isset($data->classroom_id) || !isset($data->date) || !isset($data->period)) {
        echo json_encode(array("message" => "Missing required fields"));
        return;
    }
    
    // Check if the classroom is already booked for this period
    $query = "SELECT id FROM bookings 
              WHERE classroom_id = :classroom_id 
              AND booking_date = :date 
              AND period = :period";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(":classroom_id", $data->classroom_id);
    $stmt->bindParam(":date", $data->date);
    $stmt->bindParam(":period", $data->period);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(array("message" => "Classroom already booked for this period"));
        return;
    }
    
    // Create the booking
    $query = "INSERT INTO bookings (user_id, classroom_id, booking_date, period) 
              VALUES (:user_id, :classroom_id, :date, :period)";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(":user_id", $data->user_id);
    $stmt->bindParam(":classroom_id", $data->classroom_id);
    $stmt->bindParam(":date", $data->date);
    $stmt->bindParam(":period", $data->period);
    
    if ($stmt->execute()) {
        echo json_encode(array("message" => "Booking created successfully"));
    } else {
        echo json_encode(array("message" => "Failed to create booking"));
    }
}

function deleteBooking($db, $data) {
    if (!isset($data->booking_id)) {
        echo json_encode(array("message" => "Missing booking ID"));
        return;
    }
    
    $query = "DELETE FROM bookings WHERE id = :booking_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":booking_id", $data->booking_id);
    
    if ($stmt->execute()) {
        echo json_encode(array("message" => "Booking deleted successfully"));
    } else {
        echo json_encode(array("message" => "Failed to delete booking"));
    }
}
?> 