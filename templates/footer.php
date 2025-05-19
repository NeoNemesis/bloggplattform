<?php
/**
 * Sidfot för alla sidor på Bloggplattformen
 * 
 * Denna mall innehåller:
 * - Information om webbplatsen
 * - Snabblänkar till viktiga sidor
 * - Sociala medier-länkar
 * - Copyright-information
 * - JavaScript-inkluderingar
 */

// Beräkna relativ sökväg för filer baserat på aktuell mapp
$base_path = '';
if (strpos($_SERVER['PHP_SELF'], '/auth/') !== false) {
    $base_path = '../';
}
?>

<!-- Sidfot med tre kolumner -->
<footer class="site-footer">
    <div class="footer-content">
        <!-- Om Bloggplattformen -->
        <div class="footer-section">
            <div class="footer-heading">
                <h4>Om Bloggplattformen</h4>
                <div class="heading-line"></div>
            </div>
            <p>En plats för kreativa skribenter att dela sina tankar och idéer med världen.</p>
        </div>
        
        <!-- Snabblänkar -->
        <div class="footer-section">
            <div class="footer-heading">
                <h4>Snabblänkar</h4>
                <div class="heading-line"></div>
            </div>
            <ul class="footer-links">
                <li><a href="<?php echo $base_path; ?>om.php">Om oss</a></li>
                <li><a href="<?php echo $base_path; ?>kontakt.php">Kontakta oss</a></li>
                <li><a href="<?php echo $base_path; ?>blogg.php">Senaste inläggen</a></li>
            </ul>
        </div>
        
        <!-- Sociala medier -->
        <div class="footer-section">
            <div class="footer-heading">
                <h4>Följ oss</h4>
                <div class="heading-line"></div>
            </div>
            <div class="social-links">
                <a href="#" class="social-link" aria-label="Facebook">
                    <i class="bi bi-facebook"></i>
                </a>
                <a href="#" class="social-link" aria-label="Twitter">
                    <i class="bi bi-twitter"></i>
                </a>
                <a href="#" class="social-link" aria-label="Instagram">
                    <i class="bi bi-instagram"></i>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Copyright och avskiljare -->
    <div class="footer-bottom">
        <div class="heading-line"></div>
        <p>&copy; <?php echo date('Y'); ?> Bloggplattformen. Alla rättigheter förbehållna.</p>
    </div>
</footer>

<!-- JavaScript-filer -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<?php if (file_exists($base_path . 'javascript/main.js')): // Inkludera egen JavaScript om filen finns ?>
    <script src="<?php echo $base_path; ?>javascript/main.js"></script>
<?php endif; ?>
</body>
</html>