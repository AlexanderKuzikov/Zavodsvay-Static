/**
 * process-media.js — нарезка изображений из реестра data/media.json
 *
 * Запуск из папки tools/:
 *   node process-media.js
 * Или через npm:
 *   npm run media
 *
 * Логика:
 *   - Читает data/media.json
 *   - Обрабатывает только записи с generated: false
 *   - Smart Source Limit: не генерирует размеры больше оригинала
 *   - Сохраняет WebP в assets/img/{dir}/{key}-{width}.webp
 *   - Обновляет флаг generated: true в реестре после успешной нарезки
 */

import fs from 'fs/promises';
import path from 'path';
import sharp from 'sharp';

// Скрипт запускается из tools/, корень проекта — на уровень выше
const ROOT_DIR = path.resolve(process.cwd(), '..');
const MEDIA_JSON_PATH = path.join(ROOT_DIR, 'data', 'media.json');
const ASSETS_IMG_DIR = path.join(ROOT_DIR, 'assets', 'img');

async function processMedia() {
  let media;

  try {
    const rawData = await fs.readFile(MEDIA_JSON_PATH, 'utf-8');
    media = JSON.parse(rawData);
  } catch (err) {
    console.error('❌ Не удалось прочитать data/media.json:', err.message);
    process.exit(1);
  }

  const entries = Object.entries(media);
  const pending = entries.filter(([, data]) => !data.generated);

  if (pending.length === 0) {
    console.log('✅ Нет новых изображений для обработки (все generated: true).');
    return;
  }

  console.log(`🔄 Старт обработки медиабиблиотеки. Новых записей: ${pending.length}`);

  let successCount = 0;
  let errorCount = 0;
  let updated = false;

  for (const [key, data] of pending) {
    const sourcePath = path.join(ROOT_DIR, data.file);

    // Проверяем существование исходного файла
    try {
      await fs.access(sourcePath);
    } catch {
      console.error(`\n[Пропуск] Файл не найден: ${data.file}`);
      errorCount++;
      continue;
    }

    console.log(`\n📸 Обработка: ${key}`);
    console.log(`   Источник: ${data.file}`);

    try {
      const image = sharp(sourcePath);
      const metadata = await image.metadata();
      const origWidth = metadata.width;

      console.log(`   Оригинал: ${origWidth}×${metadata.height}px`);

      // Smart Source Limit: убираем ширины больше оригинала
      let validWidths = (data.widths || []).filter(w => w <= origWidth);

      // Если оригинал меньше минимальной ширины — берём его размер as-is
      if (validWidths.length === 0) {
        validWidths = [origWidth];
        console.log(`   ⚠️  Оригинал меньше всех запрошенных размеров. Генерируем только: ${origWidth}px`);
      }

      const skipped = (data.widths || []).filter(w => w > origWidth);
      if (skipped.length > 0) {
        console.log(`   ℹ️  Пропущены размеры (больше оригинала): ${skipped.join(', ')}px`);
      }

      // Создаём выходную папку если не существует
      const outDir = path.join(ASSETS_IMG_DIR, data.dir);
      await fs.mkdir(outDir, { recursive: true });

      // Нарезка
      for (const width of validWidths) {
        const outFile = path.join(outDir, `${key}-${width}.webp`);

        await sharp(sourcePath)
          .resize({ width, withoutEnlargement: true })
          .webp({ effort: 4, quality: 80 })
          .toFile(outFile);

        console.log(`   └─ ✓ ${key}-${width}.webp`);
      }

      // Ставим флаг
      media[key].generated = true;
      updated = true;
      successCount++;

    } catch (err) {
      console.error(`   ❌ Ошибка при обработке ${key}:`, err.message);
      errorCount++;
    }
  }

  // Сохраняем обновлённый реестр
  if (updated) {
    try {
      await fs.writeFile(MEDIA_JSON_PATH, JSON.stringify(media, null, 2) + '\n');
      console.log('\n✅ data/media.json обновлён.');
    } catch (err) {
      console.error('❌ Не удалось записать data/media.json:', err.message);
    }
  }

  console.log(`\n📊 Итого: обработано ${successCount}, ошибок ${errorCount}.`);
}

processMedia();
