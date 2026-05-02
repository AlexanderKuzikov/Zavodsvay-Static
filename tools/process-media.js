/**
 * process-media.js — сканирует source/, регистрирует новые файлы в media.json, нарезает WebP
 *
 * Запуск из папки tools/:
 *   node process-media.js
 *
 * Ключ = относительный путь от source/ без расширения, слэши → дефисы
 * Пример: source/objects/obj-001/main.jpg → ключ "objects-obj-001-main"
 *          source/logo2.png → ключ "logo2"
 */

import fs from 'fs/promises';
import path from 'path';
import sharp from 'sharp';

const ROOT_DIR = path.resolve(process.cwd(), '..');
const SOURCE_DIR = path.join(ROOT_DIR, 'source');
const MEDIA_JSON_PATH = path.join(ROOT_DIR, 'data', 'media.json');
const ASSETS_IMG_DIR = path.join(ROOT_DIR, 'assets', 'img');

const SUPPORTED_EXTS = ['.jpg', '.jpeg', '.png', '.webp'];
const DEFAULT_WIDTHS = [320, 640, 1024, 1600];

async function scanSource(dir, baseDir, results = []) {
  let entries;
  try {
    entries = await fs.readdir(dir, { withFileTypes: true });
  } catch {
    console.log(`⚠️  source/ не найден: ${dir}`);
    return results;
  }
  for (const entry of entries) {
    const fullPath = path.join(dir, entry.name);
    if (entry.isDirectory()) {
      await scanSource(fullPath, baseDir, results);
    } else if (SUPPORTED_EXTS.includes(path.extname(entry.name).toLowerCase())) {
      const rel = path.relative(baseDir, fullPath).replace(/\\/g, '/');
      const key = rel.replace(/\.[^.]+$/, '').replace(/\//g, '-');
      const relDir = path.dirname(rel).replace(/\\/g, '/');
      results.push({
        key,
        file: 'source/' + rel,
        dir: relDir === '.' ? '' : relDir
      });
    }
  }
  return results;
}

async function processMedia() {
  let media = {};
  try {
    const raw = await fs.readFile(MEDIA_JSON_PATH, 'utf-8');
    media = JSON.parse(raw);
  } catch {
    console.log('ℹ️  data/media.json не найден — будет создан.');
  }

  // Сканируем source/ и регистрируем новые
  const found = await scanSource(SOURCE_DIR, SOURCE_DIR);
  let newCount = 0;
  for (const { key, file, dir } of found) {
    if (!media[key]) {
      media[key] = { file, dir, widths: DEFAULT_WIDTHS, alt: '', caption: '', generated: false };
      console.log(`➕ Зарегистрирован: ${key}  (${file})`);
      newCount++;
    }
  }
  if (newCount > 0) {
    await fs.writeFile(MEDIA_JSON_PATH, JSON.stringify(media, null, 2) + '\n');
    console.log(`✅ Добавлено записей: ${newCount}\n`);
  }

  // Нарезаем всё с generated: false
  const pending = Object.entries(media).filter(([, d]) => !d.generated);
  if (pending.length === 0) {
    console.log('✅ Нет новых изображений для нарезки.');
    return;
  }

  console.log(`🔄 Нарезка: ${pending.length} файлов\n`);
  let ok = 0, fail = 0;

  for (const [key, data] of pending) {
    const srcPath = path.join(ROOT_DIR, data.file);
    try {
      await fs.access(srcPath);
    } catch {
      console.error(`[Пропуск] Не найден: ${data.file}`);
      fail++;
      continue;
    }

    console.log(`📸 ${key}`);
    try {
      const meta = await sharp(srcPath).metadata();
      const validWidths = data.widths.filter(w => w <= meta.width);
      const finalWidths = validWidths.length ? validWidths : [meta.width];

      const skipped = data.widths.filter(w => w > meta.width);
      if (skipped.length) {
        console.log(`   ℹ️  Пропущены (> ${meta.width}px): ${skipped.join(', ')}px`);
      }

      const outDir = data.dir ? path.join(ASSETS_IMG_DIR, data.dir) : ASSETS_IMG_DIR;
      await fs.mkdir(outDir, { recursive: true });

      for (const w of finalWidths) {
        const outFile = path.join(outDir, `${key}-${w}.webp`);
        await sharp(srcPath)
          .resize({ width: w, withoutEnlargement: true })
          .webp({ effort: 4, quality: 80 })
          .toFile(outFile);
        console.log(`   └─ ✓ ${key}-${w}.webp`);
      }

      media[key].generated = true;
      ok++;
    } catch (err) {
      console.error(`   ❌ Ошибка: ${err.message}`);
      fail++;
    }
  }

  await fs.writeFile(MEDIA_JSON_PATH, JSON.stringify(media, null, 2) + '\n');
  console.log(`\n📊 Итого: обработано ${ok}, ошибок ${fail}.`);
}

processMedia();
