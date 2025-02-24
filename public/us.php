<?php
require "./partials/header.php";
require __DIR__ . '/../core/function.php';
?>
<head>
    <link href="./partials/styles.css" rel="stylesheet">
</head>

<section class="hero">
    <div class="overlay">
        <h1>Our Team</h1>
        <p>Meet the passionate individuals behind the ALF Association.</p>
    </div>
</section>

<section class="team-section">
    <div class="team-grid">
        <!-- Example Team Member (This will be echoed from a database in PHP) -->
        <div class="team-member">
            <img src="/img/Alf_round.png" alt="John Doe">
            <h3>John Doe</h3>
            <p class="specialty">Feline Nutrition Specialist</p>
            <p class="bio">John has been working with cats for over 10 years, focusing on their dietary needs and
                well-being.</p>
            <a href="/contact.php?member=John+Doe" class="contact-button">Contact</a>
        </div>

        <?php
        $sql = 'SELECT kmember, first_name, last_name, photo_url, bio, specialties.name AS specialty_name 
FROM `members` 
LEFT JOIN `specialties` ON members.fkspecialty = specialties.kspecialty 
WHERE is_active = true';

        $members = db()
        ->query($sql)
        ->get();
        foreach ($members as $member) {
        ?>
        <div class="team-member">
            <img src="<?php echo htmlspecialchars($member['photo_url'] ?? '/img/Alf_round.png'); ?>"
                 alt="<?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?>">
            <h3><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></h3>
            <p class="specialty"><?php echo htmlspecialchars($member['specialty_name'] ?? 'No Specialty'); ?></p>
            <p class="bio"><?php echo htmlspecialchars($member['bio'] ?? 'No bio available'); ?></p>
            <a href="/contact.php?member=<?php echo urlencode($member['kmember']); ?>" class="contact-button">Contact</a>
        </div>
        <?php
        }
        ?>
    </div>
</section>

<?php
require './partials/footer.php';
?>