<?php
// Remove session_start() since it's already called in index.php
require_once 'includes/db.php'; // Connect to the database

// Fetch distinct categories from the jobs table
try {
    $stmt = $pdo->query("SELECT DISTINCT category FROM jobs WHERE category IS NOT NULL");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categories = [];
    $error = "Error loading categories: " . $e->getMessage();
}
?>

<!-- Job Categories Section -->
<section class="categories-section">
    <div class="container">
        <h2>Browse by Category</h2>
        <?php if (!empty($categories)): ?>
            <div class="categories-grid">
                <?php foreach ($categories as $category): ?>
                    <div class="category-card">
                        <a href="search.php?category=<?php echo urlencode($category['category']); ?>">
                            <h3><?php echo htmlspecialchars($category['category']); ?></h3>
                            <p>Explore jobs in <?php echo htmlspecialchars($category['category']); ?></p>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No categories available at the moment.</p>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <p><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
    </div>
</section>
