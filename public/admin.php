<?php
session_start();
include 'partials/header.php'; // Your header with Tailwind and navigation
require_once __DIR__ . '/../core/function.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['member_id'])) {
    header('Location: login.php');
    exit;
}
if (!$_SESSION['is_admin']) {
    header('Location: profile.php');
    exit;
}


$success = '';
$error = '';
$kmember = $_GET['kmember'] ?? null;
$kspecialty = $_GET['kspecialty'] ?? null;

// Fetch specialties and members for the list and edit form
$specialties = db()->query('SELECT kspecialty, name, description FROM specialties ORDER BY name')->get();
$members = db()->query('SELECT * FROM members ORDER BY last_name, first_name')->get();

// Handle actions via external functions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $result = null;

        if ($action === 'edit_member') {
            $result = editMember($_POST);
        } elseif ($action === 'delete_member') {
            $result = deleteMember($_POST);
        } elseif ($action === 'toggle_active') {
            $result = toggleActiveMember($_POST);
        } elseif ($action === 'create_member') {
            $result = createMember($_POST);
        } elseif ($action === 'create_specialty') {
            $result = createSpecialty($_POST);
        } elseif ($action === 'edit_specialty') {
            $result = editSpecialty($_POST);
        } elseif ($action === 'delete_specialty') {
            $result = deleteSpecialty($_POST);
        }

        if ($result) {
            if (isset($result['success'])) {
                $success = $result['success'];
            } elseif (isset($result['error'])) {
                $error = $result['error'];
            }
        }

        // Redirect to show messages
        header('Location: admin.php?' . ($success ? 'success=' . urlencode($success) : 'error=' . urlencode($error)));
        exit;
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Fetch success/error from URL parameters
if (isset($_GET['success'])) {
    $success = urldecode($_GET['success']);
} elseif (isset($_GET['error'])) {
    $error = urldecode($_GET['error']);
}

$selectedMember = null;
if ($kmember && is_numeric($kmember)) {
    $selectedMember = db()->query('SELECT * FROM members WHERE kmember = ?', [(int)$kmember])->find();
}
$selectedSpecialty = null;
if ($kspecialty && is_numeric($kspecialty)) {
    $selectedSpecialty = db()->query('SELECT * FROM specialties WHERE kspecialty = ?', [(int)$kspecialty])->find();
}
?>

<div class="min-h-screen flex flex-col justify-center items-center bg-gray-900">
    <div class="w-full max-w-4xl flex justify-between items-center mb-6 px-4">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">ALF Admin Dashboard</h1>
        <div class="flex space-x-4">
            <a href="admin.php?create" class="bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">Create New Member</a>
            <a href="admin.php?specialties" class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400">Manage Specialties</a>
            <a href="admin.php?logout" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 dark:bg-red-400 dark:hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400" onclick="return confirm('Are you sure you want to logout?')">Logout</a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <?php if ($success): ?>
        <p class="mt-4 text-green-600 dark:text-green-400 text-center"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
        <p class="mt-4 text-red-600 dark:text-red-400 text-center"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <!-- Member List, Create Form, Edit Form, or Specialties -->
    <?php if (isset($_GET['edit']) && $kmember && $selectedMember): ?>
        <!-- Edit Member Form (Hide Create and Specialties) -->
        <div class="w-full max-w-md">
            <h2 class="text-2xl font-semibold mb-4 text-gray-900 dark:text-gray-100">Edit Member</h2>
            <form action="admin.php" method="POST" class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md space-y-4">
                <input type="hidden" name="action" value="edit_member">
                <input type="hidden" name="kmember" value="<?php echo htmlspecialchars($selectedMember['kmember']); ?>">
                <div>
                    <label for="edit_first_name" class="block text-gray-700 dark:text-gray-300">First Name</label>
                    <input type="text" id="edit_first_name" name="first_name" value="<?php echo htmlspecialchars($selectedMember['first_name']); ?>" required class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 dark:bg-gray-700 dark:text-gray-100">
                </div>
                <div>
                    <label for="edit_last_name" class="block text-gray-700 dark:text-gray-300">Last Name</label>
                    <input type="text" id="edit_last_name" name="last_name" value="<?php echo htmlspecialchars($selectedMember['last_name']); ?>" required class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 dark:bg-gray-700 dark:text-gray-100">
                </div>
                <div>
                    <label for="edit_fkspecialty" class="block text-gray-700 dark:text-gray-300">Specialty</label>
                    <select id="edit_fkspecialty" name="fkspecialty" class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 dark:bg-gray-700 dark:text-gray-100">
                        <option value="">No Specialty</option>
                        <?php foreach ($specialties as $specialty): ?>
                            <option value="<?php echo htmlspecialchars($specialty['kspecialty']); ?>" <?php echo $selectedMember['fkspecialty'] == $specialty['kspecialty'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($specialty['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="edit_bio" class="block text-gray-700 dark:text-gray-300">Bio</label>
                    <textarea id="edit_bio" name="bio" class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 dark:bg-gray-700 dark:text-gray-100"><?php echo htmlspecialchars($selectedMember['bio'] ?? ''); ?></textarea>
                </div>
                <div>
                    <label for="edit_email" class="block text-gray-700 dark:text-gray-300">Email</label>
                    <input type="email" id="edit_email" name="email" value="<?php echo htmlspecialchars($selectedMember['email']); ?>" required class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 dark:bg-gray-700 dark:text-gray-100">
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="edit_is_active" name="is_active" <?php echo $selectedMember['is_active'] ? 'checked' : ''; ?> class="mr-2">
                    <label for="edit_is_active" class="text-gray-700 dark:text-gray-300">Active</label>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="edit_is_admin" name="is_admin" <?php echo $selectedMember['is_admin'] ? 'checked' : ''; ?> class="mr-2" disabled>
                    <label for="edit_is_admin" class="text-gray-700 dark:text-gray-300">Admin (Read-only)</label>
                </div>
                <button type="submit" class="w-full bg-blue-700 text-white p-2 rounded hover:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">Update Member</button>
                <div class="mt-4">
                    <a href="admin.php" class="bg-gray-500 text-white p-2 rounded hover:bg-gray-600 dark:bg-gray-700 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:focus:ring-gray-400">Back to Member List</a>
                </div>
        </div>
    <?php elseif (isset($_GET['create'])): ?>
        <!-- Create Member Form (Hide if editing or managing specialties) -->
        <div class="w-full max-w-md">
            <h2 class="text-2xl font-semibold mb-4 text-gray-900 dark:text-gray-100">Create New Member</h2>
            <form action="admin.php" method="POST" class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md space-y-4">
                <input type="hidden" name="action" value="create_member">
                <div>
                    <label for="first_name" class="block text-gray-700 dark:text-gray-300">First Name</label>
                    <input type="text" id="first_name" name="first_name" required class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 dark:bg-gray-700 dark:text-gray-100">
                </div>
                <div>
                    <label for="last_name" class="block text-gray-700 dark:text-gray-300">Last Name</label>
                    <input type="text" id="last_name" name="last_name" required class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 dark:bg-gray-700 dark:text-gray-100">
                </div>
                <div>
                    <label for="fkspecialty" class="block text-gray-700 dark:text-gray-300">Specialty</label>
                    <select id="fkspecialty" name="fkspecialty" class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 dark:bg-gray-700 dark:text-gray-100">
                        <option value="">No Specialty</option>
                        <?php foreach ($specialties as $specialty): ?>
                            <option value="<?php echo htmlspecialchars($specialty['kspecialty']); ?>"><?php echo htmlspecialchars($specialty['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="bio" class="block text-gray-700 dark:text-gray-300">Bio</label>
                    <textarea id="bio" name="bio" class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 dark:bg-gray-700 dark:text-gray-100"></textarea>
                </div>
                <div>
                    <label for="email" class="block text-gray-700 dark:text-gray-300">Email</label>
                    <input type="email" id="email" name="email" required class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 dark:bg-gray-700 dark:text-gray-100">
                </div>
                <div>
                    <label for="password" class="block text-gray-700 dark:text-gray-300">Password</label>
                    <input type="password" id="password" name="password" required class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 dark:bg-gray-700 dark:text-gray-100">
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="is_active" name="is_active" class="mr-2">
                    <label for="is_active" class="text-gray-700 dark:text-gray-300">Active</label>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="is_admin" name="is_admin" class="mr-2">
                    <label for="is_admin" class="text-gray-700 dark:text-gray-300">Admin</label>
                </div>
                <button type="submit" class="w-full bg-blue-700 text-white p-2 rounded hover:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">Create Member</button>
                <div class="mt-4">
                    <a href="admin.php" class="bg-gray-500 text-white p-2 rounded hover:bg-gray-600 dark:bg-gray-700 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:focus:ring-gray-400">Back to Member List</a>
                </div>
            </form>
        </div>
    <?php elseif (isset($_GET['specialties'])): ?>
        <!-- Specialties Management Section (Hide if editing or creating members) -->
        <div class="w-full max-w-4xl">
            <h2 class="text-2xl font-semibold mb-4 text-gray-900 dark:text-gray-100">Specialty Management</h2>

            <!-- Create Specialty Form -->
            <?php if (!$kspecialty): ?> <!-- Hide if editing a specialty -->
                <div class="mb-6 bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">Add New Specialty</h3>
                    <form action="admin.php" method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="create_specialty">
                        <div>
                            <label for="specialty_name" class="block text-gray-700 dark:text-gray-300">Name</label>
                            <input type="text" id="specialty_name" name="name" required class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 dark:bg-gray-700 dark:text-gray-100">
                        </div>
                        <div>
                            <label for="specialty_description" class="block text-gray-700 dark:text-gray-300">Description</label>
                            <textarea id="specialty_description" name="description" class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 dark:bg-gray-700 dark:text-gray-100"></textarea>
                        </div>
                        <button type="submit" class="w-full bg-green-700 text-white p-2 rounded hover:bg-green-800 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400">Create Specialty</button>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Specialties List -->
            <?php if (!$kspecialty): ?>  <!-- Hide if editing a specialty -->
                <div class="grid gap-4">
                    <?php foreach ($specialties as $specialty): ?>
                        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md flex justify-between items-center">
                            <div>
                                <p class="text-gray-900 dark:text-gray-100"><strong><?php echo htmlspecialchars($specialty['name']); ?></strong></p>
                                <p class="text-gray-700 dark:text-gray-300"><?php echo htmlspecialchars($specialty['description']); ?></p>
                            </div>
                            <div class="flex space-x-2">
                                <a href="admin.php?specialties&kspecialty=<?php echo htmlspecialchars($specialty['kspecialty']); ?>" class="bg-yellow-500 text-white p-2 rounded hover:bg-yellow-600 dark:bg-yellow-400 dark:hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 dark:focus:ring-yellow-400">Edit</a>
                                <form action="admin.php" method="POST" class="inline">
                                    <input type="hidden" name="action" value="delete_specialty">
                                    <input type="hidden" name="kspecialty" value="<?php echo htmlspecialchars($specialty['kspecialty']); ?>">
                                    <button type="submit" class="bg-red-500 text-white p-2 rounded hover:bg-red-600 dark:bg-red-400 dark:hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400" onclick="return confirm('Are you sure you want to delete this specialty?')">Delete</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div class="mt-4">
                <a href="admin.php" class="bg-gray-500 text-white p-2 rounded hover:bg-gray-600 dark:bg-gray-700 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:focus:ring-gray-400">Back to Member List</a>
            </div>
        </div>

        <!-- Edit Specialty Form (Shown when ?specialties&kspecialty=X is present) -->
        <?php if (isset($_GET['specialties']) && $kspecialty && $selectedSpecialty): ?>
            <div class="w-full max-w-md mt-4">
                <h2 class="text-2xl font-semibold mb-4 text-gray-900 dark:text-gray-100">Edit Specialty</h2>
                <form action="admin.php" method="POST" class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md space-y-4">
                    <input type="hidden" name="action" value="edit_specialty">
                    <input type="hidden" name="kspecialty" value="<?php echo htmlspecialchars($selectedSpecialty['kspecialty']); ?>">
                    <div>
                        <label for="edit_specialty_name" class="block text-gray-700 dark:text-gray-300">Name</label>
                        <input type="text" id="edit_specialty_name" name="name" value="<?php echo htmlspecialchars($selectedSpecialty['name']); ?>" required class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 dark:bg-gray-700 dark:text-gray-100">
                    </div>
                    <div>
                        <label for="edit_specialty_description" class="block text-gray-700 dark:text-gray-300">Description (Bio)</label>
                        <textarea id="edit_specialty_description" name="description" class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 dark:bg-gray-700 dark:text-gray-100"><?php echo htmlspecialchars($selectedSpecialty['description'] ?? ''); ?></textarea>
                    </div>
                    <button type="submit" class="w-full bg-green-700 text-white p-2 rounded hover:bg-green-800 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400">Update Specialty</button>
                    <div class="mt-4">
                        <a href="admin.php?specialties" class="bg-gray-500 text-white p-2 rounded hover:bg-gray-600 dark:bg-gray-700 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:focus:ring-gray-400">Back to Specialties</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <!-- Member List (Hide Create, Edit, and Specialties Forms) -->
        <div class="w-full max-w-4xl">
            <h2 class="text-2xl font-semibold mb-4 text-gray-900 dark:text-gray-100">Member List</h2>
            <div class="grid gap-4">
                <?php foreach ($members as $member): ?>
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md flex justify-between items-center">
                        <div>
                            <p class="text-gray-900 dark:text-gray-100"><strong><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></strong></p>
                            <p class="text-gray-700 dark:text-gray-300">Email: <?php echo htmlspecialchars($member['email']); ?></p>
                            <p class="text-gray-700 dark:text-gray-300">Specialty: <?php echo htmlspecialchars($member['fkspecialty'] ? db()->query('SELECT name FROM specialties WHERE kspecialty = ?', [$member['fkspecialty']])->findColumn() : 'No Specialty'); ?></p>
                            <p class="text-gray-700 dark:text-gray-300">Bio: <?php echo htmlspecialchars($member['bio'] ?? 'No bio'); ?></p>
                            <p class="text-gray-700 dark:text-gray-300">Active: <?php echo $member['is_active'] ? 'Yes' : 'No'; ?>, Admin: <?php echo $member['is_admin'] ? 'Yes' : 'No'; ?></p>
                        </div>
                        <div class="flex space-x-2">
                            <a href="admin.php?edit&kmember=<?php echo htmlspecialchars($member['kmember']); ?>" class="bg-yellow-500 text-white p-2 rounded hover:bg-yellow-600 dark:bg-yellow-400 dark:hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 dark:focus:ring-yellow-400">Edit</a>
                            <form action="admin.php" method="POST" class="inline">
                                <input type="hidden" name="action" value="delete_member">
                                <input type="hidden" name="kmember" value="<?php echo htmlspecialchars($member['kmember']); ?>">
                                <button type="submit" class="bg-red-500 text-white p-2 rounded hover:bg-red-600 dark:bg-red-400 dark:hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400" onclick="return confirm('Are you sure you want to delete this member?')">Delete</button>
                            </form>
                            <form action="admin.php" method="POST" class="inline">
                                <input type="hidden" name="action" value="toggle_active">
                                <input type="hidden" name="kmember" value="<?php echo htmlspecialchars($member['kmember']); ?>">
                                <button type="submit" class="bg-purple-500 text-white p-2 rounded hover:bg-purple-600 dark:bg-purple-400 dark:hover:bg-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400">
                                    <?php echo $member['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
include 'partials/footer.php'; // Your footer
?>
