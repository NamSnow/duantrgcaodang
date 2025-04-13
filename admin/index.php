<?php
session_start();

// Kiểm tra xem người dùng có phải là admin không
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /cutonama3/auth/login.php"); // Điều chỉnh đường dẫn nếu cần
    exit();
}

require_once '../includes/db.php'; // Đảm bảo đường dẫn đúng

// Log access to the admin page
$ip_address = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$page_url = $_SERVER['REQUEST_URI'];

$stmt_log = $pdo->prepare("INSERT INTO access_logs (ip_address, user_agent, page_url) VALUES (?, ?, ?)");
$stmt_log->execute([$ip_address, $user_agent, $page_url]);

// Kiểm tra nếu có từ khóa tìm kiếm
$searchQuery = '';
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
}

// Lấy danh sách tin tức với tìm kiếm (nếu có)
$stmt = $pdo->prepare("SELECT * FROM news WHERE title LIKE ? OR summary LIKE ? ORDER BY created_at DESC");
$stmt->execute(['%' . $searchQuery . '%', '%' . $searchQuery . '%']);
$newsList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - News Management</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <div class="min-h-screen bg-gray-100">
        <div class="max-w-7xl mx-auto p-4">
            <h1 class="text-3xl font-semibold text-gray-800">Admin Panel - News Management</h1>

            <!-- Nút đăng xuất -->
            <a href="/cutonama3/admin/logout.php" class="bg-red-500 text-white p-2 rounded mt-4 inline-block">Logout</a>

            <!-- Form tìm kiếm -->
            <form action="" method="GET" class="mb-4 flex items-center mt-4">
                <input type="text" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>" placeholder="Search news..." class="p-2 border border-gray-300 rounded" />
                <button type="submit" class="bg-blue-500 text-white p-2 rounded ml-2">Search</button>
            </form>

            <a href="/cutonama3/admin/create.php" class="bg-blue-500 text-white p-2 rounded mt-4 inline-block">Add New News</a>

            <!-- Bảng tin tức -->
            <table class="min-w-full bg-white mt-6 border border-gray-300">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b text-left">Title</th>
                        <th class="py-2 px-4 border-b text-left">Summary</th>
                        <th class="py-2 px-4 border-b text-left">Published</th>
                        <th class="py-2 px-4 border-b text-left">Created At</th>
                        <th class="py-2 px-4 border-b text-left">Image</th> <!-- Cột ảnh -->
                        <th class="py-2 px-4 border-b text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($newsList as $news): ?>
                        <tr>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($news['title']); ?></td>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($news['summary']); ?></td>
                            <td class="py-2 px-4 border-b">
                                <?php echo $news['is_published'] ? 'Published' : 'Unpublished'; ?>
                            </td>
                            <td class="py-2 px-4 border-b"><?php echo $news['created_at']; ?></td>
                            <td class="py-2 px-4 border-b">
                                <?php if ($news['image']): ?>
                                    <img src="<?php echo htmlspecialchars($news['image']); ?>" alt="Image" class="w-20 h-20 object-cover">
                                <?php else: ?>
                                    No image
                                <?php endif; ?>
                            </td>
                            <td class="py-2 px-4 border-b">
                                <a href="/cutonama3/admin/edit.php?id=<?php echo $news['news_id']; ?>" class="text-blue-500">Edit</a> |
                                <a href="/cutonama3/admin/delete.php?id=<?php echo $news['news_id']; ?>" class="text-red-500">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </div>
</body>
</html>
