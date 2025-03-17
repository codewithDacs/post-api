<?php
header("Content-Type: application/json");

// Include the posts logic
require 'posts.php';

// Get the request URI and method
$requestUri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Remove query string from the URI
$requestUri = strtok($requestUri, '?');

// Extract the path segments
$pathSegments = explode('/', trim($requestUri, '/'));

// Handle the request based on the path and method
if ($pathSegments[0] === 'posts') {
    $id = isset($pathSegments[1]) ? $pathSegments[1] : null;

    switch ($method) {
        case 'GET':
            if ($id !== null) {
                getPost($id);
            } else {
                getPosts();
            }
            break;
        case 'POST':
            addPost();
            break;
        case 'PUT':
            if ($id !== null) {
                updatePost($id);
            } else {
                http_response_code(400);
                echo json_encode(["message" => "Post ID is required for update"]);
            }
            break;
        case 'DELETE':
            if ($id !== null) {
                deletePost($id);
            } else {
                http_response_code(400);
                echo json_encode(["message" => "Post ID is required for deletion"]);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(["message" => "Method not allowed"]);
            break;
    }
} else {
    http_response_code(404);
    echo json_encode(["message" => "Route not found"]);
}
?>