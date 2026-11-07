<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . '/db.php';