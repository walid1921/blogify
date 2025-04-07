<?php

require_once "session.php";
require_once "utils/helpers.php";

// Destroying a Session
session_unset();
session_destroy();

redirect("login.php");