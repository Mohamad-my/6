<?php
// إعدادات الاتصال بـ Supabase
$supabase_url = "https://nlszbtvnyniqdokpubbq.supabase.co"; 
$supabase_key = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im5sc3pidHZueW5pcWRva3B1YmJxIiwicm9sZSI6ImFub24iLCJpYXQiOjE3MzAyMjM5NDYsImV4cCI6MjA0NTc5OTk0Nn0.nXmb3WE-cEZqTrqGANth0yI363S2_s_T812roEKTc4I";

// إضافة مراجعة جديدة
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $text = trim($_POST['text']);
    $rating = (int)$_POST['rating'];

    if (empty($name) || empty($text) || $rating < 1 || $rating > 5) {
        echo "يرجى ملء جميع الحقول بشكل صحيح.";
        exit;
    }

    $data = [
        "name" => $name,
        "text" => $text,
        "rating" => $rating
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "$supabase_url/rest/v1/reviews");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $supabase_key",
        "apikey: $supabase_key",
        "Prefer: return=representation"
    ]);

    $result = curl_exec($ch);
    if ($result === false) {
        echo "حدث خطأ أثناء إضافة المراجعة: " . curl_error($ch);
    } else {
        echo "تم إضافة المراجعة بنجاح!";
    }
    curl_close($ch);
}

// جلب المراجعات من قاعدة البيانات
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$supabase_url/rest/v1/reviews");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $supabase_key",
    "apikey: $supabase_key",
    "Prefer: return=representation"
]);

$result = curl_exec($ch);
$reviews = json_decode($result, true) ?: [];
curl_close($ch);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>صفحة الآراء والتعليقات</title>
    <link rel="icon" href="img/4660619.png">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: rgb(43, 42, 42);">
        <div class="container">
            <a class="navbar-brand" href="#" style="display: flex; align-items:center;">
                <span class="navbar-title" style="font-family:monospace">Elderlymed Devices</span>
                <img src="img/p1.jpg" alt="Logo" style="width: 80px; height: auto; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); margin-left: 10px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto"> 
                    <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="About_us.html">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="Review.php">Review</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Display Reviews -->
    <div class="container reviews-container my-5">
        <h2>Reviews</h2>
        <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $review): ?>
                <?php if (isset($review['name'], $review['text'], $review['rating'])): ?>
                    <div class="card review-card mb-3">
                        <div class="card-header">
                            <?php echo htmlspecialchars($review['name']); ?> 
                            <span class="rating"><?php echo str_repeat('★', (int)$review['rating']); ?></span>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><?php echo htmlspecialchars($review['text']); ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <p>المراجعة غير متوفرة بشكل كامل.</p>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p>لا توجد مراجعات بعد.</p>
        <?php endif; ?>
    </div><br>

    <!-- Review Form -->
    <div class="container review-form-container">
        <form id="reviewForm" action="Review.php" method="POST">
            <div class="form-group">
                <label for="reviewerName">Your Name</label>
                <input type="text" name="name" id="reviewerName" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="reviewText">Your Review</label>
                <textarea name="text" id="reviewText" class="form-control" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="rating">Rating</label>
                <div class="star-rating">
                    <input type="radio" id="star5" name="rating" value="5" required><label for="star5">&#9733;</label>
                    <input type="radio" id="star4" name="rating" value="4"><label for="star4">&#9733;</label>
                    <input type="radio" id="star3" name="rating" value="3"><label for="star3">&#9733;</label>
                    <input type="radio" id="star2" name="rating" value="2"><label for="star2">&#9733;</label>
                    <input type="radio" id="star1" name="rating" value="1"><label for="star1">&#9733;</label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</body>
</html>
