<?php
require_once '../templates/header.php';
echo getHeader('Kontakta oss', 'contact');
?>

<div class="container">
    <aside class="left-sidebar">
        <div class="card">
            <h3>Meny</h3>
            <ul>
                <li><a href="../blogg.php">Alla inlägg</a></li>
                <li><a href="../register.php">Bli medlem</a></li>
                <li><a href="kontakt.php">Kontakta oss</a></li>
            </ul>
        </div>
    </aside>

    <main class="main-content">
        <div class="card">
            <h1>Kontakta oss</h1>
            <p>Har du frågor eller funderingar? Vi finns här för att hjälpa dig!</p>

            <form action="../process_contact.php" method="POST" class="contact-form">
                <div class="form-group">
                    <label for="name">Namn:</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="email">E-post:</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="subject">Ämne:</label>
                    <input type="text" id="subject" name="subject" required>
                </div>

                <div class="form-group">
                    <label for="message">Meddelande:</label>
                    <textarea id="message" name="message" rows="5" required></textarea>
                </div>

                <button type="submit" class="btn">Skicka meddelande</button>
            </form>
        </div>
    </main>

    <aside class="right-sidebar">
        <div class="card">
            <h3>Kontaktinformation</h3>
            <p><strong>E-post:</strong><br>info@bloggplattformen.se</p>
            <p><strong>Telefon:</strong><br>08-123 45 67</p>
            <p><strong>Adress:</strong><br>
            Bloggvägen 123<br>
            123 45 Stockholm</p>
            <p><strong>Öppettider:</strong><br>
            Mån-Fre: 09:00-17:00<br>
            Lör-Sön: Stängt</p>
        </div>
    </aside>
</div>

<?php
require_once '../templates/footer.php';
echo getFooter();
?> 