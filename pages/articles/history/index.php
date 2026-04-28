<?php
$title = "История винтовых свай — от маяков Темзы до современного строительства";
$meta_description = "Кто изобрёл винтовые сваи: история слепого инженера Александра Митчелла, первый маяк Maplin Sands в 1838 году и как технология завоевала весь мир.";
$canonical = "https://zavodsvay.ru/articles/history/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';
