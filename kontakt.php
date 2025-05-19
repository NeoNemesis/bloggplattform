<?php
require_once 'templates/header.php';
?>

<style>
    .contact-info {
        margin-bottom: 1.5rem;
    }
    .contact-info h3 {
        color: #333;
        margin-bottom: 1rem;
    }
    .contact-info p {
        margin-bottom: 0.5rem;
    }
    .opening-hours {
        margin-top: 1rem;
    }
</style>

<main class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h1 class="card-title mb-4">Kontakta Oss</h1>
                    <p class="lead mb-4">Har du frågor eller funderingar? Vi finns här för att hjälpa dig!</p>

                    <form id="contact-form" method="post" action="process_contact.php">
                        <div class="mb-3">
                            <label for="name" class="form-label">Namn</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">E-post</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">Ämne</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Meddelande</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Skicka Meddelande</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="contact-info">
                        <h3>Kontaktinformation</h3>
                        
                        <div class="mb-4">
                            <h4 class="h5">Besöksadress</h4>
                            <p>
                                Bloggvägen 123<br>
                                123 45 Bloggstad
                            </p>
                        </div>

                        <div class="mb-4">
                            <h4 class="h5">E-post</h4>
                            <p><a href="mailto:info@bloggplattformen.se">info@bloggplattformen.se</a></p>
                        </div>

                        <div class="mb-4">
                            <h4 class="h5">Telefon</h4>
                            <p>08-123 45 67</p>
                        </div>

                        <div class="opening-hours">
                            <h4 class="h5">Öppettider</h4>
                            <p>
                                Måndag-Fredag: 09:00-17:00<br>
                                Lördag-Söndag: Stängt
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once 'templates/footer.php'; ?> 