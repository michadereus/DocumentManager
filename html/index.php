<?php
    echo '<!DOCTYPE html>';
    echo '<html>';
    echo '<head>';
    echo '<title>Welcome to Document Manager</title>';
    echo '<style>';
    echo '    body {';
    echo '        width: 35em;';
    echo '        margin: 0 auto;';
    echo '        font-family: Tahoma, Verdana, Arial, sans-serif;';
    echo '    }';
    echo '</style>';
    echo '</head>';
    echo '<body>';
    echo '<h2>Document Manager</h2>';
    echo '<p></p>';
    echo '<p>Document upload, viewer, and general purpose file handler made by Michael DeReus for CS4743 at UTSA - wmi593 - michael.dereus@my.utsa.edu</p>';
    echo '<p>> <a href="/search.php">Search for document</a></p>';
    echo '<p>> <a href="/upload.php">Upload Document(s)</a></p>';
    echo '<p>> <a href="/view.php">View Documents</a></p>';
    echo '<p>> <a href="/reporting.php">View Reports</a></p>';
    echo 'Developer tools:';
    echo '<p>> <a href="/dbadmin">phpMyAdmin</a></p>';
    echo '</body>';
    echo '</html>';
?>