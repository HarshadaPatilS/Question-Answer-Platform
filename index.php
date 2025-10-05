<?php
require_once 'includes/functions.php';
require_once 'config/database.php';

$page_title = "Home - Q&A Platform";

$database = new Database();
$db = $database->getConnection();

// Get filter
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Build query
$query = "SELECT q.*, u.username, COUNT(DISTINCT a.id) as answer_count 
          FROM questions q 
          JOIN users u ON q.user_id = u.id 
          LEFT JOIN answers a ON q.id = a.question_id ";

if ($category) {
    $query .= "WHERE q.category = :category ";
}

$query .= "GROUP BY q.id ORDER BY q.created_at DESC";

$stmt = $db->prepare($query);
if ($category) {
    $stmt->bindParam(':category', $category);
}
$stmt->execute();
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories
$catQuery = "SELECT DISTINCT category FROM questions WHERE category IS NOT NULL AND category != ''";
$catStmt = $db->prepare($catQuery);
$catStmt->execute();
$categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);

include 'includes/header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Recent Questions</h2>
                <?php if (isLoggedIn()): ?>
                    <a href="questions/ask_question.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Ask Question
                    </a>
                <?php endif; ?>
            </div>

            <?php if (count($questions) > 0): ?>
                <?php foreach ($questions as $question): ?>
                    <div class="card mb-3 question-card">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="vote-section text-center me-3">
                                    <div class="vote-count"><?php echo $question['likes']; ?></div>
                                    <small class="text-muted">likes</small>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="card-title">
                                        <a href="questions/view_question.php?id=<?php echo $question['id']; ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($question['title']); ?>
                                        </a>
                                    </h5>
                                    <p class="card-text text-muted">
                                        <?php echo substr(htmlspecialchars($question['description']), 0, 150); ?>...
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <?php if ($question['category']): ?>
                                                <span class="badge bg-secondary"><?php echo htmlspecialchars($question['category']); ?></span>
                                            <?php endif; ?>
                                            <span class="badge bg-info"><?php echo $question['answer_count']; ?> answers</span>
                                        </div>
                                        <small class="text-muted">
                                            Asked by <strong><?php echo htmlspecialchars($question['username']); ?></strong>
                                            <?php echo timeAgo($question['created_at']); ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No questions found. Be the first to ask!
                </div>
            <?php endif; ?>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-funnel"></i> Filter by Category
                </div>
                <div class="list-group list-group-flush">
                    <a href="index.php" class="list-group-item list-group-item-action <?php echo !$category ? 'active' : ''; ?>">
                        All Questions
                    </a>
                    <?php foreach ($categories as $cat): ?>
                        <a href="?category=<?php echo urlencode($cat); ?>" 
                           class="list-group-item list-group-item-action <?php echo $category == $cat ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($cat); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if (!isLoggedIn()): ?>
                <div class="card mt-3">
                    <div class="card-body text-center">
                        <h5>Join Our Community!</h5>
                        <p class="text-muted">Sign up to ask questions and help others.</p>
                        <a href="auth/signup.php" class="btn btn-primary btn-sm">Sign Up Now</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>