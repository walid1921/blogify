<?php

require_once "includes/session.php";
require_once "includes/helpers.php";

// Destroying a Session
session_unset(); // to remove all session variables
session_destroy(); // to destroy the session, it deletes the session ID on the server and not the session ID cookie in the browser. buy you don't see yhe effect until you refresh the page

redirect("login.php");