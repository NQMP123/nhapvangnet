<?php
session_start();
// Hủy toàn bộ session
session_unset();
session_destroy();
header('Location: login.php');
exit;