<?php
// Set your admin credentials here
$ADMIN_USERNAME = "admin";
$ADMIN_PASSWORD = "admin123"; // use a hashed password in real projects

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
