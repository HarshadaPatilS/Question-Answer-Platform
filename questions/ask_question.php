<?php
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header("Location: /qa-platform/auth/login.php");
    exit();
}

$page_title = "Ask a Question";
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitizeInput($_POST['title']);
    $description = sanitizeInput($_POST['description']);
    $category = sanitizeInput($_POST['category']);

    if (empty($title) || empty($description)) {
        $error = "Title and description are required.";
    } else {
        $database = new Database();
        $db = $database->getConnection();

        $query = "INSERT INTO questions (user_id, title, description, category) VALUES (:user_id, :title, :description, :category)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':category', $category);

        if ($stmt->execute()) {
            $question_id = $db->lastInsertId();
            header("Location: view_question.php?id=" . $question_id);
            exit();
        } else {
            $error = "Failed to post question. Please try again.";
        }
    }
}

include '../includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-question-circle"></i> Ask a Question</h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Question Title</label>
                            <input type="text" name="title" class="form-control" 
                                   placeholder="What's your question?" required>
                            <small class="text-muted">Be specific and clear</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="6" 
                                      placeholder="Provide more details about your question..." required></textarea>
                            <small class="text-muted">Include all relevant information</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-control">
                                <option value="">Select a category</option>
                                <option value="Technology">Technology</option>
                                <option value="Science">Science</option>
                                <option value="Programming">Programming</option>
                                <option value="Design">Design</option>
                                <option value="Business">Business</option>
                                <option value="General">General</option>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send"></i> Post Question
                            </button>
                            <a href="/qa-platform/index.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>