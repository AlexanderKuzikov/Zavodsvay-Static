import fs from 'fs';

// Настройки (запуск из корня проекта)
const mapJsonPath = './data/map.json';
const sitemapPath = './sitemap.xml';
const BASE_URL = 'https://zavodsvay.ru';

// Статические страницы с приоритетами и частотой обновления
const staticPages = [
    { url: '/',           changefreq: 'weekly',  priority: '1.0' },
    { url: '/catalog/',   changefreq: 'weekly',  priority: '0.9' },
    { url: '/prices/',    changefreq: 'weekly',  priority: '0.9' },
    { url: '/calc/',      changefreq: 'monthly', priority: '0.8' },
    { url: '/montage/',   changefreq: 'monthly', priority: '0.8' },
    { url: '/articles/',  changefreq: 'weekly',  priority: '0.8' },
    { url: '/map/',       changefreq: 'monthly', priority: '0.7' },
    { url: '/contacts/',  changefreq: 'monthly', priority: '0.7' },
    { url: '/document/',  changefreq: 'monthly', priority: '0.6' },
];

function formatDate(date) {
    return date.toISOString().split('T')[0]; // YYYY-MM-DD
}

function makeLoc(url) {
    return `${BASE_URL}${url}`;
}

function makeUrl({ url, lastmod, changefreq, priority }) {
    return [
        '  <url>',
        `    <loc>${makeLoc(url)}</loc>`,
        lastmod    ? `    <lastmod>${lastmod}</lastmod>` : null,
        changefreq ? `    <changefreq>${changefreq}</changefreq>` : null,
        priority   ? `    <priority>${priority}</priority>` : null,
        '  </url>',
    ].filter(Boolean).join('\n');
}

async function run() {
    const today = formatDate(new Date());

    // Статические страницы
    const staticEntries = staticPages.map(p => makeUrl({ ...p, lastmod: today }));

    // Статьи из pages/articles/
    let articleEntries = [];
    const articlesDir = './pages/articles';
    if (fs.existsSync(articlesDir)) {
        const slugs = fs.readdirSync(articlesDir, { withFileTypes: true })
            .filter(d => d.isDirectory())
            .map(d => d.name);
        articleEntries = slugs.map(slug => makeUrl({
            url: `/articles/${slug}/`,
            lastmod: today,
            changefreq: 'monthly',
            priority: '0.7',
        }));
        console.log(`Статей: ${articleEntries.length}`);
    }

    // Object pages из map.json — только те, у кого есть url
    let objectEntries = [];
    if (fs.existsSync(mapJsonPath)) {
        const mapData = JSON.parse(fs.readFileSync(mapJsonPath, 'utf-8'));
        const withUrl = mapData.filter(o => o.url);
        objectEntries = withUrl.map(o => makeUrl({
            url: o.url,
            lastmod: today,
            changefreq: 'yearly',
            priority: '0.5',
        }));
        console.log(`Объектов: ${objectEntries.length}`);
    }

    const allEntries = [...staticEntries, ...articleEntries, ...objectEntries];

    const xml = [
        '<?xml version="1.0" encoding="UTF-8"?>',
        '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
        ...allEntries,
        '</urlset>',
    ].join('\n');

    fs.writeFileSync(sitemapPath, xml, 'utf-8');
    console.log(`\nГотово! sitemap.xml — ${allEntries.length} URL`);
}

run();
