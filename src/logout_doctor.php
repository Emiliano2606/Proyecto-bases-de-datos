<?php
session_start();
session_destroy();
header("Location:../es/login_doctor.html");
exit();