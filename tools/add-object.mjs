/**
 * add-object.mjs
 *
 * CLI для добавления нового объекта на карту.
 * Обновляет data/map.json, создаёт pages/objects/{id}/index.php, обновляет sitemap.xml.
 *
 * Запуск:
 *   node tools/add-object.mjs \
 *     --id 530 \
 *     --coords "58.0105,56.2502" \
 *     --category house \
 *     --title "Жилой дом, ул. Ленина 12, Пермь" \
 *     --desc "Ø89, 6 свай, 2.5м" \
 *     --images "530_1.webp,530_2.webp"
 *
 *   Флаги:
 *     --dry-run   только вывод, без записи файлов
 *     --no-page   не создавать pages/objects/{id}/index.php
 *     --no-sitemap  не обновлять sitemap.xml
 */

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const ROOT = path.resolve(__dirname, '..');

// === Константы ===
const CATEGORIES = ['house', 'banya', 'fence', 'commercial', 'industrial', 'water', 'social', 'agro', 'other'];
const MAP_JSON   = path.join(ROOT, 'data/map.json');
const SITEMAP    = path.join(ROOT, 'sitemap.xml');
const PAGES_DIR  = path.join(ROOT, 'pages/objects');
const IMG_DIR    = path.join(ROOT, 'assets/img/objects');

// === Парсинг аргументов ===
const args = process.argv.slice(2);
const DRY_RUN     = args.includes('--dry-run');
const NO_PAGE     = args.includes('--no-page');
const NO_SITEMAP  = args.includes('--no-sitemap');

function getArg(name) {
    const idx = args.indexOf(`--${name}`);
    return idx !== -1 && args[idx + 1] ? args[idx + 1] : null;
}

const id       = parseInt(getArg('id'), 10);
const coordsRaw = getArg('coords');
const category  = getArg('category');
const title     = getArg('title');
const desc      = getArg('desc') || '';
const imagesRaw = getArg('images') || '';

// === Валидация ===
const errors = [];

if (!id || isNaN(id) || id <= 0)  errors.push('--id: обязательный, положительное целое число');
if (!coordsRaw)                   errors.push('--coords: обязательный, формат "lat,lng"');
if (!category)                    errors.push('--category: обязательный');
if (!title)                       errors.push('--title: обязательный');

let coords = null;
if (coordsRaw) {
    const parts = coordsRaw.split(',').map(s => parseFloat(s.trim()));
    if (parts.length !== 2 || parts.some(isNaN)) {
        errors.push('--coords: неверный формат, ожидается "lat,lng" (например "58.0105,56.2502")');
    } else {
        coords = parts;
    }
}

if (category && !CATEGORIES.includes(category)) {
    errors.push(`--category: неизвестная категория "${category}". Допустимые: ${CATEGORIES.join(', ')}`);
}

if (errors.length) {
    console.error('\n❌ Ошибки валидации:');
    errors.forEach(e => console.error(`   • ${e}`));
    console.error('\nПример:');
    console.error('  node tools/add-object.mjs --id 530 --coords "58.0105,56.2502" --category house --title "Дом" --desc "Ø89, 6 свай" --images "530_1.webp"');
    process.exit(1);
}

// === Загружаем map.json ===
if (!fs.existsSync(MAP_JSON)) {
    console.error(`❌ Не найден ${MAP_JSON}`);
    process.exit(1);
}
const mapData = JSON.parse(fs.readFileSync(MAP_JSON, 'utf8'));

// Проверяем дубль id
if (mapData.some(o => o.id === id)) {
    console.error(`❌ Объект с id=${id} уже существует в map.json`);
    process.exit(1);
}

// === Формируем объект ===
const images = imagesRaw ? imagesRaw.split(',').map(s => s.trim()).filter(Boolean) : [];

const newObject = {
    id,
    coords,
    category,
    title,
    techDescription: desc,
    images,
    url: `/objects/${id}/`
};

// === Проверяем наличие папки с изображениями ===
const imgObjectDir = path.join(IMG_DIR, String(id));
const imgWarnings = [];

if (!fs.existsSync(imgObjectDir)) {
    imgWarnings.push(`⚠️  Папка изображений не найдена: assets/img/objects/${id}/`);
} else if (images.length > 0) {
    for (const img of images) {
        const imgPath = path.join(imgObjectDir, img);
        if (!fs.existsSync(imgPath)) {
            imgWarnings.push(`⚠️  Файл не найден: assets/img/objects/${id}/${img}`);
        }
    }
}

// === Вывод preview ===
console.log('\n📋 Новый объект:');
console.log(JSON.stringify(newObject, null, 2));

if (imgWarnings.length) {
    console.log('');
    imgWarnings.forEach(w => console.log(w));
}

if (DRY_RUN) {
    console.log('\n🔍 DRY RUN — файлы не изменяются');
    console.log('   Будет добавлено в map.json');
    if (!NO_PAGE)    console.log(`   Будет создан: pages/objects/${id}/index.php`);
    if (!NO_SITEMAP) console.log(`   Будет обновлён: sitemap.xml`);
    process.exit(0);
}

// === 1. Обновляем map.json ===
mapData.push(newObject);

// Сортируем по id для порядка
mapData.sort((a, b) => a.id - b.id);

fs.writeFileSync(MAP_JSON, JSON.stringify(mapData, null, 4) + '\n', 'utf8');
console.log(`\n✅ map.json обновлён (всего объектов: ${mapData.length})`);

// === 2. Создаём pages/objects/{id}/index.php ===
if (!NO_PAGE) {
    const pageDir  = path.join(PAGES_DIR, String(id));
    const pageFile = path.join(pageDir, 'index.php');

    if (fs.existsSync(pageFile)) {
        console.log(`⏭️  pages/objects/${id}/index.php уже существует, пропуск`);
    } else {
        fs.mkdirSync(pageDir, { recursive: true });
        const pageContent = `<?php $object_id = ${id}; $object_dir = __DIR__; require __DIR__ . '/../../_template.php';\n`;
        fs.writeFileSync(pageFile, pageContent, 'utf8');
        console.log(`✅ Создан: pages/objects/${id}/index.php`);
    }
}

// === 3. Обновляем sitemap.xml ===
if (!NO_SITEMAP) {
    if (!fs.existsSync(SITEMAP)) {
        console.log(`⚠️  sitemap.xml не найден, пропуск`);
    } else {
        let sitemap = fs.readFileSync(SITEMAP, 'utf8');
        const today = new Date().toISOString().split('T')[0];
        const newUrl = `\n    <url>\n        <loc>https://zavodsvay.ru/objects/${id}/</loc>\n        <lastmod>${today}</lastmod>\n        <changefreq>monthly</changefreq>\n        <priority>0.6</priority>\n    </url>`;

        // Вставляем перед закрывающим </urlset>
        if (sitemap.includes('</urlset>')) {
            sitemap = sitemap.replace('</urlset>', newUrl + '\n</urlset>');
            fs.writeFileSync(SITEMAP, sitemap, 'utf8');
            console.log(`✅ sitemap.xml обновлён (добавлен /objects/${id}/)`);
        } else {
            console.log(`⚠️  sitemap.xml: тег </urlset> не найден, пропуск`);
        }
    }
}

// === Итог ===
console.log(`\n🎉 Объект id=${id} успешно добавлен!`);
if (images.length === 0) {
    console.log(`⚠️  Изображения не указаны. Положи файлы в assets/img/objects/${id}/`);
} else if (imgWarnings.length) {
    console.log(`⚠️  Не забудь добавить недостающие изображения в assets/img/objects/${id}/`);
}
