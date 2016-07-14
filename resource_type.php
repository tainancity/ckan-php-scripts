<?php

require __DIR__ . '/ckan/vendor/autoload.php';
$config = require __DIR__ . '/config.php';
$target = 'tainan';

$s = Guzzle\Service\Builder\ServiceBuilder::factory($config['sites'])->get($target);

$datasets = $s->GetDatasets()->getAll();

$fh = fopen(__DIR__ . '/datasets/' . $target . '_resource.csv', 'w');

fputcsv($fh, array(
    'format', 'organization', 'dataset name', 'dataset title', 'resource id', 'resource name',
));

foreach ($datasets['result'] AS $datasetId) {
    $jsonDataset = $s->GetDataset(array('id' => $datasetId))->getAll();
    foreach ($jsonDataset['result']['resources'] AS $resource) {
        fputcsv($fh, array(
            $resource['format'],
            $jsonDataset['result']['organization']['title'],
            $jsonDataset['result']['name'],
            $jsonDataset['result']['title'],
            $resource['id'],
            $resource['name'],
        ));
    }
}