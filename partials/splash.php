<?php
/**
 * Splash screen - только для главной страницы
 */
$splashCompany1 = $splashCompany1 ?? 'Завод винтовых свай';
$splashCompany2 = $splashCompany2 ?? '«Гефест»';
$splashSlogan = $splashSlogan ?? 'Производство и монтаж винтовых свай в Перми';
?>
<div id="splash-screen">
    <picture class="background-picture">
        <source media="(min-width: 1201px)"
                srcset="/assets/img/start/start-1600.webp 1600w,
                        /assets/img/start/start-1920.webp 1920w"
                sizes="100vw" type="image/webp">
        <source srcset="/assets/img/start/start-480.webp 480w,
                        /assets/img/start/start-600.webp 600w,
                        /assets/img/start/start-800.webp 800w,
                        /assets/img/start/start-1200.webp 1200w"
                sizes="100vw" type="image/webp">
        <img src="/assets/img/start/start-fallback.jpg" alt="" sizes="100vw">
    </picture>
    <img src="/assets/img/logocircletrans360.webp" alt="" class="logo">
    <div class="company-name-container">
        <p class="company-name-part1"><?= htmlspecialchars($splashCompany1) ?></p>
        <p class="company-name-part2"><?= htmlspecialchars($splashCompany2) ?></p>
    </div>
    <p class="slogan"><?= htmlspecialchars($splashSlogan) ?></p>
</div>
