<?php
/**
 * Меню статей в сайдбаре
 */
$menu = json_decode(file_get_contents(__DIR__ . '/../../data/components/sidebar-menu.json'), true);
?>
<div class="sidebar-menu-title">
    <?= htmlspecialchars($menu['title']) ?>
</div>
<div class="sidebar-menu-list">
    <ul>
        <?php foreach ($menu['items'] as $item): ?>
        <li>
            <a href="<?= htmlspecialchars($item['url']) ?>">
                <?= htmlspecialchars($item['label']) ?>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
</div>
