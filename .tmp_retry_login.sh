#!/bin/bash
set -euo pipefail
COOKIE=/tmp/admin_login_retry_cookie.txt
rm -f "$COOKIE" /tmp/login_page_retry.html /tmp/login_headers_retry.txt /tmp/login_body_retry.txt /tmp/dash_headers_retry.txt /tmp/dash_body_retry.txt || true

# Fetch login page and save cookie
curl -s -c "$COOKIE" http://localhost:8080/backend/auth/login.php -o /tmp/login_page_retry.html

# Extract CSRF token using Perl (handles newlines)
CSRF=$(perl -0777 -ne 'if(/name="csrf"\s*value="([^"]+)"/s){print $1}' /tmp/login_page_retry.html || true)
echo "CSRF=$CSRF"

# POST login
curl -s -D /tmp/login_headers_retry.txt -o /tmp/login_body_retry.txt -b "$COOKIE" -c "$COOKIE" \
  -X POST http://localhost:8080/backend/auth/process_login.php \
  --data-urlencode "username=admin" \
  --data-urlencode "password=61560d08fdb078e4194d2ab0" \
  --data-urlencode "csrf=$CSRF"

echo "--- login headers ---"
sed -n '1,160p' /tmp/login_headers_retry.txt || true
echo "--- login body ---"
sed -n '1,240p' /tmp/login_body_retry.txt || true

# Fetch dashboard with session cookie
curl -s -D /tmp/dash_headers_retry.txt -o /tmp/dash_body_retry.txt -b "$COOKIE" http://localhost:8080/backend/admin/dashboard.php || true

echo "--- dashboard headers ---"
sed -n '1,160p' /tmp/dash_headers_retry.txt || true
echo "--- dashboard body (snippet) ---"
sed -n '1,240p' /tmp/dash_body_retry.txt || true
