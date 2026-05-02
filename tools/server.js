/**
 * server.js — локальный Express-сервер медиабиблиотеки
 *
 * Запуск из папки tools/:
 *   node server.js
 *   npm run ui
 *
 * UI: http://localhost:3010
 *
 * API:
 *   GET    /api/media          — весь реестр
 *   GET    /api/scan           — сканирует source/, регистрирует новые
 *   POST   /api/media/:key     — сохранить alt/caption, regenerate=true → перенарезать
 *   DELETE /api/media/:key     — удалить запись; ?files=1 → удалить и WebP из assets/
 *   GET    /api/orphans        — WebP-файлы в assets/img/ без записи в реестре
 *   DELETE /api/orphans        — удалить все orphan-файлы
 *   POST   /api/process        — нарезать все с generated:false
 *   GET    /api/thumb/:key     — превью 200px из source/
 */

import express from 'express';
import path from 'path';
import fs from 'fs/promises';
import sharp from 'sharp';
import {
  ROOT_DIR, SOURCE_DIR, ASSETS_IMG_DIR,
  scanSource, readMedia, writeMedia, processEntry
} from './process-media.js';

const DEFAULT_WIDTHS = [320, 640, 1024, 1600];
const PORT = 3010;
const app = express();

app.use(express.json());
app.use(express.static(path.join(process.cwd(), 'ui')));
app.use('/assets', express.static(path.join(ROOT_DIR, 'assets')));

// GET /api/media
app.get('/api/media', async (req, res) => {
  res.json(await readMedia());
});

// GET /api/scan
app.get('/api/scan', async (req, res) => {
  const media = await readMedia();
  const found = await scanSource(SOURCE_DIR, SOURCE_DIR);
  let newCount = 0;
  for (const { key, file, dir } of found) {
    if (!media[key]) {
      media[key] = { file, dir, widths: DEFAULT_WIDTHS, alt: '', caption: '', generated: false };
      newCount++;
    }
  }
  if (newCount > 0) await writeMedia(media);
  res.json({ added: newCount, total: Object.keys(media).length });
});

// GET /api/thumb/:key
app.get('/api/thumb/:key', async (req, res) => {
  const media = await readMedia();
  const data = media[req.params.key];
  if (!data) return res.status(404).send('Not found');
  const srcPath = path.join(ROOT_DIR, data.file);
  const ext = path.extname(data.file).toLowerCase();
  const isGif = ext === '.gif';
  try {
    const buf = await sharp(srcPath, isGif ? { animated: true } : {})
      .resize({ width: 200 })
      .webp({ quality: 70 })
      .toBuffer();
    res.set('Content-Type', 'image/webp').send(buf);
  } catch {
    res.status(500).send('Error');
  }
});

// POST /api/media/:key
app.post('/api/media/:key', async (req, res) => {
  const { key } = req.params;
  const { alt, caption, regenerate } = req.body;
  const media = await readMedia();
  if (!media[key]) return res.status(404).json({ error: 'Key not found' });

  media[key].alt = alt ?? media[key].alt;
  media[key].caption = caption ?? media[key].caption;
  if (regenerate) media[key].generated = false;

  const logs = [];
  if (!media[key].generated) {
    try {
      media[key] = await processEntry(key, media[key], l => logs.push(l));
    } catch (err) {
      return res.status(500).json({ error: err.message, logs });
    }
  }

  await writeMedia(media);
  res.json({ ok: true, entry: media[key], logs });
});

// DELETE /api/media/:key?files=1
app.delete('/api/media/:key', async (req, res) => {
  const { key } = req.params;
  const deleteFiles = req.query.files === '1';
  const media = await readMedia();
  if (!media[key]) return res.status(404).json({ error: 'Key not found' });

  const data = media[key];
  const deleted = [];

  if (deleteFiles && data.widths && data.widths.length) {
    const outDir = data.dir ? path.join(ASSETS_IMG_DIR, data.dir) : ASSETS_IMG_DIR;
    for (const w of data.widths) {
      const filePath = path.join(outDir, `${key}-${w}.webp`);
      try {
        await fs.unlink(filePath);
        deleted.push(`${key}-${w}.webp`);
      } catch { /* файл уже удалён */ }
    }
  }

  delete media[key];
  await writeMedia(media);
  res.json({ ok: true, deletedFiles: deleted });
});

// GET /api/orphans — WebP в assets/img/ без записи в реестре
async function collectWebp(dir, baseDir, results = []) {
  let entries;
  try { entries = await fs.readdir(dir, { withFileTypes: true }); } catch { return results; }
  for (const entry of entries) {
    const full = path.join(dir, entry.name);
    if (entry.isDirectory()) await collectWebp(full, baseDir, results);
    else if (entry.name.endsWith('.webp')) {
      results.push(path.relative(baseDir, full).replace(/\\/g, '/'));
    }
  }
  return results;
}

app.get('/api/orphans', async (req, res) => {
  const media = await readMedia();
  const allWebp = await collectWebp(ASSETS_IMG_DIR, ASSETS_IMG_DIR);

  // Собираем все ожидаемые пути из реестра
  const expected = new Set();
  for (const [key, data] of Object.entries(media)) {
    for (const w of (data.widths || [])) {
      const rel = data.dir ? `${data.dir}/${key}-${w}.webp` : `${key}-${w}.webp`;
      expected.add(rel);
    }
  }

  const orphans = allWebp.filter(f => !expected.has(f));
  res.json({ count: orphans.length, files: orphans });
});

// DELETE /api/orphans
app.delete('/api/orphans', async (req, res) => {
  const media = await readMedia();
  const allWebp = await collectWebp(ASSETS_IMG_DIR, ASSETS_IMG_DIR);

  const expected = new Set();
  for (const [key, data] of Object.entries(media)) {
    for (const w of (data.widths || [])) {
      const rel = data.dir ? `${data.dir}/${key}-${w}.webp` : `${key}-${w}.webp`;
      expected.add(rel);
    }
  }

  const orphans = allWebp.filter(f => !expected.has(f));
  const deleted = [];
  for (const rel of orphans) {
    try {
      await fs.unlink(path.join(ASSETS_IMG_DIR, rel));
      deleted.push(rel);
    } catch { /* ignore */ }
  }
  res.json({ deleted: deleted.length, files: deleted });
});

// POST /api/process
app.post('/api/process', async (req, res) => {
  const media = await readMedia();
  const pending = Object.entries(media).filter(([, d]) => !d.generated);
  const logs = [];
  let ok = 0, fail = 0;
  for (const [key, data] of pending) {
    logs.push(`📸 ${key}`);
    try {
      media[key] = await processEntry(key, data, l => logs.push(l));
      ok++;
    } catch (err) {
      logs.push(`❌ ${err.message}`);
      fail++;
    }
  }
  await writeMedia(media);
  res.json({ ok, fail, logs });
});

app.listen(PORT, () => {
  console.log(`📸 Media UI: http://localhost:${PORT}`);
});
