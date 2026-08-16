<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

admin_logout();
redirect('login.php');
