<?php
require_once '../includes/functions.php';
require_once '../config/database.php';

if (!isLoggedIn()) {
    header("Location: /qa-platform/auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $question_id = (int)$_POST['question_id'];
    $answer_text = sanitizeInput($_POST['answer_text']);

    if (empty($answer_text)) {
        header("Location: ../questions/view_question.php?id=" . $question_id . "&error=empty");
        exit();
    }

    $database = new Database();
    $db = $database->getConnection();

    $query = "INSERT INTO answers (question_id, user_id, answer_text) VALUES (:question_id, :user_id, :answer_text)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':question_id', $question_id);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->bindParam(':answer_text', $answer_text);

    if ($stmt->execute()) {
        header("Location: ../questions/view_question.php?id=" . $question_id . "&success=answer_posted");
    } else {
        header("Location: ../questions/view_question.php?id=" . $question_id . "&error=failed");
    }
    exit();
}

header("Location: /qa-platform/index.php");
exit();
?>