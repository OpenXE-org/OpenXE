<?php
/**
 * Authenticated Documentation Viewer
 * Uses the same Digest authentication as the REST API
 */

// Load API bootstrap to get authentication
require_once __DIR__ . '/bootstrap.php';

// Get the requested file from query parameter
$file = $_GET['file'] ?? 'swagger.html';

// Whitelist of allowed files
$allowedFiles = [
    'swagger.html',
    'docs.html',
    'openapi.json',
    'docs.raml',
    'docs.generated.raml',
];

// Security: Only allow whitelisted files
if (!in_array($file, $allowedFiles)) {
    http_response_code(404);
    die('File not found');
}

$filePath = __DIR__ . '/' . $file;

if (!file_exists($filePath)) {
    http_response_code(404);
    die('File not found');
}

// Determine content type
$contentTypes = [
    'html' => 'text/html',
    'json' => 'application/json',
    'raml' => 'application/raml+yaml',
];

$extension = pathinfo($file, PATHINFO_EXTENSION);
$contentType = $contentTypes[$extension] ?? 'text/plain';

// Set appropriate headers
header('Content-Type: ' . $contentType . '; charset=utf-8');
header('X-Content-Type-Options: nosniff');

// Output the file
readfile($filePath);
