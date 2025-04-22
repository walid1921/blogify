<!DOCTYPE html>
<html>
<head>
    <title>User List</title>
</head>
<body>
<h1>All Users</h1>
<ul>
    <?php foreach ($users as $user): ?>
        <li><?= htmlspecialchars($user['username']) ?></li>
    <?php endforeach; ?>
</ul>
</body>
</html>
