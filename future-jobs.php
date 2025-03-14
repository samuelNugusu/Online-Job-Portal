<?php
// No session_start() since it's handled by index.php
require_once 'includes/db.php'; // Connect to the database

// Dummy data for future jobs
$futureJobs = [
    [
        'title' => 'AI Ethics Consultant',
        'description' => 'Advise companies on ethical AI implementation.',
        'image' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=300&q=80',
        'category' => 'Technology'
    ],
    [
        'title' => 'Climate Change Analyst',
        'description' => 'Analyze environmental impact for sustainable growth.',
        'image' => 'https://images.pexels.com/photos/1108572/pexels-photo-1108572.jpeg?auto=compress&cs=tinysrgb&w=300',
        'category' => 'Environmental'
    ],
    [
        'title' => 'Virtual Reality Designer',
        'description' => 'Create immersive experiences for virtual environments.',
        'image' => 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=300&q=80',
        'category' => 'Creative'
    ]
];

// Initialize variables
$success = '';
$error = '';
$submittedJobTitle = null; // Use null to indicate no submission yet

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['future_job_interest'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $submittedJobTitle = isset($_POST['job_title']) ? trim($_POST['job_title']) : null;

    if (!$submittedJobTitle) {
        $error = "Job title is missing.";
    } elseif (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // In a real scenario, save to database or send email notification
        $success = "Thank you! We'll notify you about " . htmlspecialchars($submittedJobTitle) . " opportunities.";
    } else {
        $error = "Invalid email address.";
    }
}
?>

<!-- Future Jobs Section -->
<section class="future-jobs-section">
    <div class="container">
        <div class="future-jobs-wrapper">
            <h2 class="section-title">Future Jobs</h2>
            <p class="section-subtitle">Explore emerging career opportunities shaping the next decade.</p>
            <div class="future-jobs-grid">
                <?php foreach ($futureJobs as $job): ?>
                    <div class="future-job-card">
                        <div class="future-job-image-wrapper">
                            <img src="<?php echo htmlspecialchars($job['image']); ?>" alt="<?php echo htmlspecialchars($job['title']); ?>" class="future-job-image">
                        </div>
                        <div class="future-job-content">
                            <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                            <p class="job-description"><?php echo htmlspecialchars($job['description']); ?></p>
                            <p class="job-category"><strong>Category:</strong> <?php echo htmlspecialchars($job['category']); ?></p>
                            <form method="post" action="" class="interest-form">
                                <input type="hidden" name="job_title" value="<?php echo htmlspecialchars($job['title']); ?>">
                                <div class="form-group">
                                    <input type="email" name="email" placeholder="Enter your email for updates" required class="input-field">
                                    <button type="submit" name="future_job_interest" class="btn notify-btn">Get Notified</button>
                                </div>
                            </form>
                            <?php if ($submittedJobTitle === $job['title']): ?>
                                <?php if ($success): ?>
                                    <p class="success-message"><?php echo htmlspecialchars($success); ?></p>
                                <?php elseif ($error): ?>
                                    <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>