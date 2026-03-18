<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Auth;

Auth::logout();
redirect(url('admin/login.php'));
