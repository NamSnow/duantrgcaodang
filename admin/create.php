<?php
session_start();

// Kiểm tra xem người dùng có phải là admin không
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../includes/db.php';  // Đảm bảo có kết nối với DB

    // Kiểm tra và xử lý hình ảnh
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadDir = '../assets/image';
        $imageFileName = uniqid() . '-' . basename($_FILES['image']['name']);  // Tạo tên file duy nhất
        $imagePath = $uploadDir . $imageFileName;

        // Di chuyển file ảnh vào thư mục uploads
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            echo "Failed to upload image.";
            exit();
        }
    }

    // Lấy dữ liệu từ form và kiểm tra sự tồn tại
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $summary = isset($_POST['summary']) ? $_POST['summary'] : '';
    $content = isset($_POST['content']) ? $_POST['content'] : '';

    if (empty($title) || empty($summary) || empty($content)) {
        echo "All fields are required.";
        exit();
    }

    // Thêm dữ liệu vào cơ sở dữ liệu
    try {
        $stmt = $pdo->prepare("INSERT INTO news (title, summary, content, image) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $summary, $content, $imagePath]);

        // Chuyển hướng về trang quản lý tin tức
        header("Location: /cutonama3/admin/index.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Add News</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <div class="min-h-screen bg-gray-100">
        <div class="max-w-7xl mx-auto p-4">
            <h1 class="text-3xl font-semibold text-gray-800">Add New News</h1>

            <form action="create.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" name="title" id="title" required class="mt-1 p-2 w-full border border-gray-300 rounded">
                </div>

                <div>
                    <label for="summary" class="block text-sm font-medium text-gray-700">Summary</label>
                    <textarea name="summary" id="summary" required class="mt-1 p-2 w-full border border-gray-300 rounded"></textarea>
                </div>

                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                    <textarea name="content" id="content" required class="mt-1 p-2 w-full border border-gray-300 rounded"></textarea>
                </div>

                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700">Image</label>
                    <input type="file" name="image" id="image" class="mt-1 p-2 w-full border border-gray-300 rounded">
                </div>

                <div>
                    <button type="submit" class="bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Add News</button>
                </div>
            </form>

        </div>
    </div>
</body>
</html>
