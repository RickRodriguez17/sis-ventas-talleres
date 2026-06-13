CREATE DATABASE IF NOT EXISTS db_ventas
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE db_ventas;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS voided_sales;
DROP TABLE IF EXISTS order_status_history;
DROP TABLE IF EXISTS sale_details;
DROP TABLE IF EXISTS sales;
DROP TABLE IF EXISTS cash_movements;
DROP TABLE IF EXISTS cash_registers;
DROP TABLE IF EXISTS production_details;
DROP TABLE IF EXISTS production_batches;
DROP TABLE IF EXISTS combo_items;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS settings;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS roles;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id INT UNSIGNED NOT NULL,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(30) NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    last_login_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB;

CREATE TABLE settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    business_name VARCHAR(150) NOT NULL,
    logo VARCHAR(255) NULL,
    address VARCHAR(255) NULL,
    phone VARCHAR(50) NULL,
    currency VARCHAR(10) NOT NULL DEFAULT 'Bs',
    ticket_footer TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    slug VARCHAR(140) NOT NULL UNIQUE,
    description TEXT NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(170) NOT NULL UNIQUE,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) NULL,
    description TEXT NULL,
    is_combo TINYINT(1) NOT NULL DEFAULT 0,
    status ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id)
) ENGINE=InnoDB;

CREATE TABLE combo_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    combo_product_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    quantity INT UNSIGNED NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_combo_items_combo FOREIGN KEY (combo_product_id) REFERENCES products(id),
    CONSTRAINT fk_combo_items_product FOREIGN KEY (product_id) REFERENCES products(id),
    CONSTRAINT uq_combo_item UNIQUE (combo_product_id, product_id)
) ENGINE=InnoDB;

CREATE TABLE production_batches (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    production_date DATE NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_production_batches_user FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_production_batches_date (production_date)
) ENGINE=InnoDB;

CREATE TABLE production_details (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    batch_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    production_date DATE NOT NULL,
    produced_quantity INT UNSIGNED NOT NULL,
    remaining_quantity INT UNSIGNED NOT NULL,
    low_stock_alert INT UNSIGNED NOT NULL DEFAULT 5,
    unit_cost DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_production_details_batch FOREIGN KEY (batch_id) REFERENCES production_batches(id),
    CONSTRAINT fk_production_details_product FOREIGN KEY (product_id) REFERENCES products(id),
    INDEX idx_production_details_date_product (production_date, product_id),
    INDEX idx_production_details_remaining (production_date, remaining_quantity)
) ENGINE=InnoDB;

CREATE TABLE cash_registers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(30) NOT NULL UNIQUE,
    user_id INT UNSIGNED NOT NULL,
    opening_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    closing_amount DECIMAL(10,2) NULL,
    expected_amount DECIMAL(10,2) NULL,
    difference_amount DECIMAL(10,2) NULL,
    status ENUM('abierta', 'cerrada') NOT NULL DEFAULT 'abierta',
    opened_at DATETIME NOT NULL,
    closed_at DATETIME NULL,
    notes TEXT NULL,
    CONSTRAINT fk_cash_registers_user FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_cash_registers_user_status (user_id, status)
) ENGINE=InnoDB;

CREATE TABLE cash_movements (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cash_register_id INT UNSIGNED NOT NULL,
    type ENUM('ingreso', 'egreso') NOT NULL,
    concept VARCHAR(180) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    created_by INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cash_movements_register FOREIGN KEY (cash_register_id) REFERENCES cash_registers(id),
    CONSTRAINT fk_cash_movements_user FOREIGN KEY (created_by) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE sales (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(20) NOT NULL UNIQUE,
    user_id INT UNSIGNED NOT NULL,
    cash_register_id INT UNSIGNED NULL,
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    discount_type ENUM('none', 'percentage', 'fixed') NOT NULL DEFAULT 'none',
    discount_value DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    discount_total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    paid_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    change_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    payment_method ENUM('efectivo', 'qr', 'tarjeta', 'mixto') NOT NULL DEFAULT 'efectivo',
    observations TEXT NULL,
    order_status ENUM('pendiente', 'en_preparacion', 'listo', 'entregado') NOT NULL DEFAULT 'pendiente',
    status ENUM('activa', 'anulada') NOT NULL DEFAULT 'activa',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_sales_user FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT fk_sales_cash_register FOREIGN KEY (cash_register_id) REFERENCES cash_registers(id),
    INDEX idx_sales_created_status (created_at, status),
    INDEX idx_sales_order_status (order_status)
) ENGINE=InnoDB;

CREATE TABLE sale_details (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sale_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    discount_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_sale_details_sale FOREIGN KEY (sale_id) REFERENCES sales(id),
    CONSTRAINT fk_sale_details_product FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB;

CREATE TABLE order_status_history (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sale_id INT UNSIGNED NOT NULL,
    previous_status ENUM('pendiente', 'en_preparacion', 'listo', 'entregado') NULL,
    new_status ENUM('pendiente', 'en_preparacion', 'listo', 'entregado') NOT NULL,
    changed_by INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_order_status_history_sale FOREIGN KEY (sale_id) REFERENCES sales(id),
    CONSTRAINT fk_order_status_history_user FOREIGN KEY (changed_by) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE voided_sales (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sale_id INT UNSIGNED NOT NULL,
    voided_by INT UNSIGNED NOT NULL,
    reason TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_voided_sales_sale FOREIGN KEY (sale_id) REFERENCES sales(id),
    CONSTRAINT fk_voided_sales_user FOREIGN KEY (voided_by) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    action VARCHAR(120) NOT NULL,
    entity VARCHAR(120) NULL,
    entity_id BIGINT UNSIGNED NULL,
    description TEXT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_audit_logs_user FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_audit_logs_entity (entity, entity_id),
    INDEX idx_audit_logs_created_at (created_at)
) ENGINE=InnoDB;

INSERT INTO roles (id, name, slug) VALUES
(1, 'Administrador', 'administrador'),
(2, 'Cajero', 'cajero');

INSERT INTO users (id, role_id, name, email, password, status) VALUES
(1, 1, 'Administrador Demo', 'admin@demo.com', '$2y$10$96kN7wa8lPQLEHCeAtex2u/xSacu2hRf4Fqf9IRXpJaYzGbD9ArVa', 1),
(2, 2, 'Cajero Demo', 'cajero@demo.com', '$2y$10$L0Q4MIIh4aK.oSLnsraTH.If4KHwkuEHAf.KO17QrEEuZVzJP7DIq', 1);

INSERT INTO settings (business_name, address, phone, currency, ticket_footer) VALUES
('FastFood Ventas', 'Av. Principal #123', '70000000', 'Bs', 'Gracias por su compra. Recoja su pedido cuando aparezca en pantalla.');

INSERT INTO categories (id, name, slug, description, status) VALUES
(1, 'Hamburguesas', 'hamburguesas', 'Hamburguesas simples y especiales', 1),
(2, 'Pizzas', 'pizzas', 'Pizzas personales y familiares', 1),
(3, 'Pollos', 'pollos', 'Pollos y acompañamientos', 1),
(4, 'Bebidas', 'bebidas', 'Gaseosas, jugos y agua', 1),
(5, 'Combos', 'combos', 'Promociones de productos combinados', 1),
(6, 'Postres', 'postres', 'Postres para acompañar pedidos', 1);

INSERT INTO products (id, category_id, name, slug, price, description, is_combo, status) VALUES
(1, 1, 'Hamburguesa Clásica', 'hamburguesa-clasica', 18.00, 'Pan, carne, queso y vegetales', 0, 'activo'),
(2, 2, 'Pizza Personal', 'pizza-personal', 25.00, 'Pizza individual de la casa', 0, 'activo'),
(3, 3, 'Pollo Broaster', 'pollo-broaster', 22.00, 'Porción de pollo con papas', 0, 'activo'),
(4, 4, 'Gaseosa 500ml', 'gaseosa-500ml', 8.00, 'Bebida fría individual', 0, 'activo'),
(5, 5, 'Combo Hamburguesa', 'combo-hamburguesa', 25.00, 'Hamburguesa clásica con gaseosa', 1, 'activo'),
(6, 6, 'Brownie', 'brownie', 10.00, 'Postre de chocolate', 0, 'activo');

INSERT INTO combo_items (combo_product_id, product_id, quantity) VALUES
(5, 1, 1),
(5, 4, 1);

INSERT INTO production_batches (production_date, user_id, notes) VALUES
(CURDATE(), 1, 'Producción inicial de demostración');

SET @production_batch_id = LAST_INSERT_ID();

INSERT INTO production_details (batch_id, product_id, production_date, produced_quantity, remaining_quantity, low_stock_alert, unit_cost) VALUES
(@production_batch_id, 1, CURDATE(), 60, 60, 10, 9.50),
(@production_batch_id, 2, CURDATE(), 25, 25, 5, 13.00),
(@production_batch_id, 3, CURDATE(), 20, 20, 5, 12.00),
(@production_batch_id, 4, CURDATE(), 80, 80, 12, 4.00),
(@production_batch_id, 6, CURDATE(), 30, 30, 6, 5.00);
