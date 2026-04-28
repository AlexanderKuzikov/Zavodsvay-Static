<?php
/**
 * Шапка сайта с логотипом и навигацией
 */
$nav = json_decode(file_get_contents(__DIR__ . '/../data/components/nav.json'), true);
$currentUrl = $_SERVER['REQUEST_URI'] ?? '/';
?>
<header>
    <div class="header-content-wrapper">
        <div class="header-logo">
            <img src="/assets/img/logocircletrans180.webp" alt="Завод винтовых свай Гефест">
            <span>Завод винтовых свай<br>«Гефест»</span>
        </div>
        <button class="hamburger-menu-toggle" aria-label="Открыть меню">
            <span class="hamburger-icon"></span>
            <span class="hamburger-icon"></span>
            <span class="hamburger-icon"></span>
        </button>
        <nav>
            <ul>
                <?php foreach ($nav['items'] as $item): ?>
                <li>
                    <a href="<?= htmlspecialchars($item['url']) ?>"
                       <?= (isset($currentUrl) && $currentUrl === $item['url']) ? 'class="active"' : '' ?>>
                        <?= htmlspecialchars($item['label']) ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </div>
</header>
