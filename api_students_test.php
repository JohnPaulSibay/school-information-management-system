<?php
$apiUrl = "http://localhost:3000/api/students";

$response = file_get_contents($apiUrl);

if ($response === FALSE) {
    die("Failed to connect to API.");
}

$data = json_decode($response, true);

echo "<h2>Students from S3 API</h2>";

echo "<pre>";
print_r($data);
echo "</pre>";
?>
