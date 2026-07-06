<?php
require_once __DIR__ . '/functions.php';

logout_user();
redirect(app_url('login.php'));
