# Docker setup (development)

This project can be run locally using Docker + Docker Compose.

Quick start

1. Build and start containers:

   docker-compose up --build -d

2. Open the site in your browser at: http://localhost:8080

3. Import the database schema (once MySQL is ready):

   # Linux / macOS (bash): replace password if you changed it in `docker-compose.yml`
   docker exec -i $(docker-compose ps -q db) sh -c 'exec mysql -uroot -ppassword lhsa_web' < backend/db.sql

   # Windows PowerShell:
   $cid = docker-compose ps -q db; Get-Content backend/db.sql | docker exec -i $cid sh -c "exec mysql -uroot -ppassword lhsa_web"

Notes
- By default the web app uses environment variables for DB connection (read from the container env). The `docker-compose.yml` sets:
  - `DB_HOST=db`
  - `DB_NAME=lhsa_web`
  - `DB_USER=root`
  - `DB_PASS=password`
- Change these values in `docker-compose.yml` or set them in your environment before starting the containers.
- Web is exposed on port 8080 by default, change mapping in `docker-compose.yml` if needed.

Stopping and removing:

   docker-compose down -v

Security
- Do not store production credentials in the repository. For production, use proper secrets management (Docker secrets, environment injection, or external secret manager).
