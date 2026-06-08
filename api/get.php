<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT 
            b.id, 
            u.name as user_name, 
            c.name as classroom_name, 
            b.date, 
            b.period, 
            b.status
          FROM bookings b
          JOIN users u ON b.user_id = u.id
          JOIN classrooms c ON b.classroom_id = c.id
          ORDER BY b.date DESC, b.period ASC";

$stmt = $db->prepare($query);
$stmt->execute();

$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "success" => true,
    "bookings" => $bookings
]);
?> 