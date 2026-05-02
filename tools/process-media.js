/**
 * process-media.js — сканирует source/, регистрирует новые файлы в media.json, нарезает WebP.
 * Поддерживаемые форматы: jpg, jpeg, png, webp, gif (включая анимированные).
 * GIF конвертируется в анимированный WebP.
 *
 * Ключ = относительный путь от source/ без расширения, слэши → дефисы.
 * source/logo2.png              → "logo2"
 * source/objects/obj-001/main.jpg → "objects-obj-001-main"
 * source/banner.gif             → "banner"
 */

import fs from 'fs/promises';
import path from 'path';
import sharp from 'sharp';

export const ROOT_DIR = path.resolve(process.cwd(), '..');
export const SOURCE_DIR = path.join(ROOT_DIR, 'source');
export const MEDIA_JSON_PATH = path.join(ROOT_DIR, 'data', 'media.json');
export const ASSETS_IMG_DIR = path.join(ROOT_DIR, 'assets', 'img');

const SUPPORTED_EXTS = ['.jpg', '.jpeg', '.png', '.webp', '.gif'];
const DEFAULT_WIDTHS = [320, 640, 1024, 1600];

export async function scanSource(dir, baseDir, results = []) {
  let entries;
  try {
    entries = await fs.readdir(dir, { withFileTypes: true });
  } catch {
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
      results.push({ key, file: 'source/' + rel, dir: relDir === '.' ? '' : relDir });
    }
  }
  return results;
}

export async function readMedia() {
  try {
    const raw = await fs.readFile(MEDIA_JSON_PATH, 'utf-8');
    return JSON.parse(raw);
  } catch {
    return {};
  }
}

export async function writeMedia(media) {
  await fs.writeFile(MEDIA_JSON_PATH, JSON.stringify(media, null, 2) + '\n');
}

/**
 * Нарезает одну запись из реестра.
 * GIF обрабатывается с флагом animated:true — сохраняется анимация.
 */
export async function processEntry(key, data, onLog = console.log) {
  const srcPath = path.join(ROOT_DIR, data.file);
  const ext = path.extname(data.file).toLowerCase();
  const isGif = ext === '.gif';

  try {
    await fs.access(srcPath);
  } catch {
    onLog(`[skip] Не найден: ${data.file}`);
    return data;
  }

  // Для GIF читаем метаданные с учётом анимации
  const image = sharp(srcPath, isGif ? { animated: true } : {});
  const meta = await image.metadata();
  const origWidth = meta.width;
  const origHeight = meta.pageHeight ?? meta.height; // pageHeight для анимированных

  const validWidths = data.widths.filter(w => w <= origWidth);
  const finalWidths = validWidths.length ? validWidths : [origWidth];

  const skipped = data.widths.filter(w => w > origWidth);
  if (skipped.length) onLog(`   ℹ  Пропущены (> ${origWidth}px): ${skipped.join(', ')}px`);

  const outDir = data.dir ? path.join(ASSETS_IMG_DIR, data.dir) : ASSETS_IMG_DIR;
  await fs.mkdir(outDir, { recursive: true });

  for (const w of finalWidths) {
    const outFile = path.join(outDir, `${key}-${w}.webp`);
    await sharp(srcPath, isGif ? { animated: true } : {})
      .resize({ width: w, withoutEnlargement: true })
      .webp({ effort: 4, quality: 80 })
      .toFile(outFile);
    onLog(`   └─ ✓ ${key}-${w}.webp${isGif ? ' (animated)' : ''}`);
  }

  return {
    ...data,
    orig_width: origWidth,
    orig_height: origHeight,
    widths: finalWidths,
    generated: true
  };
}

// CLI-режим
async function runCLI() {
  let media = await readMedia();

  const found = await scanSource(SOURCE_DIR, SOURCE_DIR);
  let newCount = 0;
  for (const { key, file, dir } of found) {
    if (!media[key]) {
      media[key] = { file, dir, widths: DEFAULT_WIDTHS, alt: '', caption: '', generated: false };
      console.log(`✚ ${key}  (${file})`);
      newCount++;
    }
  }
  if (newCount > 0) {
    await writeMedia(media);
    console.log(`✅ Добавлено записей: ${newCount}\n`);
    media = await readMedia();
  }

  const pending = Object.entries(media).filter(([, d]) => !d.generated);
  if (pending.length === 0) { console.log('✅ Нет новых изображений.'); return; }

  console.log(`🔄 Нарезка: ${pending.length} файлов\n`);
  let ok = 0, fail = 0;

  for (const [key, data] of pending) {
    console.log(`📸 ${key}`);
    try {
      media[key] = await processEntry(key, data);
      ok++;
    } catch (err) {
      console.error(`   ❌ ${err.message}`);
      fail++;
    }
  }

  await writeMedia(media);
  console.log(`\n📊 Итого: ok=${ok}, fail=${fail}.`);
}

runCLI();
