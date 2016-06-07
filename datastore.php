<?php

require __DIR__ . '/ckan/vendor/autoload.php';
$config = require __DIR__ . '/config.php';
$targets = array(
    'tainan' => 'http://data.tainan.gov.tw/dataset/',
    'taoyuan' => 'http://ckan.tycg.gov.tw/dataset/',
    'nantou' => 'http://data.nantou.gov.tw/dataset/',
    'taipei' => 'http://163.29.157.32:8080/dataset/',
);

foreach ($targets AS $target => $baseUrl) {
    $s = Guzzle\Service\Builder\ServiceBuilder::factory($config['sites'])->get($target);

    $json = json_decode(file_get_contents(__DIR__ . '/datasets/' . $target . '.json'), true);

    $fh = fopen(__DIR__ . '/datastore/' . $target . '.csv', 'w');
    fputcsv($fh, array('資料集', '資源', '網址', '欄位名稱', '資料數量'));
    foreach ($json AS $k => $datasets) {
        if ($k === 'time_generated') {
            continue;
        }
        foreach ($datasets['datasets'] AS $dataset) {
            foreach ($dataset['resources'] AS $resource) {
                try {
                    $r = $s->DatastoreSearch(array('resource_id' => $resource['id'], 'limit' => 1));
                } catch (Guzzle\Http\Exception\BadResponseException $e) {
                    //print_r(json_decode($e->getResponse()->getBody(true)));
                    continue;
                }
                if (is_array($r)) {
                    $fields = array();
                    foreach ($r['result']['fields'] AS $f) {
                        $fields[] = preg_replace('/\s+/', '_', $f['id']);
                    }
                    fputcsv($fh, array($dataset['title'], $resource['name'], "{$baseUrl}{$dataset['name']}/resource/{$resource['id']}", implode(',', $fields), isset($r['result']['total']) ? $r['result']['total'] : 0));
                }
            }
        }
    }
    fclose($fh);
}

