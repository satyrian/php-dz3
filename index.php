<?php
function checkGrammar($string) {
    $regExp = [
        "/[а-яёА-ЯЁ]*(жы|шы)[а-яё]*/ui" => "жи/ши пиши через И",
        "/координально/ui" => "правильно писать кардинально",
        "/сдесь/ui" => "не \"сдесь\", а \"здесь\"",
        "/\bздела[летюни]{1,2}/ui" => "приставки \"з\" в русском языке нет",
        "/[а-яёА-ЯЁ0-9]+\s+а\s+/ui" => "пропущена запятая перед а",
        "/[а-яёА-ЯЁ0-9]+\s+но\s+/ui" => "пропущена запятая перед но",
        "/[.,!?;:]+[^\s.,!?;:\"]+/ui" => "пропущен пробел после знака препинания",
    ];
    $matches = [];
    $errorsMessages = [];

    foreach ($regExp as $pattern => $message) {
        if (preg_match_all($pattern, $string, $matches)) {
            foreach ($matches[0] as $word) {
                $stringPos = mb_strpos($string, $word);
                $stringBefore = mb_substr($string, $stringPos - 30, 30);
                $stringAfter = mb_substr($string, $stringPos + mb_strlen($word), 30);
                $errorsMessages[] = "Допущена ошибка: ...$stringBefore [$word] $stringAfter... ($message)";
            }
        }
    }

    if (empty($errorsMessages))
        return false;

    return $errorsMessages;
}

function fixGrammar($string) {
    $regExp = [
        "regExp" => [
            "/([Жж]|[Шш])ы/ui",
            "/координально/ui",
            "/сдесь/ui",
            "/\b(здела)([летюни]{1,2})/ui",
            "/([а-яёА-ЯЁ0-9]+)\s+(а|но)\s+/ui",
            "/([.,!?;:]+)([^\s.,!?;:\"])/ui"
        ],
        "replacement" => [
            "\$1и",
            "кардинально",
            "здесь",
            "сдела\$2",
            "\$1, \$2 ",
            "\$1 \$2"
        ]
    ];
    $fixString = preg_replace($regExp["regExp"], $regExp["replacement"], $string);

    return "Исправленный текст:\n$fixString";
}

$text = "Давно выяснено, что при оценке дизайна и композиции читаемый текст мешает сосредоточиться.Lorem Ipsum используют потому, что тот обеспечивает более или менее стандартное заполнение шаблона а также реальное распределение букв и пробелов в абзацах, которое не получается при простой дубликации \"Здесь ваш текст..Здесь ваш текст.. Сдесь ваш текст..\" Многие программы электронной вёрстки и редакторы HTML используют Lorem Ipsum в качестве текста по умолчанию, так что поиск по ключевым словам \"lorem ipsum\" сразу показывает, как много веб-страниц всё ещё дожыдаются своего настоящего рождения. За прошедшие годы текст Lorem Ipsum получил много версий. Некоторые версии появились по ошибке, некоторые - зделали намеренно (например, юмористические варианты).";

if ($errors = checkGrammar($text)) {
    foreach ($errors as $error) {
        echo "$error\n";
    }
    echo fixGrammar($text);
} else {
    echo "Ошибок не найдено\n";
}
