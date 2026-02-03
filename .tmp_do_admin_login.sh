#!/bin/bash
COOKIE=/tmp/admin_cookie_test2.txt
rm -f "$COOKIE"
# fetch login page
curl -s -c "$COOKIE" http://localhost:8080/backend/auth/login.php -o /tmp/login_page2.html
CSRF=$(grep -oP 'name="csrf" value="\\K[^"]+' /tmp/login_page2.html || true)
echo "CSRF=$CSRF"
# post login
curl -s -D /tmp/login2_headers.txt -o /tmp/login2_body.txt -b "$COOKIE" -c "$COOKIE" -X POST http://localhost:8080/backend/auth/process_login.php --data-urlencode "username=admin" --data-urlencode "password=61560d08fdb078e4194d2ab0" --data-urlencode "csrf=$CSRF"
echo "--- headers ---"
sed -n '1,120p' /tmp/login2_headers.txt
echo "--- body ---"
sed -n '1,200p' /tmp/login2_body.txt
# fetch dashboard
curl -s -D /tmp/dash2_headers.txt -o /tmp/dash2_body.txt -b "$COOKIE" http://localhost:8080/backend/admin/dashboard.php
echo "--- dashboard headers ---"
sed -n '1,120p' /tmp/dash2_headers.txt
echo "--- dashboard snippet ---"
sed -n '1,200p' /tmp/dash2_body.txt
