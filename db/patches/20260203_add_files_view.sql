-- Patch: add files.view permission and grant it to admin
-- Run with your MySQL client, e.g.:
--   mysql -u root -p lhsa < db/patches/20260203_add_files_view.sql

START TRANSACTION;

-- create permission if missing
INSERT IGNORE INTO permissions (name)
VALUES ('files.view');

-- grant to admin role if not already granted
INSERT INTO role_permissions (role, permission_id)
SELECT 'admin', p.id
FROM permissions p
WHERE p.name = 'files.view'
  AND NOT EXISTS (
    SELECT 1 FROM role_permissions rp WHERE rp.role = 'admin' AND rp.permission_id = p.id
  );

COMMIT;

-- Note: Adjust the database name/user as needed when running this script.
