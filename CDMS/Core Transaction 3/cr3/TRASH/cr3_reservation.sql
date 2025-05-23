CREATE DATABASE IF NOT EXISTS hotel_reservation;
USE hotel_reservation;

CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    features TEXT NOT NULL,
    image VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    reservation_date DATE NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    checkout_date DATE NOT NULL,
    guests INT NOT NULL,
    FOREIGN KEY (room_id) REFERENCES rooms(id)
);

INSERT INTO rooms (name, price, features, image) VALUES 
('Deluxe King Room', 200, 'Smart TV, High-Speed WiFi, Air Conditioning, King Bed', '1.jpg'),
('Executive Club Suite', 350, '55-inch Smart TV, Dining Area, Work Desk, Rain Shower', '2.jpg'),
('Presidential Suite', 500, 'Mini Bar, Luxury Bathtub, Private Gym, Entertainment Room', '3.jpg'),
('Overwater Villa', 700, 'Ocean View, Private Pool, Sunrise Balcony, King Bed', '4.jpg'),
('Presidential Suite', 1000, 'Mini Bar, Jacuzzi, Lounge Area, Private Chauffeur', '5.png');
