<?php
session_start();
session_unset();
session_destroy();
header("Location: /qa-platform/index.php");
exit();
?>