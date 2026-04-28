<?php
/**
 * Контейнер с иконками мессенджеров
 */
$contacts = json_decode(file_get_contents(__DIR__ . '/../../data/components/contacts.json'), true);
?>
<div class="icons-container">
    <?php if (!empty($contacts['phone'])): ?>
    <a href="tel:<?= htmlspecialchars($contacts['phone']['href']) ?>" class="icon-link" aria-label="Позвонить">
        <svg aria-hidden="true"><use href="#icon-phone"></use></svg>
    </a>
    <?php endif; ?>
    <?php if (!empty($contacts['telegram'])): ?>
    <a href="<?= htmlspecialchars($contacts['telegram']['url']) ?>" class="icon-link" aria-label="Telegram" target="_blank" rel="noopener">
        <svg aria-hidden="true"><use href="#icon-telegram"></use></svg>
    </a>
    <?php endif; ?>
    <?php if (!empty($contacts['whatsapp'])): ?>
    <a href="<?= htmlspecialchars($contacts['whatsapp']['url']) ?>" class="icon-link" aria-label="WhatsApp" target="_blank" rel="noopener">
        <svg aria-hidden="true"><use href="#icon-whatsapp"></use></svg>
    </a>
    <?php endif; ?>
    <?php if (!empty($contacts['vk'])): ?>
    <a href="<?= htmlspecialchars($contacts['vk']['url']) ?>" class="icon-link" aria-label="VK" target="_blank" rel="noopener">
        <svg aria-hidden="true"><use href="#icon-vk"></use></svg>
    </a>
    <?php endif; ?>
</div>
