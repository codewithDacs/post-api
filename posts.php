<?php
header("Content-Type: application/json");

// Database connection
$host = 'localhost';
$db = 'blog';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(["message" => "Database connection failed: " . $conn->connect_error]));
}

// Get all posts
function getPosts() {
    global $conn;
    $result = $conn->query("SELECT * FROM posts");
    $posts = [];
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
    echo json_encode($posts);
}

// Get a single post by ID
function getPost($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Post not found"]);
    }
}

// Add a new post
function addPost() {
    global $conn;
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['title']) && isset($input['body'])) {
        $stmt = $conn->prepare("INSERT INTO posts (title, body) VALUES (?, ?)");
        $stmt->bind_param("ss", $input['title'], $input['body']);
        if ($stmt->execute()) {
            $newPostId = $stmt->insert_id;
            http_response_code(201);
            echo json_encode(["id" => $newPostId, "title" => $input['title'], "body" => $input['body']]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Failed to create post"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Invalid input"]);
    }
}

// Update a post by ID
function updatePost($id) {
    global $conn;
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['title']) || isset($input['body'])) {
        $query = "UPDATE posts SET ";
        $params = [];
        if (isset($input['title'])) {
            $query .= "title = ?, ";
            $params[] = $input['title'];
        }
        if (isset($input['body'])) {
            $query .= "body = ?, ";
            $params[] = $input['body'];
        }
        $query = rtrim($query, ", ") . " WHERE id = ?";
        $params[] = $id;

        $stmt = $conn->prepare($query);
        $types = str_repeat('s', count($params) - 1) . 'i'; // All strings except the last (id)
        $stmt->bind_param($types, ...$params);
        if ($stmt->execute()) {
            echo json_encode(["message" => "Post updated"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Failed to update post"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "No data provided"]);
    }
}

// Delete a post by ID
function deletePost($id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(["message" => "Post deleted"]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Failed to delete post"]);
    }
}
?>