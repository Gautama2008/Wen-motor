-- Create database
CREATE DATABASE IF NOT EXISTS motor_shop;
USE motor_shop;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Motors table
CREATE TABLE motors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(15,2) NOT NULL,
    category ENUM('sport', 'matic', 'bebek', 'big-bike') NOT NULL,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample motors
INSERT INTO motors (name, description, price, category, image) VALUES
('Yamaha R15', 'Motor sport dengan performa tinggi dan desain aerodinamis', 35000000, 'sport', 'images/motors/r15.jpg'),
('Yamaha NMAX', 'Skutik premium dengan teknologi Blue Core', 32000000, 'matic', 'images/motors/nmax.jpg'),
('Yamaha Vixion', 'Motor bebek sport dengan mesin bertenaga', 25000000, 'bebek', 'images/motors/vixion.jpg'),
('Yamaha MT-25', 'Naked bike dengan karakter agresif', 55000000, 'sport', 'images/motors/mt25.jpg'),
('Yamaha Aerox', 'Skutik sporty untuk anak muda', 28000000, 'matic', 'images/motors/aerox.jpg'),
('Yamaha Jupiter MX', 'Motor bebek sport legendaris', 20000000, 'bebek', 'images/motors/jupiter-mx.jpg'),
('Yamaha R25', 'Sport bike dengan teknologi terdepan', 45000000, 'sport', 'images/motors/r25.jpg'),
('Yamaha Lexi', 'Skutik stylish untuk mobilitas harian', 22000000, 'matic', 'images/motors/lexi.jpg'),
('Yamaha MT-09', 'Big bike dengan performa luar biasa', 250000000, 'big-bike', 'images/motors/mt09.jpg');

-- Orders table (Pesanan)
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    motor_id INT NOT NULL,
    quantity INT DEFAULT 1,
    total_price DECIMAL(15,2) NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    customer_address TEXT NOT NULL,
    status ENUM('pending', 'confirmed', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (motor_id) REFERENCES motors(id)
);

-- Customers table (Data Pembeli)
CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    id_number VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sales table (Penjualan)
CREATE TABLE sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    customer_id INT NOT NULL,
    motor_id INT NOT NULL,
    sale_price DECIMAL(15,2) NOT NULL,
    payment_method ENUM('cash', 'credit', 'bank_transfer') NOT NULL,
    payment_status ENUM('pending', 'paid', 'partial') DEFAULT 'pending',
    sale_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (motor_id) REFERENCES motors(id)
);

-- Stock table (Stok Motor)
CREATE TABLE stock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    motor_id INT NOT NULL,
    quantity INT DEFAULT 0,
    min_stock INT DEFAULT 5,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (motor_id) REFERENCES motors(id)
);

-- Insert sample user (password: 123456)
INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@yamaha.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample stock for all motors
INSERT INTO stock (motor_id, quantity, min_stock) VALUES
(1, 15, 5), (2, 20, 5), (3, 12, 5), (4, 8, 3), (5, 18, 5),
(6, 10, 5), (7, 6, 3), (8, 25, 5), (9, 3, 2);