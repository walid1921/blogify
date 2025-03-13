<?php 

include "./db/database.php";

$username = $password = "";
$usernameErr = $passwordErr = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $username = $_POST["username"];
    $password = $_POST["password"];
    

     if (empty( trim($username)) || empty(trim($password))) {
        $usernameErr = "Username and Password are required";
    } else {
        $username = mysqli_real_escape_string($conn, $username);
        $password = mysqli_real_escape_string($conn, $password);
        
        $sql = "SELECT * FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            if (password_verify($password, $row["password"])) {
                session_start();
                $_SESSION["username"] = $username;
                header("Location: admin.html");
            } else {
                $passwordErr = "Invalid password";
            }
        } else {
            $usernameErr = "Invalid username";
        }
    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login">
   
    
    <nav>
        <ul>
            <li>
                <a href="index.php">Home</a>
            </li>
    
            <!-- When the user is logged in -->
            <li>
                <a href="admin.html">Admin</a>
            </li>
            <li>
                <a href="logout.html">Logout</a>
            </li>
    
            <!-- When the user is not logged in -->
            <li>
                <a href="register.php">Register</a>
            </li>
            <li>
                <a href="login.php">Login</a>
            </li>
        </ul>
    </nav>
    

    <!-- Include Header and Navigation -->
    
    <div class="container">
        <div class="form-container">
            <form method="POST" action="">
                <h2>Login</h2>
    
                <!-- Error message placeholder -->
                <p style="color:red">
                    <!-- Error message goes here -->
                </p>
    
                <label for="username">Username:</label><br>
                <input type="text" name="username" required><br><br>
    
                <label for="password">Password:</label><br>
                <input type="password" name="password" required><br><br>
    
                <input type="submit" value="Login">
            </form>
        </div>
    </div>
    
    <!-- Include Footer -->

</body>
</html>