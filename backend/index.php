<?php
require_once __DIR__ . '/bootstrap.php';

if (isLoggedIn()) {
    redirect(isAdmin() ? 'admin/dashboard.php' : 'user/dashboard.php');
}
redirect('auth/login.php');