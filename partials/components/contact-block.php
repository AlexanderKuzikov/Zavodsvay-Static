<?php
/**
 * Блок контактов для сайдбара
 */
$contacts = json_decode(file_get_contents(__DIR__ . '/../../data/components/contacts.json'), true);
?>
<div class="contact-block">
    <p>
        <a href="tel:<?= htmlspecialchars($contacts['phone']['href']) ?>">
            <?= htmlspecialchars($contacts['phone']['label']) ?>
        </a>
    </p>
    <?php if (!empty($contacts['email'])): ?>
    <p>
        <a href="mailto:<?= htmlspecialchars($contacts['email']) ?>">
            <?= htmlspecialchars($contacts['email']) ?>
        </a>
    </p>
    <?php endif; ?>
    <p><?= htmlspecialchars($contacts['address']) ?></p>
</div>
