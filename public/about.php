<?php
require "./partials/header.php";
$pageTitle = "About";
?>
<head>
    <link href="./partials/styles.css" rel="stylesheet">
</head>

<section class="hero">
    <div class="overlay">
        <h1>About ALF Association</h1>
        <p>Promoting freedom, independence, and well-being for all felines since 1986.</p>
        <div class="down-arrow"">
            <svg class="size-10 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
            </svg>
        </div>
    </div>
</section>

<section class="content">
    <div class="text-container">
        <h2>Our Mission</h2>
        <p>
            Founded by Albert Louis Féral in 1986, the ALF Association is dedicated to the
            liberation, emancipation, and well-being of cats. Our mission is to ensure that
            every feline enjoys independence and dignity in society.
        </p>

        <h2>What We Do</h2>
        <ul>
            <li>🐾 Raising awareness about feline rights and independence.</li>
            <li>🐾 Advocating for ethical treatment and freedom for all cats.</li>
            <li>🐾 Creating a community for cat lovers and supporters.</li>
        </ul>

        <h2>Our Values</h2>
        <p>✨ Freedom | ✨ Respect | ✨ Compassion</p>

        <button onclick="location.href = '/us.php';" class="cta-button">Discover our team</button>
    </div>
</section>


</body>
</html>

<?php
require './partials/footer.php';
?>
