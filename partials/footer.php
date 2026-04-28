<?php
/**
 * Подвал сайта
 */
$contacts = json_decode(file_get_contents(__DIR__ . '/../data/components/contacts.json'), true);
$footer = json_decode(file_get_contents(__DIR__ . '/../data/components/footer.json'), true);
?>
<footer>
    <div class="footer-content-wrapper">
        <div class="footer-nav">
            <ul>
                <?php foreach ($footer['links'] as $link): ?>
                <li><a href="<?= htmlspecialchars($link['url']) ?>"><?= htmlspecialchars($link['label']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="footer-social footer-info">
            <?php include __DIR__ . '/components/icons-container.php'; ?>
            <p>
                <a href="tel:<?= htmlspecialchars($contacts['phone']['href']) ?>"><?= htmlspecialchars($contacts['phone']['label']) ?></a><br>
                <a href="mailto:<?= htmlspecialchars($contacts['email']) ?>"><?= htmlspecialchars($contacts['email']) ?></a><br>
                <?= htmlspecialchars($contacts['address']) ?>
            </p>
        </div>
        <div class="footer-copyright">
            <p><?= htmlspecialchars($footer['copyright']) ?></p>
        </div>
    </div>
</footer>
