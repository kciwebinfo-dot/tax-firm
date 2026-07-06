INSERT IGNORE INTO permissions (name, slug, module) VALUES
('View Profile', 'profile.view', 'profile'),
('Update Profile', 'profile.update', 'profile'),
('Change Own Password', 'profile.password', 'profile');

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.slug IN ('profile.view', 'profile.update', 'profile.password')
WHERE r.slug = 'admin';
