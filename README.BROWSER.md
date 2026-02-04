Project Browser — relocation note
================================

The interactive file browser page has been moved from the repository root to:

- `backend/browser.php`

If you previously linked to `browser.php` at the project root, update those links to point to `backend/browser.php`.

Permissions and enabling modify actions
- The browser uses session-based permissions. To allow delete/rename/upload actions, ensure the user session includes an admin role or the appropriate permissions. The app sets `$_SESSION['role']` and loads permissions into `$_SESSION['permissions']` at login.
- Recommended quick permission setup for the `browser` directory (run as root or via sudo):

```bash
sudo chown -R www-data:www-data browser
sudo find browser -type d -exec chmod 755 {} +
sudo find browser -type f -exec chmod 644 {} +
```

Testing
- Visit `/backend/browser.php` in your web browser (relative to your site root) and test uploading a small file.

Security notes
- Avoid leaving directories world-writable (0777). Prefer adjusting ownership to the webserver user.
- The browser includes CSRF protection and checks `userCanModify()` before performing destructive actions; integrate `userCanModify()` with your auth system if needed (see `backend/browser_permissions.php`).

If you want, I can add a redirect page or update any project navigation files that should point to the new location.
