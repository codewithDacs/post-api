<?php
header("Content-Type: application/json");

// Include the posts logic
require 'posts.php';

// Get the HTTP method, path, and input
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));

// Handle the request
switch ($method) {
    case 'GET':
        if (isset($request[0]) && $request[0] !== '') {
            $id = $request[0];
            getPost($id);
        } else {
            getPosts();
        }
        break;
    case 'POST':
        addPost();
        break;
    case 'PUT':
        if (isset($request[0])) {
            $id = $request[0];
            updatePost($id);
        }
        break;
    case 'DELETE':
        if (isset($request[0])) {
            $id = $request[0];
            deletePost($id);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        break;
}
?>