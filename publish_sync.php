<?php

require __DIR__ . '/ckan/vendor/autoload.php';
$config = require __DIR__ . '/config.php';

$tmpPath = __DIR__ . '/tmp/publis_sync';
if (!file_exists($tmpPath)) {
    mkdir($tmpPath, 0777, true);
}

$headers = array();
$headers[] = 'Content-Type:application/json';
$headers[] = 'Authorization: ' . $config['ndc']['key'];

$s = Guzzle\Service\Builder\ServiceBuilder::factory($config['sites'])->get('tainan');

$datasets = $s->GetDatasets()->getAll();

foreach ($datasets['result'] AS $datasetId) {
    $jsonDataset = $s->GetDataset(array('id' => $datasetId))->getAll();
    $pData = $config['ndc']['base'];
    $options = array();
    foreach ($jsonDataset['result']['extras'] AS $extra) {
        $options[$extra['key']] = $extra['value'];
    }
    $pData['identifier'] = $jsonDataset['result']['id'];
    $pData['title'] = $jsonDataset['result']['title'];
    $pData['description'] = $jsonDataset['result']['notes'];
    $pData['fieldDescription'] = isset($jsonDataset['result']['resources'][0]['description']) ? $jsonDataset['result']['resources'][0]['description'] : '';
    $pData['issued'] = $jsonDataset['result']['metadata_created'];
    $pData['temporalCoverageFrom'] = isset($options['收錄期間（起）']) ? $options['收錄期間（起）'] : '';
    $pData['temporalCoverageTo'] = isset($options['收錄期間（迄）']) ? $options['收錄期間（迄）'] : '';
    if (empty($pData['temporalCoverageFrom'])) {
        unset($pData['temporalCoverageFrom']);
    }
    if (empty($pData['temporalCoverageTo'])) {
        unset($pData['temporalCoverageTo']);
    }
    $pData['accrualPeriodicity'] = isset($options['更新頻率']) ? $options['更新頻率'] : '';
    $pData['modified'] = $jsonDataset['result']['metadata_modified'];
    $pData['publisher'] .= $jsonDataset['result']['organization']['title'];
    $pData['publisherContactName'] = !empty($jsonDataset['result']['maintainer']) ? $jsonDataset['result']['maintainer'] : $jsonDataset['result']['organization']['title'];
    $pData['publisherContactPhone'] = isset($options['提供機關聯絡人電話']) ? $options['提供機關聯絡人電話'] : '';
    $pData['landingPage'] = 'http://data.tainan.gov.tw/dataset/' . $jsonDataset['result']['name'];
    $pData['numberOfData'] = isset($options['資料量']) ? intval($options['資料量']) : '';
    $pData['keyword'][] = $jsonDataset['result']['organization']['title'];
    foreach ($jsonDataset['result']['tags'] AS $tag) {
        $pData['keyword'][] = $tag['name'];
    }

    foreach ($jsonDataset['result']['resources'] AS $resource) {
        $pData['distribution'][] = array(
            'resourceID' => $resource['id'],
            'resourceDescription' => $resource['description'],
            'resourceModified' => $resource['last_modified'],
            'format' => $resource['format'],
            'accessURL' => 'http://data.tainan.gov.tw/dataset/' . $jsonDataset['result']['name'] . '/resource/' . $resource['id'],
            'characterSetCode' => 'UTF-8'
        );
    }
    error_log("processing {$pData['title']}\n");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $config['ndc']['sru'] . '/dataset');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($pData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec($ch);
    curl_close($ch);
    $json = json_decode(substr($server_output, strpos($server_output, '{')), true);
    if (isset($json['error']['message']) && $json['error']['message'] === '欲新增的資料集已存在') {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $config['ndc']['sru'] . '/dataset/' . $pData['identifier']);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($pData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);
        curl_close($ch);
        $json = json_decode(substr($server_output, strpos($server_output, '{')), true);
    }

    print_r($json);

    file_put_contents($tmpPath . '/' . $jsonDataset['result']['name'] . '_' . $datasetId . '.json', json_encode(array(
        'result' => $json,
        'data' => $pData,
                    ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

exit();

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $config['ndc']['sru'] . '/dataset/tainan-' . $datasetId);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
echo curl_exec($ch);
curl_close($ch);
exit();

