<?php
session_start();
require_once __DIR__ . '/../config/database.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserById($user_id) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT id, username, email, created_at FROM users WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function timeAgo($timestamp) {
    $time = strtotime($timestamp);
    $diff = time() - $time;
    
    if ($diff < 60) return "just now";
    if ($diff < 3600) return floor($diff / 60) . " minutes ago";
    if ($diff < 86400) return floor($diff / 3600) . " hours ago";
    if ($diff < 604800) return floor($diff / 86400) . " days ago";
    
    return date("M d, Y", $time);
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function hasUserLikedQuestion($user_id, $question_id) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT id FROM question_likes WHERE user_id = :user_id AND question_id = :question_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':question_id', $question_id);
    $stmt->execute();
    
    return $stmt->rowCount() > 0;
}

function hasUserLikedAnswer($user_id, $answer_id) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT id FROM answer_likes WHERE user_id = :user_id AND answer_id = :answer_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':answer_id', $answer_id);
    $stmt->execute();
    
    return $stmt->rowCount() > 0;
}

function getRelatedQuestions($question_id, $category, $limit = 5) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT q.*, u.username, COUNT(a.id) as answer_count 
              FROM questions q 
              JOIN users u ON q.user_id = u.id 
              LEFT JOIN answers a ON q.id = a.question_id 
              WHERE q.category = :category AND q.id != :question_id 
              GROUP BY q.id 
              ORDER BY q.created_at DESC 
              LIMIT :limit";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':question_id', $question_id);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>