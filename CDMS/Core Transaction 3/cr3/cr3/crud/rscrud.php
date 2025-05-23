<?php
session_start();
require '../Database.php';
require '../functions.php';
$config = require '../config.php';

$conn = new Database($config['database']);
// $guest = $conn->query('SELECT s.*, r.*,g.* FROM reservationstatus s INNER JOIN reservations r ON r. reservation_id = s.reservation_id INNER JOIN guests g ON r.GuestID = g.GuestID')->fetchAll();
// dd($guest);
// ADD NEW GUEST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Field required
    $errors = [];
    $requiredFields = [
        'guest_name' => 'Guest Name',
        'phone' => 'Phone',
        'address' => 'Address',
        'date_of_birth' => 'Date of Birth',
        'email' => 'Email',
        'gender' => 'Gender',
        'nationality' => 'Nationality',
        'reservation' => 'Reservation',
        'check_in' => 'Check-In',
        'check_out' => 'Check-Out',
        'status' => 'Status',
        'room_id' => 'Room ID'
    ];

    // Sanitize input data
    $name = htmlspecialchars($_POST['guest_name']);
    $phone = htmlspecialchars($_POST['phone']);
    $address = htmlspecialchars($_POST['address']);
    $birthday = htmlspecialchars($_POST['date_of_birth']);
    $email = htmlspecialchars($_POST['email']);
    $gender = htmlspecialchars($_POST['gender']);
    $nationality = htmlspecialchars($_POST['nationality']);
    $reservation = htmlspecialchars($_POST['reservation']);
    $checkin = htmlspecialchars($_POST['check_in']);
    $checkout = htmlspecialchars($_POST['check_out']);
    $status = htmlspecialchars($_POST['status']);
    if ($_POST['add'] === 'true') {
          dd($_POST);
         $reservations = $conn->query('INSERT INTO reservations (room_id) VALUES (:room_id)', [
            ':room_id' => $_POST['room_id']
        ]);
        $guest = $conn->query('INSERT INTO guests (guest_name, phone, address, date_of_birth, email, gender, nationality, reservation, check_in, check_out status) VALUES (:guest_name, :phone, :address, :date_of_birth, :email, :gender, :nationality, :reservation, check_in, check_out, :status)', [
            ':guest_name' => $name,
            ':phone' => $phone,
             ':address' => $address,
             ':date_of_birth' => $birthday,
             ':email' => $email,
            'gender' => $gender,
            'nationality' => $nationality,
            'reservation' => $reservation,
            ':check_in' => $checkin,
            ':check_out' => $checkout,
            'status' => $status,
         ]);
        dd($guest);
         header('Location: ../cr3re/rs.php');
        exit();
}
// Deleting a reservation
if (isset($_POST['delete'])) {
    try {
        $reservationID = $_POST['reservationID'];

        $conn->query(
            'DELETE FROM reservations WHERE reservation_id = :reservation_id',
            [':reservation_id' => $reservationID]
        );

        header('Location: ../cr3re/rs.php');
        exit();
    } catch (PDOException $e) {
        dd("Error deleting reservation: " . $e->getMessage());
    }
}
}