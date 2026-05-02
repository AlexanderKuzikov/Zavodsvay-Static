/**
 * server.js — локальный Express-сервер медиабиблиотеки
 *
 * Запуск из папки tools/:
 *   node server.js
 *
 * UI доступен по адресу: http://localhost:3000
 *
 * API:
 *   GET  /api/media          — весь реестр media.json
 *   GET  /api/scan           — сканирует source/, регистрирует новые
 *   POST /api/media/:key     — обновляет alt/caption, если regenerate=true → нарезает
 *   POST /api/process        — нарезает все с generated:false
 *   GET  /api/thumb/:key     — темп превью (200px) из source/
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
const PORT = 3000;
const app = express();

app.use(express.json());
app.use(express.static(path.join(process.cwd(), 'ui')));

// Отдаём нарезанные ассеты для превью в UI
app.use('/assets', express.static(path.join(ROOT_DIR, 'assets')));

// GET /api/media
app.get('/api/media', async (req, res) => {
  const media = await readMedia();
  res.json(media);
});

// GET /api/scan — находит новые файлы в source/, добавляет их в media.json
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

// GET /api/thumb/:key — превью 200px из оригинала
app.get('/api/thumb/:key', async (req, res) => {
  const media = await readMedia();
  const data = media[req.params.key];
  if (!data) return res.status(404).send('Not found');
  const srcPath = path.join(ROOT_DIR, data.file);
  try {
    const buf = await sharp(srcPath).resize({ width: 200 }).webp({ quality: 70 }).toBuffer();
    res.set('Content-Type', 'image/webp').send(buf);
  } catch {
    res.status(500).send('Error generating thumb');
  }
});

// POST /api/media/:key — сохранить alt/caption, опционально перегенерировать
app.post('/api/media/:key', async (req, res) => {
  const { key } = req.params;
  const { alt, caption, regenerate } = req.body;
  const media = await readMedia();
  if (!media[key]) return res.status(404).json({ error: 'Key not found' });

  media[key].alt = alt ?? media[key].alt;
  media[key].caption = caption ?? media[key].caption;

  if (regenerate) {
    media[key].generated = false;
  }

  const logs = [];
  if (regenerate || !media[key].generated) {
    try {
      media[key] = await processEntry(key, media[key], (l) => logs.push(l));
    } catch (err) {
      return res.status(500).json({ error: err.message, logs });
    }
  }

  await writeMedia(media);
  res.json({ ok: true, entry: media[key], logs });
});

// POST /api/process — нарезать всё с generated:false
app.post('/api/process', async (req, res) => {
  const media = await readMedia();
  const pending = Object.entries(media).filter(([, d]) => !d.generated);
  const logs = [];
  let ok = 0, fail = 0;

  for (const [key, data] of pending) {
    logs.push(`📸 ${key}`);
    try {
      media[key] = await processEntry(key, data, (l) => logs.push(l));
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
