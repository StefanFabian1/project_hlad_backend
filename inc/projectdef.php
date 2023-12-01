<?php

define("PROJECT_ROOT_PATH", __DIR__ . "\\..\\");

define("DATE_TIME_MASK", "Y-m-d H:i:s");

date_default_timezone_set("Europe/Bratislava");

require_once PROJECT_ROOT_PATH . "inc\\config.php";

require_once PROJECT_ROOT_PATH . "Controller\\Api\\BaseController.php";

require_once PROJECT_ROOT_PATH . "Model\\Database\\Database.php";

require_once PROJECT_ROOT_PATH . "utils\\SessionManager.php";

require_once PROJECT_ROOT_PATH . "utils\\StringUtils.php";

?>