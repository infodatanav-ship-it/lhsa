#!/bin/bash
COOKIE=/tmp/admin_cookie_test.txt
rm -f "$COOKIE"
curl -s -c "$COOKIE" http://localhost:8080/backend/auth/login.php -o /tmp/login_page.html
CSRF=$(grep -oP 'name="csrf" value="\\K[^"]+' /tmp/login_page.html || true)
echo "CSRF=$CSRF"
curl -s -D /tmp/login_headers.txt -o /tmp/login_resp.json -b "$COOKIE" -c "$COOKIE" -X POST http://localhost:8080/backend/auth/process_login.php --data-urlencode "username=admin" --data-urlencode "password=61560d08fdb078e4194d2ab0" --data-urlencode "csrf=$CSRF"
echo "--- login headers ---"
sed -n '1,120p' /tmp/login_headers.txt
echo "--- login json ---"
sed -n '1,200p' /tmp/login_resp.json
echo
necho "GET dashboard..."
curl -s -D /tmp/dash_headers.txt -o /tmp/dash.html -b "$COOKIE" http://localhost:8080/backend/admin/dashboard.php
echo "--- dashboard headers ---"
sed -n '1,120p' /tmp/dash_headers.txt
echo "--- dashboard snippet ---"
sed -n '1,200p' /tmp/dash.html
