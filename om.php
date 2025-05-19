<?php
require_once 'templates/header.php';
?>

<style>
    .team-member {
        text-align: center;
        margin-bottom: 2rem;
    }
    .team-member img {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        margin-bottom: 1rem;
        object-fit: cover;
    }
    .feature-list {
        list-style: none;
        padding-left: 0;
    }
    .feature-list li {
        margin-bottom: 0.5rem;
        padding-left: 1.5rem;
        position: relative;
    }
    .feature-list li:before {
        content: "✓";
        color: #28a745;
        position: absolute;
        left: 0;
    }
</style>

<main class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-body">
                    <h1 class="card-title">Om Bloggplattformen</h1>
                    
                    <section class="mb-5">
                        <h2>Vår Vision</h2>
                        <p class="lead">
                            Bloggplattformen skapades med målet att ge kreativa skribenter en plats att dela sina tankar, idéer och 
                            berättelser. Vi tror på kraften i det skrivna ordet och möjligheten att inspirera andra genom personliga 
                            erfarenheter och kunskaper.
                        </p>
                    </section>

                    <section class="mb-5">
                        <h2>Vad Vi Erbjuder</h2>
                        <ul class="feature-list">
                            <li>En användarvänlig plattform för att skapa och dela blogginlägg</li>
                            <li>Möjlighet att interagera med andra bloggare och läsare</li>
                            <li>Anpassningsbara bloggsidor</li>
                            <li>Säker och pålitlig hosting av ditt innehåll</li>
                        </ul>
                    </section>

                    <section class="mb-5">
                        <h2>Vårt Team</h2>
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="team-member">
                                    <img src="images/team/placeholder.jpg" alt="Anna Andersson" class="mb-3">
                                    <h3>Anna Andersson</h3>
                                    <p class="text-muted">Grundare & Utvecklare</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="team-member">
                                    <img src="images/team/placeholder.jpg" alt="Erik Eriksson" class="mb-3">
                                    <h3>Erik Eriksson</h3>
                                    <p class="text-muted">Design & UX</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="team-member">
                                    <img src="images/team/placeholder.jpg" alt="Maria Nilsson" class="mb-3">
                                    <h3>Maria Nilsson</h3>
                                    <p class="text-muted">Community Manager</p>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once 'templates/footer.php'; ?> 