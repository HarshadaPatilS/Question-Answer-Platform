<?php
require_once '../includes/functions.php';
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $answer_id = (int)$_POST['answer_id'];
    $user_id = $_SESSION['user_id'];

    $database = new Database();
    $db = $database->getConnection();

    // Check if already liked
    $checkQuery = "SELECT id FROM answer_likes WHERE user_id = :user_id AND answer_id = :answer_id";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':user_id', $user_id);
    $checkStmt->bindParam(':answer_id', $answer_id);
    $checkStmt->execute();

    if ($checkStmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'Already liked']);
        exit();
    }

    try {
        $db->beginTransaction();

        // Insert like
        $likeQuery = "INSERT INTO answer_likes (user_id, answer_id) VALUES (:user_id, :answer_id)";
        $likeStmt = $db->prepare($likeQuery);
        $likeStmt->bindParam(':user_id', $user_id);
        $likeStmt->bindParam(':answer_id', $answer_id);
        $likeStmt->execute();

        // Update like count
        $updateQuery = "UPDATE answers SET likes = likes + 1 WHERE id = :answer_id";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->bindParam(':answer_id', $answer_id);
        $updateStmt->execute();

        // Get new like count
        $countQuery = "SELECT likes FROM answers WHERE id = :answer_id";
        $countStmt = $db->prepare($countQuery);
        $countStmt->bindParam(':answer_id', $answer_id);
        $countStmt->execute();
        $result = $countStmt->fetch(PDO::FETCH_ASSOC);

        $db->commit();

        echo json_encode([
            'success' => true, 
            'likes' => $result['likes'],
            'message' => 'Answer liked successfully'
        ]);
    } catch (Exception $e) {
        $db->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}