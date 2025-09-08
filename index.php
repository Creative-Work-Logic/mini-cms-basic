<?php
    include_once 'scripts/clsMain.php';
    $main=new clsMain();
    $main->set_web_root(__DIR__ . '\\'); // Set the web root directory
    $main->execute_main(); // Initialize the main class
    $main->output(); // Call output method to render the page
