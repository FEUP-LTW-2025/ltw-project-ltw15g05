CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    name TEXT NOT NULL,
    email TEXT UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE roles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT UNIQUE NOT NULL CHECK(name IN ('freelancer', 'client', 'admin'))
);

CREATE TABLE user_roles (
    user_id INTEGER NOT NULL,
    role_id INTEGER NOT NULL,
    PRIMARY KEY (user_id, role_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);

CREATE TABLE services (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    freelancer_id INTEGER NOT NULL,
    title TEXT NOT NULL,
    description TEXT NOT NULL,
    category_id INTEGER NOT NULL,
    price REAL NOT NULL,
    delivery_time INTEGER NOT NULL, -- days
    photo_style TEXT NOT NULL, -- Portrait, Landscape
    equipment_provided BOOLEAN NOT NULL DEFAULT 0,
    location TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (freelancer_id) REFERENCES users(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT UNIQUE NOT NULL
);

CREATE TABLE transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    service_id INTEGER NOT NULL,
    client_id INTEGER NOT NULL,
    freelancer_id INTEGER NOT NULL,
    status TEXT CHECK(status IN ('pending', 'completed', 'canceled')) NOT NULL,
    payment_amount REAL NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id),
    FOREIGN KEY (client_id) REFERENCES users(id),
    FOREIGN KEY (freelancer_id) REFERENCES users(id)
);

CREATE TABLE reviews (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    transaction_id INTEGER NOT NULL,
    client_id INTEGER NOT NULL,
    freelancer_id INTEGER NOT NULL,
    rating INTEGER CHECK(rating BETWEEN 1 AND 5) NOT NULL,
    comment TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id),
    FOREIGN KEY (client_id) REFERENCES users(id),
    FOREIGN KEY (freelancer_id) REFERENCES users(id)
);

CREATE TABLE messages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    sender_id INTEGER NOT NULL,
    receiver_id INTEGER NOT NULL,
    content TEXT NOT NULL,
    sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id),
    FOREIGN KEY (receiver_id) REFERENCES users(id)
);

CREATE TABLE admin_actions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    admin_id INTEGER NOT NULL,
    action TEXT NOT NULL,
    target_user_id INTEGER,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(id),
    FOREIGN KEY (target_user_id) REFERENCES users(id)
);

CREATE TABLE portfolio (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    freelancer_id INTEGER NOT NULL,
    image_url TEXT NOT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (freelancer_id) REFERENCES users(id)
);

CREATE TABLE booking_requests (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    client_id INTEGER NOT NULL,
    freelancer_id INTEGER NOT NULL,
    event_date DATE NOT NULL,
    location TEXT,
    details TEXT,
    status TEXT CHECK(status IN ('pending', 'accepted', 'declined')) DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id),
    FOREIGN KEY (freelancer_id) REFERENCES users(id)
);




--inserts
INSERT INTO users (username, password, name, created_at) VALUES 
('john_doe', 'securepass123', 'John Doe', '2023-01-15 09:30:00'),
('jane_smith', 'janespassword', 'Jane Smith', '2023-02-20 14:15:00'),
('mike_johnson', 'mikepass789', 'Mike Johnson', '2023-03-10 11:45:00'),
('sarah_williams', 'sarah123!', 'Sarah Williams', '2023-04-05 16:20:00'),
('david_brown', 'browndavid', 'David Brown', '2023-05-12 10:00:00'),
('emily_clark', 'clarkemily', 'Emily Clark', '2023-06-18 13:25:00'),
('robert_taylor', 'taylorrobert', 'Robert Taylor', '2023-07-22 08:40:00'),
('lisa_miller', 'millerlisa', 'Lisa Miller', '2023-08-30 17:10:00'),
('admin_user', 'adminpass123', 'Admin User', '2023-01-01 00:00:00'),
('alex_green', 'greenalex', 'Alex Green', '2023-09-15 12:35:00');

INSERT INTO categories (name) VALUES
('Photography'),
('Videography'),
('Editing'),
('Graphic Design'),
('Event Coverage'),
('Drone Services');

INSERT INTO services (
    freelancer_id, title, description, category_id, price,
    delivery_time, photo_style, equipment_provided, location
) VALUES
(1, 'Wedding Photography Package', 'Full-day wedding coverage including edited photos and online gallery.', 1, 1500.00, 7, 'Portrait', 1, 'Los Angeles, CA'),
(2, 'Event Videography', 'Professional videography for corporate or private events. Includes 4K video.', 2, 1200.00, 10, 'Landscape', 1, 'New York, NY'),
(3, 'Portrait Editing Services', 'High-end retouching for professional headshots and portraits.', 3, 250.00, 3, 'Portrait', 0, NULL),
(4, 'Logo & Brand Kit Design', 'Custom logo design with brand color palette and typography.', 4, 500.00, 5, 'Landscape', 0, NULL),
(5, 'Birthday Party Photography', '3-hour event shoot with 50+ edited images delivered digitally.', 1, 400.00, 4, 'Portrait', 1, 'Austin, TX'),
(6, 'Drone Aerial Shots', 'High-quality drone footage for real estate or commercial use.', 6, 600.00, 2, 'Landscape', 1, 'San Diego, CA'),
(7, 'Engagement Photoshoot', '1-hour outdoor shoot with 20 edited photos.', 1, 300.00, 3, 'Portrait', 1, 'Seattle, WA');

-- Assign roles to users (assuming role IDs: 1=freelancer, 2=client, 3=admin)
INSERT INTO user_roles (user_id, role_id) VALUES
(1, 1), -- John Doe is a freelancer
(2, 1), -- Jane Smith is a freelancer
(3, 2), -- Mike Johnson is a client
(4, 1), -- Sarah Williams is a freelancer
(4, 2), -- Sarah Williams is also a client
(5, 2), -- David Brown is a client
(6, 1), -- Emily Clark is a freelancer
(7, 2), -- Robert Taylor is a client
(8, 1), -- Lisa Miller is a freelancer
(9, 3), -- Admin User is an admin
(10, 2); -- Alex Green is a client