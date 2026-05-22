/**
 * migrate-object-images.mjs
 *
 * Копирует pages/map/img/{id}_N.webp → assets/img/objects/{id}/{id}_N.webp
 * map.json НЕ трогает (поле images хранит только имя файла, путь — в шаблоне)
 *
 * Запуск:
 *   node tools/migrate-object-images.mjs            # копировать
 *   node tools/migrate-object-images.mjs --dry-run  # превью без изменений
 *   npm run migrate:images                           # из корня
 *   npm run migrate:images:dry                      # dry-run из корня
 */

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const ROOT      = path.resolve(__dirname, '..');

const SRC_DIR   = path.join(ROOT, 'pages/map/img');
const DEST_ROOT = path.join(ROOT, 'assets/img/objects');

const DRY_RUN = process.argv.includes('--dry-run');

// --- Читаем map.json для валидации ---
const mapJson = JSON.parse(fs.readFileSync(path.join(ROOT, 'data/map.json'), 'utf8'));
const knownIds = new Set(mapJson.map(o => o.id));

// --- Сканируем source ---
if (!fs.existsSync(SRC_DIR)) {
    console.error(`❌ Source dir не найден: ${SRC_DIR}`);
    process.exit(1);
}

const files = fs.readdirSync(SRC_DIR).filter(f => f.endsWith('.webp'));

// Группируем по id: { id → [filename, ...] }
const byId = new Map();
for (const file of files) {
    const match = file.match(/^(\d+)_(\d+)\.webp$/);
    if (!match) {
        console.warn(`⚠️  Пропущен (не соответствует {id}_{n}.webp): ${file}`);
        continue;
    }
    const id = parseInt(match[1], 10);
    if (!byId.has(id)) byId.set(id, []);
    byId.get(id).push(file);
}

console.log(`\n📦 Объектов с файлами: ${byId.size}, файлов всего: ${files.length}`);
if (DRY_RUN) console.log('🔍 DRY RUN — файлы не копируются и не удаляются\n');

let copied = 0, skipped = 0, errors = 0;
const orphans = []; // файлы без id в map.json

for (const [id, imgFiles] of [...byId.entries()].sort((a, b) => a[0] - b[0])) {
    if (!knownIds.has(id)) {
        orphans.push({ id, files: imgFiles });
    }

    const destDir = path.join(DEST_ROOT, String(id));

    if (!DRY_RUN) {
        fs.mkdirSync(destDir, { recursive: true });
    }

    for (const file of imgFiles) {
        const src  = path.join(SRC_DIR, file);
        const dest = path.join(destDir, file);

        if (!DRY_RUN && fs.existsSync(dest)) {
            skipped++;
            continue;
        }

        if (DRY_RUN) {
            console.log(`  [copy] ${path.relative(ROOT, src)} → ${path.relative(ROOT, dest)}`);
            copied++;
            continue;
        }

        try {
            fs.copyFileSync(src, dest);
            copied++;
        } catch (e) {
            console.error(`  ❌ Ошибка копирования ${file}: ${e.message}`);
            errors++;
        }
    }
}

// --- Удаление источников (только если нет ошибок) ---
if (!DRY_RUN && errors === 0) {
    let deleted = 0;
    for (const [, imgFiles] of byId) {
        for (const file of imgFiles) {
            try {
                fs.unlinkSync(path.join(SRC_DIR, file));
                deleted++;
            } catch (e) {
                console.error(`  ❌ Не удалось удалить ${file}: ${e.message}`);
            }
        }
    }
    console.log(`\n🗑️  Удалено источников: ${deleted}`);
}

// --- Отчёт ---
if (DRY_RUN) {
    console.log(`\n🔍 DRY RUN итог: будет скопировано ${copied} файлов`);
} else {
    console.log(`\n✅ Скопировано: ${copied}`);
    if (skipped) console.log(`⏭️  Пропущено (уже существовали): ${skipped}`);
    if (errors)  console.log(`❌ Ошибок: ${errors}`);
}

if (orphans.length) {
    console.log(`\n⚠️  Файлы без записи в map.json (${orphans.length} объектов):`);
    for (const { id, files: f } of orphans) {
        console.log(`   id=${id}: ${f.join(', ')}`);
    }
}

if (!DRY_RUN && errors === 0) {
    console.log('\n📝 Следующий шаг: обновить $img_base в pages/objects/_template.php');
    console.log("   Было:  $og_image = '/pages/map/img/' . $object_id . '_1.webp';");
    console.log("         $img_base = '/pages/map/img/';");
    console.log("   Стало: $og_image = '/assets/img/objects/' . $object_id . '/' . $object_id . '_1.webp';");
    console.log("         $img_base = '/assets/img/objects/' . $object_id . '/';");
}
