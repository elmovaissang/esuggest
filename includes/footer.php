<?php
// pied de page
?>
    <!-- Fermeture du conteneur (ouvert dans header.php) -->
        </div>
    </main>

    <footer class="main-footer">
        <div class="footer-container">
            <div class="footer-section">
                <h3 class="footer-title">eSuggest</h3>
                <p>Gestion de factures électroniques conforme à la réforme 2024-2026.</p>
            </div>
            <div class="footer-section">
                <h3 class="footer-title">Liens utiles</h3>
                <ul class="footer-links">
                    <li><a href="<?= SITE_ROOT ?>index.php">Accueil</a></li>
                    <li><a href="<?= SITE_ROOT ?>factures/list.php">Factures</a></li>
                    <li><a href="<?= SITE_ROOT ?>classeurs/list.php">Classeurs</a></li>
                    <?php if (isAdmin()): ?>
                        <li><a href="<?= SITE_ROOT ?>admin/users.php">Utilisateurs</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="footer-section">
                <h3 class="footer-title">Contact</h3>
                <p>Email : contact@esuggest.fr</p>
                <p>Téléphone : +33 1 23 45 67 89</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> eSuggest. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>
