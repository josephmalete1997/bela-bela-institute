<?php
/**
 * Test file to verify .htaccess rewrite is working
 * Access: /test_rewrite (without .php extension)
 */
echo "✅ Rewrite is working! You can access PHP files without .php extension.";
echo "<br><br>";
echo "Current URL: " . $_SERVER['REQUEST_URI'];
echo "<br>";
echo "Script Name: " . $_SERVER['SCRIPT_NAME'];
echo "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'];
echo "<br>";
echo "Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A');
