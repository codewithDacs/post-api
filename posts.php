<?php
// Simulate a database with an array
$posts = [
    ['id' => 1, 'title' => 'First Post', 'body' => 'This is the first post.'],
    ['id' => 2, 'title' => 'Second Post', 'body' => 'This is the second post.']
];

// Get all posts
function getPosts() {
    global $posts;
    echo json_encode($posts);
}

// Get a single post by ID
function getPost($id) {
    global $posts;
    $post = array_filter($posts, function($post) use ($id) {
        return $post['id'] == $id;
    });
    if (!empty($post)) {
        echo json_encode(array_values($post)[0]);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Post not found"]);
    }
}

// Add a new post
function addPost() {
    global $posts;
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['title']) && isset($input['body'])) {
        $newPost = [
            'id' => end($posts)['id'] + 1,
            'title' => $input['title'],
            'body' => $input['body']
        ];
        array_push($posts, $newPost);
        http_response_code(201);
        echo json_encode($newPost);
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Invalid input"]);
    }
}

// Update a post by ID
function updatePost($id) {
    global $posts;
    $input = json_decode(file_get_contents('php://input'), true);
    foreach ($posts as &$post) {
        if ($post['id'] == $id) {
            if (isset($input['title'])) $post['title'] = $input['title'];
            if (isset($input['body'])) $post['body'] = $input['body'];
            echo json_encode($post);
            return;
        }
    }
    http_response_code(404);
    echo json_encode(["message" => "Post not found"]);
}

// Delete a post by ID
function deletePost($id) {
    global $posts;
    $initialLength = count($posts);
    $posts = array_filter($posts, function($post) use ($id) {
        return $post['id'] != $id;
    });
    if (count($posts) < $initialLength) {
        http_response_code(200);
        echo json_encode(["message" => "Post deleted"]);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Post not found"]);
    }
}
?>