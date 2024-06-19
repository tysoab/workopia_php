<?php
$config = require basePath('config/db.php');
$db = new Database($config);
$listings = $db->query("SELECT * FROM listings LIMIT 6")->fetchAll();

// passing the data to view
// goto helper and change the loadView function
loadView('listings/index', ['listings' => $listings]);
