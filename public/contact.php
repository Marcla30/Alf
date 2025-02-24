<?php
include 'partials/header.php';
require_once __DIR__ . '/../core/function.php';
?>


    <div class="min-h-screen flex flex-col justify-center items-center bg-gray-900">
        <h1 class="text-3xl font-bold text-center mb-6 text-gray-100">Contact Us</h1>

        <form action="contact.php" method="POST" class="max-w-md w-full bg-gray-800 p-6 rounded-lg shadow-md">
            <div class="mb-4">
                <label for="subject" class="block text-gray-300 mb-2">Subject</label>
                <input type="text" id="subject" name="subject" required class="w-full p-2 border border-gray-600 rounded bg-gray-700 text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-400" value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>">
            </div>

            <div class="mb-4">
                <label for="body" class="block text-gray-300 mb-2">Message</label>
                <textarea id="body" name="body" required class="w-full p-2 border border-gray-600 rounded bg-gray-700 text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-400" rows="5"><?php echo htmlspecialchars($_POST['body'] ?? ''); ?></textarea>
            </div>

            <div class="mb-4">
                <label for="sender_email" class="block text-gray-300 mb-2">Your Email (Optional)</label>
                <input type="email" id="sender_email" name="sender_email" class="w-full p-2 border border-gray-600 rounded bg-gray-700 text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-400" value="<?php echo htmlspecialchars($_POST['sender_email'] ?? ''); ?>">
            </div>

            <div class="mb-4">
                <label for="fkmember" class="block text-gray-300 mb-2">Contact a Member</label>
                <select id="fkmember" name="fkmember" class="w-full p-2 border border-gray-600 rounded bg-gray-700 text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">Select a Member (Optional)</option>
                    <?php
                    $sql = 'SELECT kmember, first_name, last_name FROM members WHERE is_active = true ORDER BY last_name, first_name';
                    $members = db()->query($sql)->get();

                    $selectedMember = isset($_GET['member']) ? (int)$_GET['member'] : (isset($_POST['fkmember']) ? $_POST['fkmember'] : '');

                    foreach ($members as $member) {
                        $selected = ($selectedMember == $member['kmember']) ? 'selected' : '';
                        echo '<option value="' . htmlspecialchars($member['kmember']) . '" ' . $selected . '>' . htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) . '</option>';
                    }
                    ?>
                </select>
            </div>

            <button type="submit" class="w-full bg-blue-700 text-white p-2 rounded hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-400">Send Message</button>

            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $subject = trim($_POST['subject'] ?? '');
                $body = trim($_POST['body'] ?? '');
                $sender_email = trim($_POST['sender_email'] ?? '');
                $fkmember = $_POST['fkmember'] ?? null;

                if (!empty($subject) && !empty($body)) {
                    try {
                        $insert_sql = 'INSERT INTO contact (subject, body, sender_email, fkmember) VALUES (:subject, :body, :sender_email, :fkmember)';
                        db()->query($insert_sql, [
                            ':subject' => $subject,
                            ':body' => $body,
                            ':sender_email' => $sender_email ?: null,
                            ':fkmember' => $fkmember ? (int)$fkmember : null
                        ]);
                        echo '<p class="mt-4 text-green-400 text-center">Message sent successfully!</p>';
                    } catch (Exception $e) {
                        echo '<p class="mt-4 text-red-400 text-center">Error sending message. Please try again.</p>';
                    }
                } else {
                    echo '<p class="mt-4 text-red-400 text-center">Please fill in all required fields.</p>';
                }
            }
            ?>
        </form>
    </div>


<?php
include 'partials/footer.php';
?>