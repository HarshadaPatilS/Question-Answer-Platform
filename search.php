<?php
require_once 'includes/functions.php';
require_once 'config/database.php';

$page_title = "Search Results";
$search_query = isset($_GET['q']) ? sanitizeInput($_GET['q']) : '';

$database = new Database();
$db = $database->getConnection();

$questions = [];

if (!empty($search_query)) {
    $query = "SELECT q.*, u.username, COUNT(DISTINCT a.id) as answer_count 
              FROM questions q 
              JOIN users u ON q.user_id = u.id 
              LEFT JOIN answers a ON q.id = a.question_id 
              WHERE q.title LIKE :search OR q.description LIKE :search 
              GROUP BY q.id 
              ORDER BY q.created_at DESC";
    
    $stmt = $db->prepare($query);
    $search_param = "%{$search_query}%";
    $stmt->bindParam(':search', $search_param);
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

include 'includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center mb-4">
        <div class="col-md-8">
            <h2 class="mb-3">Search Questions</h2>
            <form method="GET" action="search.php" class="mb-4">
                <div class="input-group input-group-lg">
                    <input type="text" name="q" class="form-control" 
                           placeholder="Search for questions..." 
                           value="<?php echo htmlspecialchars($search_query); ?>" required>
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php if (!empty($search_query)): ?>
        <div class="row">
            <div class="col-12">
                <h4 class="mb-3">
                    <?php echo count($questions); ?> result<?php echo count($questions) != 1 ? 's' : ''; ?> 
                    for "<?php echo htmlspecialchars($search_query); ?>"
                </h4>

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
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> No questions found matching your search.
                        <br>Try different keywords or <a href="index.php">browse all questions</a>.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>