#!/usr/bin/env node
/**
 * FTP Deploy Utility
 * Usage (from tools/ directory):
 *   npm run deploy       — upload changed files since last deploy
 *   npm run deploy:dry   — dry-run, show what would be uploaded/deleted
 *   npm run deploy:full  — upload ALL deployable files (first run or full restore)
 *
 * Config: tools/.env (never committed)
 */

import * as ftp from 'basic-ftp';
import { execSync } from 'child_process';
import { existsSync, readFileSync, writeFileSync, readdirSync, statSync } from 'fs';
import { resolve, dirname, relative } from 'path';
import { fileURLToPath } from 'url';
import { createInterface } from 'readline';
import { config } from 'dotenv';

const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT = resolve(__dirname, '..');
const STATE_FILE = resolve(__dirname, '.last-deploy');
const ENV_FILE = resolve(__dirname, '.env');

config({ path: ENV_FILE });

const FTP_HOST = process.env.FTP_HOST;
const FTP_USER = process.env.FTP_USER;
const FTP_PASS = process.env.FTP_PASS;
const FTP_REMOTE_DIR = process.env.FTP_REMOTE_DIR || '/';

const EXCLUDE = [
  '.git',
  '.github',
  '.gitignore',
  'source',
  'tools',
  'node_modules',
  'README.md',
  'CONTEXT.md',
  'nginx.conf',
  'var_domain.record.csv',
];

const isDryRun = process.argv.includes('--dry-run');
const isFull = process.argv.includes('--full');

function isExcluded(filePath) {
  const top = filePath.split('/')[0];
  return EXCLUDE.includes(top) || EXCLUDE.includes(filePath);
}

function getAllFilesSync(dir = ROOT, base = ROOT) {
  const results = [];
  for (const entry of readdirSync(dir)) {
    const fullPath = resolve(dir, entry);
    const relPath = relative(base, fullPath).replace(/\\/g, '/');
    if (isExcluded(relPath.split('/')[0])) continue;
    if (statSync(fullPath).isDirectory()) {
      results.push(...getAllFilesSync(fullPath, base));
    } else {
      results.push(relPath);
    }
  }
  return results;
}

function getChangedFiles(sinceSha) {
  try {
    const upload = execSync(
      `git diff ${sinceSha} HEAD --name-only --diff-filter=ACMR`,
      { cwd: ROOT, encoding: 'utf8' }
    ).trim().split('\n').filter(Boolean).filter(f => !isExcluded(f));

    const del = execSync(
      `git diff ${sinceSha} HEAD --name-only --diff-filter=D`,
      { cwd: ROOT, encoding: 'utf8' }
    ).trim().split('\n').filter(Boolean).filter(f => !isExcluded(f));

    return { upload, delete: del };
  } catch {
    return { upload: [], delete: [] };
  }
}

function getCurrentSha() {
  return execSync('git rev-parse HEAD', { cwd: ROOT, encoding: 'utf8' }).trim();
}

function getLastDeployedSha() {
  if (!existsSync(STATE_FILE)) return null;
  return readFileSync(STATE_FILE, 'utf8').trim();
}

function saveDeployedSha(sha) {
  writeFileSync(STATE_FILE, sha, 'utf8');
}

async function confirm(message) {
  const rl = createInterface({ input: process.stdin, output: process.stdout });
  return new Promise(res => {
    rl.question(`${message} [y/n] `, answer => {
      rl.close();
      res(answer.toLowerCase() === 'y');
    });
  });
}

async function main() {
  if (!FTP_HOST || !FTP_USER || !FTP_PASS) {
    console.error('\n❌ Не найден tools/.env или не заданы FTP_HOST, FTP_USER, FTP_PASS');
    console.error('Создай файл tools/.env по образцу tools/.env.example\n');
    process.exit(1);
  }

  const currentSha = getCurrentSha();
  const lastSha = getLastDeployedSha();

  let filesToUpload = [];
  let filesToDelete = [];

  if (isFull || !lastSha) {
    console.log(!lastSha
      ? '\nℹ️  .last-deploy не найден — выполняется полный деплой'
      : '\nℹ️  Режим полного деплоя (--full)'
    );
    filesToUpload = getAllFilesSync();
  } else {
    const diff = getChangedFiles(lastSha);
    filesToUpload = diff.upload;
    filesToDelete = diff.delete;
  }

  if (filesToUpload.length === 0 && filesToDelete.length === 0) {
    console.log('\n✅ Нет изменений для деплоя.\n');
    process.exit(0);
  }

  console.log(`\n📦 Деплой: ${currentSha.slice(0, 7)}`);
  if (lastSha) console.log(`   Предыдущий: ${lastSha.slice(0, 7)}`);
  console.log(`   Remote: ${FTP_HOST}${FTP_REMOTE_DIR}\n`);

  if (filesToUpload.length > 0) {
    console.log(`📤 Будет загружено (${filesToUpload.length}):`);
    filesToUpload.forEach(f => console.log(`   + ${f}`));
  }

  if (filesToDelete.length > 0) {
    console.log(`\n🗑️  Будет удалено (${filesToDelete.length}):`);
    filesToDelete.forEach(f => console.log(`   - ${f}`));
  }

  if (isDryRun) {
    console.log('\n🔍 Dry-run завершён. Реальных изменений не было.\n');
    process.exit(0);
  }

  const ok = await confirm('\nПродолжить деплой?');
  if (!ok) {
    console.log('Отменено.\n');
    process.exit(0);
  }

  const client = new ftp.Client();
  client.ftp.verbose = false;

  try {
    await client.access({
      host: FTP_HOST,
      user: FTP_USER,
      password: FTP_PASS,
      secure: false,
    });

    console.log('\n🔗 Подключено к FTP\n');

    for (const file of filesToUpload) {
      const localPath = resolve(ROOT, file);
      const remotePath = FTP_REMOTE_DIR + file;
      const remoteDir = remotePath.substring(0, remotePath.lastIndexOf('/'));
      try {
        await client.ensureDir(remoteDir);
        await client.uploadFrom(localPath, remotePath);
        console.log(`✅ ${file}`);
      } catch (err) {
        console.error(`❌ ${file}: ${err.message}`);
      }
    }

    for (const file of filesToDelete) {
      const remotePath = FTP_REMOTE_DIR + file;
      try {
        await client.remove(remotePath);
        console.log(`🗑️  Удалён: ${file}`);
      } catch {
        // файл уже не существует — игнорируем
      }
    }

    saveDeployedSha(currentSha);
    console.log(`\n🎉 Деплой завершён. SHA сохранён: ${currentSha.slice(0, 7)}\n`);

  } catch (err) {
    console.error(`\n❌ Ошибка FTP: ${err.message}\n`);
    process.exit(1);
  } finally {
    client.close();
  }
}

main();
