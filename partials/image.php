<?php
/**
 * image.php — PHP-хелпер для вывода адаптивных WebP изображений
 *
 * Использование в content.html или PHP-страницах:
 *   <?php require_once __DIR__ . '/../../partials/image.php'; ?>
 *   <?= render_image('obj-001-main') ?>
 *   <?= render_image('obj-001-main', 'content-image-wrapper--right') ?>
 *   <?= render_image('obj-001-main', 'content-image-wrapper--full', '100vw') ?>
 *
 * Читает data/media.json один раз и кэширует в static $registry.
 * Zero-dependency: только нативный PHP, никаких библиотек.
 */

/**
 * Генерирует HTML-блок <figure><picture> для адаптивного изображения.
 *
 * @param string $key            Ключ изображения в data/media.json
 * @param string $modifier_class BEM-модификатор (например, 'content-image-wrapper--right')
 * @param string $sizes          Атрибут sizes для srcset
 * @param string $loading        Стратегия загрузки: 'lazy' или 'eager'
 * @return string                Готовый HTML
 */
function render_image(
    string $key,
    string $modifier_class = '',
    string $sizes = '(max-width: 768px) 100vw, (max-width: 1024px) 100vw, 48%',
    string $loading = 'lazy'
): string {
    static $registry = null;

    // Загружаем реестр один раз за время выполнения страницы
    if ($registry === null) {
        $json_path = dirname(__DIR__) . '/data/media.json';
        if (!file_exists($json_path)) {
            return "<!-- render_image: data/media.json не найден -->";
        }
        $decoded = json_decode(file_get_contents($json_path), true);
        $registry = is_array($decoded) ? $decoded : [];
    }

    if (!isset($registry[$key])) {
        return "<!-- render_image: ключ '{$key}' не найден в media.json -->";
    }

    $data     = $registry[$key];
    $dir      = trim($data['dir'] ?? '', '/');
    $widths   = $data['widths'] ?? [];
    $alt      = htmlspecialchars($data['alt'] ?? '', ENT_QUOTES, 'UTF-8');
    $caption  = trim($data['caption'] ?? '');
    $loading  = in_array($loading, ['lazy', 'eager'], true) ? $loading : 'lazy';

    sort($widths);

    if (empty($widths) || empty($dir)) {
        return "<!-- render_image: некорректная запись для ключа '{$key}' -->";
    }

    // Формируем srcset
    $srcset_parts = [];
    foreach ($widths as $w) {
        $srcset_parts[] = "/assets/img/{$dir}/{$key}-{$w}.webp {$w}w";
    }
    $srcset = implode(",\n      ", $srcset_parts);

    // Fallback src — наибольший доступный размер
    $max_width   = end($widths);
    $fallback_src = "/assets/img/{$dir}/{$key}-{$max_width}.webp";

    // CSS-классы
    $classes = 'content-image-wrapper';
    if ($modifier_class !== '') {
        // Принимаем как полный класс ('content-image-wrapper--right')
        // так и короткий модификатор ('--right' или 'right')
        if (str_starts_with($modifier_class, 'content-image-wrapper')) {
            $classes .= ' ' . $modifier_class;
        } elseif (str_starts_with($modifier_class, '--')) {
            $classes .= ' content-image-wrapper' . $modifier_class;
        } else {
            $classes .= ' ' . $modifier_class;
        }
    }

    // Сборка HTML
    $html  = "<figure class=\"{$classes}\">\n";
    $html .= "  <picture>\n";
    $html .= "    <source\n";
    $html .= "      type=\"image/webp\"\n";
    $html .= "      srcset=\"\n      {$srcset}\n    \"\n";
    $html .= "      sizes=\"{$sizes}\">\n";
    $html .= "    <img\n";
    $html .= "      src=\"{$fallback_src}\"\n";
    $html .= "      alt=\"{$alt}\"\n";
    $html .= "      loading=\"{$loading}\"\n";
    $html .= "      decoding=\"async\">\n";
    $html .= "  </picture>\n";

    if ($caption !== '') {
        $caption_html = htmlspecialchars($caption, ENT_QUOTES, 'UTF-8');
        $html .= "  <figcaption>{$caption_html}</figcaption>\n";
    }

    $html .= "</figure>\n";

    return $html;
}
