<?php
require_once '../config.php';
require_once '../includes/functions.php';
session_destroy();
redirect('/backend/auth/login.php');