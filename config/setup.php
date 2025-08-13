<?php
require_once __DIR__ . '/database.php';

// Pass true to auto setup the DB
new Database(autoSetup: true);

echo "🎉 Setup complete! You can now run your project.\n";
