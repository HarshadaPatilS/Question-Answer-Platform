<?php
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header("Location: /qa-platform/auth/login.php");
    exit();
}

$page_title = "My Questions";

$database = new Database();
$db = $database->getConnection();

$query = "SELECT q.*, COUNT(a.id) as answer_count 
          FROM questions q 
          LEFT JOIN answers a ON q.id = a.question_id 
          WHERE q.user_id = :user_id 
          GROUP BY q.id 
          ORDER BY q.created_at DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Questions</h2>
        <a href="/qa-platform/questions/ask_question.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Ask New Question
        </a>
    </div>

    <?php if (count($questions) > 0): ?>
        <?php foreach ($questions as $question): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">
                        <a href="/qa-platform/questions/view_question.php?id=<?php echo $question['id']; ?>">
                            <?php echo htmlspecialchars($question['title']); ?>
                        </a>
                    </h5>
                    <p class="card-text text-muted">
                        <?php echo substr(htmlspecialchars($question['description']), 0, 200); ?>...
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <?php if ($question['category']): ?>
                                <span class="badge bg-secondary"><?php echo htmlspecialchars($question['category']); ?></span>
                            <?php endif; ?>
                            <span class="badge bg-info"><?php echo $question['answer_count']; ?> answers</span>
                            <span class="badge bg-success"><?php echo $question['likes']; ?> likes</span>
                            <span class="badge bg-warning text-dark"><?php echo $question['views']; ?> views</span>
                        </div>
                        <small class="text-muted">
                            Posted <?php echo timeAgo($question['created_at']); ?>
                        </small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> You haven't asked any questions yet.
            <a href="/qa-platform/questions/ask_question.php">Ask your first question!</a>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>