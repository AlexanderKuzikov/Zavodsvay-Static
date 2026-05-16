import fs from 'fs';
import path from 'path';

// Настройки путей (запуск из корня проекта)
const mapJsonPath = './data/map.json';
const imagesDir = './pages/map/img';

async function run() {
    console.log(`Сканирование директории изображений ${imagesDir}...`);
    if (!fs.existsSync(imagesDir)) {
        console.error(`[ERROR] Директория ${imagesDir} не найдена.`);
        return;
    }

    const files = fs.readdirSync(imagesDir);
    const webpFiles = files.filter(f => f.toLowerCase().endsWith('.webp'));

    // Группируем картинки по ID объекта
    const imagesById = {};
    for (const file of webpFiles) {
        // Ожидаемый формат: {id}_{index}.webp, например 3_1.webp
        const match = file.match(/^(\d+)_/);
        if (match) {
            const id = parseInt(match[1], 10);
            if (!imagesById[id]) {
                imagesById[id] = [];
            }
            imagesById[id].push(file);
        }
    }

    // Сортируем массивы картинок натуральной сортировкой 
    // (чтобы 10_10.webp шел после 10_2.webp, а не после 10_1.webp)
    for (const id in imagesById) {
        imagesById[id].sort((a, b) => a.localeCompare(b, undefined, { numeric: true }));
    }

    console.log(`Найдено картинок: ${webpFiles.length}, они относятся к ${Object.keys(imagesById).length} уникальным объектам.\n`);

    console.log(`Чтение ${mapJsonPath}...`);
    const rawData = fs.readFileSync(mapJsonPath, 'utf-8');
    let mapData;
    try {
        mapData = JSON.parse(rawData);
    } catch (e) {
        console.error(`[ERROR] Ошибка парсинга JSON:`, e.message);
        return;
    }

    let updatedCount = 0;
    let withImagesCount = 0;

    for (const obj of mapData) {
        if (!obj.id) continue;

        // 1. Прописываем/обновляем URL страницы объекта
        obj.url = `/objects/${obj.id}/`;

        // 2. Привязываем массив картинок
        const objImages = imagesById[obj.id];
        if (objImages && objImages.length > 0) {
            obj.images = objImages;
            withImagesCount++;
        } else {
            // Если картинок нет, оставляем пустой массив, 
            // чтобы PHP-шаблон мог сделать if (!empty($object['images']))
            obj.images = [];
        }

        updatedCount++;
    }

    // Сохраняем обратно с форматированием (2 пробела)
    fs.writeFileSync(mapJsonPath, JSON.stringify(mapData, null, 2), 'utf-8');

    console.log(`Готово!`);
    console.log(`Объектов обработано: ${updatedCount}`);
    console.log(`Объектов с картинками: ${withImagesCount}`);
}

run();
