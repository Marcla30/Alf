<?php
session_start();
$pageTitle = 'Profile'; // Set the page title
include 'partials/header.php'; // Your header with Tailwind and navigation
require_once __DIR__ . '/../core/function.php';
// Check if user is logged in
if (!isset($_SESSION['member_id'])) {
    header('Location: login.php');
    exit;
}

// Check if user is not an admin (already redirected if admin in login.php)
if ($_SESSION['is_admin']) {
    header('Location: admin.php');
    exit;
}

// Fetch member details
$specialties = db()->query('SELECT kspecialty, name FROM specialties ORDER BY name')->get();
$member = db()->query('SELECT * FROM members WHERE kmember = ?', [$_SESSION['member_id']])->find();

if (!$member) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Handle profile edit submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_profile') {
    $result = editMember($_POST);
    if (isset($result['success'])) {
        $success = $result['success'];
        // Refresh member data after successful edit
        $member = db()->query('SELECT * FROM members WHERE kmember = ?', [$_SESSION['member_id']])->find();
    } elseif (isset($result['error'])) {
        $error = $result['error'];
    }

}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

?>

    <div class="min-h-screen flex flex-col justify-center items-center bg-gray-900">
        <div class="w-full max-w-md">
            <h1 class="text-3xl font-bold text-center mb-6 text-gray-900 dark:text-gray-100">Profile</h1>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                <!-- Profile Display -->
                <p class="text-gray-900 dark:text-gray-100"><strong>Name:</strong> <?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></p>
                <p class="text-gray-700 dark:text-gray-300"><strong>Email:</strong> <?php echo htmlspecialchars($member['email']); ?></p>
                <p class="text-gray-700 dark:text-gray-300"><strong>Specialty:</strong> <?php echo htmlspecialchars($member['fkspecialty'] ? db()->query('SELECT name FROM specialties WHERE kspecialty = ?', [$member['fkspecialty']])->findColumn() : 'No Specialty'); ?></p>
                <p class="text-gray-700 dark:text-gray-300"><strong>Bio:</strong> <?php echo htmlspecialchars($member['bio'] ?? 'No bio'); ?></p>
                <p class="text-gray-700 dark:text-gray-300"><strong>Active:</strong> <?php echo $member['is_active'] ? 'Yes' : 'No'; ?></p>
                <p class="text-gray-700 dark:text-gray-300"><strong>Admin:</strong> <?php echo $member['is_admin'] ? 'Yes' : 'No'; ?></p>

                <!-- Edit Profile Button -->
                <div class="mt-4">
                    <a href="#edit-profile" class="bg-blue-700 text-white p-2 rounded hover:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400" onclick="document.getElementById('edit-profile-form').classList.remove('hidden'); return false;">Edit Profile</a>
                </div>

                <!-- Edit Profile Form (Initially Hidden) -->
                <form id="edit-profile-form" action="profile.php" method="POST" class="hidden mt-4 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md space-y-4">
                    <input type="hidden" name="action" value="edit_profile">
                    <input type="hidden" name="kmember" value="<?php echo htmlspecialchars($member['kmember']); ?>">
                    <div>
                        <label for="edit_first_name" class="block text-gray-700 dark:text-gray-300">First Name</label>
                        <input type="text" id="edit_first_name" name="first_name" value="<?php echo htmlspecialchars($member['first_name']); ?>" required class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 dark:bg-gray-700 dark:text-gray-100">
                    </div>
                    <div>
                        <label for="edit_last_name" class="block text-gray-700 dark:text-gray-300">Last Name</label>
                        <input type="text" id="edit_last_name" name="last_name" value="<?php echo htmlspecialchars($member['last_name']); ?>" required class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 dark:bg-gray-700 dark:text-gray-100">
                    </div>
                    <div>
                        <label for="edit_fkspecialty" class="block text-gray-700 dark:text-gray-300">Specialty</label>
                        <select id="edit_fkspecialty" name="fkspecialty" class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 dark:bg-gray-700 dark:text-gray-100">
                            <option value="">No Specialty</option>
                            <?php foreach ($specialties as $specialty): ?>
                                <option value="<?php echo htmlspecialchars($specialty['kspecialty']); ?>" <?php echo $member['fkspecialty'] == $specialty['kspecialty'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($specialty['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="edit_bio" class="block text-gray-700 dark:text-gray-300">Bio</label>
                        <textarea id="edit_bio" name="bio" class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 dark:bg-gray-700 dark:text-gray-100"><?php echo htmlspecialchars($member['bio'] ?? ''); ?></textarea>
                    </div>
                    <div>
                        <label for="edit_email" class="block text-gray-700 dark:text-gray-300">Email</label>
                        <input type="email" id="edit_email" name="email" value="<?php echo htmlspecialchars($member['email']); ?>" required class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 dark:bg-gray-700 dark:text-gray-100">
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="edit_is_active" name="is_active" <?php echo $member['is_active'] ? 'checked' : ''; ?> class="mr-2">
                        <label for="edit_is_active" class="text-gray-700 dark:text-gray-300">Active</label>
                    </div>
                    <button type="submit" class="w-full bg-blue-700 text-white p-2 rounded hover:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">Save Changes</button>
                    <button type="button" class="w-full bg-gray-500 text-white p-2 rounded hover:bg-gray-600 dark:bg-gray-700 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:focus:ring-gray-400 mt-2" onclick="document.getElementById('edit-profile-form').classList.add('hidden');">Cancel</button>
                </form>
            </div>
            <div class="mt-4">
                <a href="login.php?logout" class="bg-red-500 text-white p-2 rounded hover:bg-red-600 dark:bg-red-400 dark:hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400" onclick="return confirm('Are you sure you want to logout?')">Logout</a>
            </div>
        </div>
    </div>

<?php
include 'partials/footer.php'; // Your footer
?>