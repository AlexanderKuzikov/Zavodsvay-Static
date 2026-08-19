<?php
/**
 * Сайдбар с контактами и меню статей
 */
?>
<aside class="sidebar">
    <?php include __DIR__ . '/components/icons-container.php'; ?>
    <button type="button" class="callback-btn" data-callback-open aria-label="Обратный звонок">
        <svg class="callback-btn__icon" aria-hidden="true" focusable="false"><use href="#icon-phone-callback"></use></svg>
        <span class="callback-btn__text">Обратный звонок</span>
    </button>
    <?php include __DIR__ . '/components/contact-block.php'; ?>
    <?php include __DIR__ . '/components/sidebar-menu.php'; ?>
</aside>
