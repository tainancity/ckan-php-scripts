<?php

$fh = fopen(__DIR__ . '/datasets/tainan_resource.csv', 'r');
fgetcsv($fh, 2048);
$result = array();
while ($line = fgetcsv($fh, 2048)) {
    switch (strtolower($line[0])) {
        case 'doc':
        case 'html':
        case 'docx':
        case 'xls':
        case 'pdf':
        case '.xlsx':
        case 'link':
        case 'xlsx':
        case '網址':
        case 'zip':
        case '.doc':
        case 'hyperlink':
        case 'excel':
        case 'word':
        case 'dwg':
            if(!isset($result[$line[1]])) {
                $result[$line[1]] = array();
            }
            $result[$line[1]][] = "[{$line[0]}]{$line[3]} > {$line[5]} - http://data.tainan.gov.tw/dataset/{$line[2]}/resource/{$line[4]}";
            break;
    }
}

foreach($result AS $org => $links) {
    echo "\n\n{$org}\n\n * ";
    echo implode("\n * ", $links);
}