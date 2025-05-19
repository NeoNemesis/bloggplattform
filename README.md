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
2. Kopiera `db_credentials.example.php` till `db_credentials.php` och konfigurera med dina databasuppgifter
3. Importera databasstrukturen från `database/schema.sql`
4. Placera filerna i din webbserver (t.ex. xampp/htdocs)

## Säkerhet
- CSRF-skydd
- XSS-skydd
- Säker filuppladdning
- Lösenordshashning
- Känsliga uppgifter är exkluderade från versionshantering

## Systemkrav
- PHP 7.1 eller högre
- MySQL 5.7 eller högre
- Webbserver (Apache/Nginx)

## Viktigt
- Känsliga filer som innehåller databasuppgifter eller lösenord är exkluderade från repositoryt
- Se till att konfigurera dina egna säkra uppgifter lokalt
- Följ säkerhetsbestämmelser för din miljö 