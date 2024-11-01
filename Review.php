<?php 
// إعدادات الاتصال بـ Supabase
$supabase_url = "https://nlszbtvnyniqdokpubbq.supabase.co"; 
$supabase_key = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im5sc3pidHZueW5pcWRva3B1YmJxIiwicm9sZSI6ImFub24iLCJpYXQiOjE3MzAyMjM5NDYsImV4cCI6MjA0NTc5OTk0Nn0.nXmb3WE-cEZqTrqGANth0yI363S2_s_T812roEKTc4I";

// التحقق من الاتصال
if (empty($supabase_url) || empty($supabase_key)) {
    die("الرجاء ضبط إعدادات Supabase");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // جلب بيانات النموذج
    $name = trim($_POST['name']);
    $text = trim($_POST['text']);
    $rating = intval($_POST['rating']);

    // التحقق من صحة البيانات
    if (empty($name) || empty($text) || $rating < 1 || $rating > 5) {
        die("يرجى ملء جميع الحقول بشكل صحيح.");
    }

    $data = [
        "name" => $name,
        "text" => $text,
        "rating" => $rating
    ];

    // إعداد الخيارات للـ POST
    $options = [
        "http" => [
            "header"  => "Content-type: application/json\r\nAuthorization: Bearer $supabase_key\r\nPrefer: return=representation",
            "method"  => "POST",
            "content" => json_encode($data),
            "ignore_errors" => true
        ],
        "ssl" => [
            "verify_peer" => false,
            "verify_peer_name" => false
        ]
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents("$supabase_url/rest/v1/reviews", false, $context);
    
    if ($result === FALSE) {
        echo "حدث خطأ أثناء إضافة المراجعة.<br>";
        $error = error_get_last();
        echo "تفاصيل الخطأ: " . htmlspecialchars($error['message']);
    } else {
        $response = json_decode($result, true);
        if (isset($response['error'])) {
            echo "خطأ من قاعدة البيانات: " . htmlspecialchars($response['error']['message']);
        } else {
            header("Location: Review.php");
            exit();
        }
    }
}

// جلب الآراء من قاعدة البيانات
$options = [
    "http" => [
        "header" => "Authorization: Bearer $supabase_key",
        "ignore_errors" => true
    ],
    "ssl" => [
        "verify_peer" => false,
        "verify_peer_name" => false
    ]
];
$context = stream_context_create($options);
$response = file_get_contents("$supabase_url/rest/v1/reviews?select=*", false, $context);

if ($response === FALSE) {
    echo "حدث خطأ أثناء جلب البيانات من Supabase.";
    $error = error_get_last();
    echo "تفاصيل الخطأ: " . htmlspecialchars($error['message']);
    $reviews = [];
} else {
    $reviews = json_decode($response, true);
    if (!is_array($reviews)) {
        echo "حدث خطأ أثناء جلب البيانات. الاستجابة غير صحيحة.";
        $reviews = [];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>صفحة الآراء و التعليقات</title>
    <link rel="website icon" type="png" href="img/4660619.png">
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
                    <li class="nav-item">
                        <a class="nav-link" href="index.html">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="About_us.html">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="Review.php">Review</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Display Reviews -->
    <div class="container reviews-container">
        <h2>Reviews</h2>
        <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $review): ?>
                <?php if (isset($review['name'], $review['text'], $review['rating'])): ?>
                    <div class="card review-card mb-3">
                        <div class="card-header">
                            <?php echo htmlspecialchars($review['name']); ?> 
                            <span class="rating"><?php echo str_repeat('★', $review['rating']); ?></span>
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
