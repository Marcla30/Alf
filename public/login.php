<?php
session_start();
$pageTitle = "Login";
include 'partials/header.php';
require_once __DIR__ . '/../core/function.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($email) && !empty($password)) {
        $member = db()->query('SELECT kmember, email, password_hash, is_admin FROM members WHERE email = ? AND password_hash = ?', [$email, $password])->find();

        if ($member) {
            $_SESSION['member_id'] = $member['kmember'];
            $_SESSION['is_admin'] = $member['is_admin'];

            if ($member['is_admin']) {
                header('Location: admin.php');
            } else {
                header('Location: profile.php');
            }
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    } else {
        $error = 'Please fill in all fields.';
    }
}
?>

    <div class="min-h-screen flex flex-col justify-center items-center bg-gray-900">
        <h1 class="text-3xl font-bold text-center mb-6 text-gray-900 dark:text-gray-100">Member zone</h1>

        <?php if (isset($error)): ?>
            <p class="mt-4 text-red-600 dark:text-red-400 text-center"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form action="login.php" method="POST" class="max-w-md mx-auto bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <div class="mb-4">
                <label for="email" class="block text-gray-700 dark:text-gray-300 mb-2">Email</label>
                <input type="email" id="email" name="email" required class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 dark:bg-gray-700 dark:text-gray-100">
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700 dark:text-gray-300 mb-2">Password</label>
                <input type="password" id="password" name="password" required class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 dark:bg-gray-700 dark:text-gray-100">
            </div>
            <button type="submit" class="w-full bg-blue-700 text-white p-2 rounded hover:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">Login</button>
        </form>
    </div>

<?php
include 'partials/footer.php';
?>