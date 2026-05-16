import fs from 'fs';
import path from 'path';

// Настройки путей (предполагается запуск из корня проекта)
const mapJsonPath = './data/map.json';
const objectsDir = './pages/objects';

async function run() {
    console.log(`Чтение данных из ${mapJsonPath}...`);

    if (!fs.existsSync(mapJsonPath)) {
        console.error(`[ERROR] Файл ${mapJsonPath} не найден! Запустите скрипт из корня проекта.`);
        return;
    }

    const rawData = fs.readFileSync(mapJsonPath, 'utf-8');
    let mapData;
    try {
        mapData = JSON.parse(rawData);
    } catch (e) {
        console.error(`[ERROR] Ошибка парсинга JSON:`, e.message);
        return;
    }

    console.log(`Найдено ${mapData.length} объектов. Начинаем генерацию...\n`);

    let created = 0;
    let skipped = 0;

    for (const obj of mapData) {
        if (!obj.id) {
            skipped++;
            continue;
        }

        const id = obj.id;
        const targetDir = path.join(objectsDir, String(id));
        const targetFile = path.join(targetDir, 'index.php');

        // Шаблон страницы объекта
        const phpContent = `<?php
$object_id  = ${id};
$object_dir = __DIR__;
require __DIR__ . '/../_template.php';
`;

        // Создаем директорию, если её нет
        if (!fs.existsSync(targetDir)) {
            fs.mkdirSync(targetDir, { recursive: true });
        }

        // Записываем файл
        fs.writeFileSync(targetFile, phpContent, 'utf-8');
        created++;
    }

    console.log(`\nГотово!`);
    console.log(`Сгенерировано/обновлено страниц: ${created}`);
    if (skipped > 0) console.log(`Пропущено объектов (нет id): ${skipped}`);
}

run();
