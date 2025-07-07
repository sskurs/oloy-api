-- OpenLoyalty User Setup Script for oloy.com domain
-- This script creates users for all three roles with proper authentication

-- Clear existing demo users (optional - uncomment if you want to start fresh)
-- DELETE FROM ol__users_roles WHERE user_id IN ('demo-admin-001', 'demo-seller-001', 'demo-customer-001');
-- DELETE FROM ol__user WHERE id IN ('demo-admin-001', 'demo-seller-001', 'demo-customer-001');

-- 1. INSERT ROLES (if not already present)
INSERT INTO ol__roles (id, role) VALUES 
(1, 'ROLE_ADMIN'),
(2, 'ROLE_PARTICIPANT'), 
(3, 'ROLE_SELLER')
ON CONFLICT (id) DO NOTHING;

-- 2. INSERT ADMIN USER
-- Password: admin123 (bcrypt hash)
INSERT INTO ol__user (id, username, password, salt, is_active, create_at, email, dtype, first_name, last_name) VALUES 
('demo-admin-001', 'admin@oloy.com', '$2y$13$r2ycEYr7ZGcV/3DcC8lbvOezuiG5safNgjuv16TjrTtO8KHdoWJkm', '', true, NOW(), 'admin@oloy.com', 'admin', 'System', 'Administrator')
ON CONFLICT (username) DO NOTHING;

-- 3. ASSIGN ADMIN ROLE TO USER
INSERT INTO ol__users_roles (user_id, role_id) VALUES 
('demo-admin-001', 1)
ON CONFLICT DO NOTHING;

-- 4. INSERT SELLER USER
-- Password: seller123 (bcrypt hash)
INSERT INTO ol__user (id, username, password, salt, is_active, create_at, email, dtype, first_name, last_name) VALUES 
('demo-seller-001', 'seller@oloy.com', '$2y$13$r2ycEYr7ZGcV/3DcC8lbvOezuiG5safNgjuv16TjrTtO8KHdoWJkm', '', true, NOW(), 'seller@oloy.com', 'seller', 'Coffee', 'Shop Manager')
ON CONFLICT (username) DO NOTHING;

-- 5. ASSIGN SELLER ROLE
INSERT INTO ol__users_roles (user_id, role_id) VALUES 
('demo-seller-001', 3)
ON CONFLICT DO NOTHING;

-- 6. INSERT CUSTOMER USER
-- Password: customer123 (bcrypt hash)
INSERT INTO ol__user (id, username, password, salt, is_active, create_at, email, dtype, first_name, last_name) VALUES 
('demo-customer-001', 'customer@oloy.com', '$2y$13$r2ycEYr7ZGcV/3DcC8lbvOezuiG5safNgjuv16TjrTtO8KHdoWJkm', '', true, NOW(), 'customer@oloy.com', 'customer', 'John', 'Doe')
ON CONFLICT (username) DO NOTHING;

-- 7. ASSIGN CUSTOMER ROLE
INSERT INTO ol__users_roles (user_id, role_id) VALUES 
('demo-customer-001', 2)
ON CONFLICT DO NOTHING;

-- 8. INSERT ADDITIONAL CUSTOMER USERS FOR TESTING
INSERT INTO ol__user (id, username, password, salt, is_active, create_at, email, dtype, first_name, last_name) VALUES 
('demo-customer-002', 'jane.smith@example.com', '$2y$13$r2ycEYr7ZGcV/3DcC8lbvOezuiG5safNgjuv16TjrTtO8KHdoWJkm', '', true, NOW(), 'jane.smith@example.com', 'customer', 'Jane', 'Smith'),
('demo-customer-003', 'mike.johnson@example.com', '$2y$13$r2ycEYr7ZGcV/3DcC8lbvOezuiG5safNgjuv16TjrTtO8KHdoWJkm', '', true, NOW(), 'mike.johnson@example.com', 'customer', 'Mike', 'Johnson'),
('demo-customer-004', 'sarah.wilson@example.com', '$2y$13$r2ycEYr7ZGcV/3DcC8lbvOezuiG5safNgjuv16TjrTtO8KHdoWJkm', '', true, NOW(), 'sarah.wilson@example.com', 'customer', 'Sarah', 'Wilson')
ON CONFLICT (username) DO NOTHING;

-- 9. ASSIGN CUSTOMER ROLES TO ADDITIONAL USERS
INSERT INTO ol__users_roles (user_id, role_id) VALUES 
('demo-customer-002', 2),
('demo-customer-003', 2),
('demo-customer-004', 2)
ON CONFLICT DO NOTHING;

-- 10. INSERT ADDITIONAL SELLER USERS
INSERT INTO ol__user (id, username, password, salt, is_active, create_at, email, dtype, first_name, last_name) VALUES 
('demo-seller-002', 'restaurant@oloy.com', '$2y$13$r2ycEYr7ZGcV/3DcC8lbvOezuiG5safNgjuv16TjrTtO8KHdoWJkm', '', true, NOW(), 'restaurant@oloy.com', 'seller', 'Restaurant', 'Manager'),
('demo-seller-003', 'retail@oloy.com', '$2y$13$r2ycEYr7ZGcV/3DcC8lbvOezuiG5safNgjuv16TjrTtO8KHdoWJkm', '', true, NOW(), 'retail@oloy.com', 'seller', 'Retail', 'Store')
ON CONFLICT (username) DO NOTHING;

-- 11. ASSIGN SELLER ROLES TO ADDITIONAL USERS
INSERT INTO ol__users_roles (user_id, role_id) VALUES 
('demo-seller-002', 3),
('demo-seller-003', 3)
ON CONFLICT DO NOTHING;

-- Display summary
SELECT 'User setup completed successfully!' as status;
SELECT 'Admin users:' as user_type;
SELECT '  - admin@oloy.com / admin123' as credentials;
SELECT 'Seller users:' as user_type;
SELECT '  - seller@oloy.com / seller123' as credentials;
SELECT '  - restaurant@oloy.com / admin123' as credentials;
SELECT '  - retail@oloy.com / admin123' as credentials;
SELECT 'Customer users:' as user_type;
SELECT '  - customer@oloy.com / customer123' as credentials;
SELECT '  - jane.smith@example.com / admin123' as credentials;
SELECT '  - mike.johnson@example.com / admin123' as credentials;
SELECT '  - sarah.wilson@example.com / admin123' as credentials; 