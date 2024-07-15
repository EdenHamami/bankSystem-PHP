<?php
session_start();
session_unset();
session_destroy();
echo "Session destroyed";
header("Location: ../views/login.php");
exit();

