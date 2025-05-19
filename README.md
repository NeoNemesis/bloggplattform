# Bloggplattform

En modern bloggplattform utvecklad i PHP med följande funktioner:

## Funktioner
- Användarhantering (registrering, inloggning)
- Blogginlägg med bildstöd
- Admin-panel för innehållshantering
- Responsiv design med Bootstrap
- Säker filhantering

## Installation
1. Klona detta repository
2. Importera `d0019e_blog.sql` till din MySQL-databas
3. Konfigurera `db_credentials.php` med dina databasuppgifter
4. Placera filerna i din webbserver (t.ex. xampp/htdocs)

## Systemkrav
- PHP 7.1 eller högre
- MySQL 5.7 eller högre
- Webbserver (Apache/Nginx)

## Säkerhet
- CSRF-skydd
- XSS-skydd
- Säker filuppladdning
- Lösenordshashning 