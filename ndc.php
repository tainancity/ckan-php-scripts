<?php

require __DIR__ . '/ckan/vendor/autoload.php';
$config = require __DIR__ . '/config.php';

$s = Guzzle\Service\Builder\ServiceBuilder::factory($config['sites'])->get('tainan');
$t = Guzzle\Service\Builder\ServiceBuilder::factory($config['sites'])->get('ndc');

$datasets = $t->PackageSearch(array('q' => 'owner_org:c7765c06-7637-4a44-9f5b-34e528e009d6'))->getAll();
while ($datasets['result']['count'] != 0) {
    foreach ($datasets['result']['results'] AS $dataset) {
        try {
            $t->PackageDelete(array('data' => json_encode(array('id' => $dataset['id']))));
        } catch (Guzzle\Http\Exception\BadResponseException $e) {
            print_r(json_decode($e->getResponse()->getBody(true)));
            exit();
        }
    }
    $datasets = $t->PackageSearch(array('q' => 'owner_org:c7765c06-7637-4a44-9f5b-34e528e009d6'))->getAll();
}


$datasets = $s->GetDatasets()->getAll();
$base = array(
    'name' => '',
    'title' => '',
    'author' => '',
    'author_email' => '',
    'maintainer' => '',
    'maintainer_email' => '',
    'license_id' => '',
    'notes' => '',
    'url' => '',
    'version' => '',
    'state' => '',
    'type' => '',
    'resources' => array(),
    'tags' => array(),
    'owner_org' => '395000000a');

foreach ($datasets['result'] AS $datasetId) {
    $jsonDataset = $s->GetDataset(array('id' => $datasetId))->getAll();
    $pData = $base;
    $pData['name'] = 'tainan-data-' . $jsonDataset['result']['name'];
    $pData['title'] = $jsonDataset['result']['title'];
    $pData['author'] = $jsonDataset['result']['author'];
    $pData['author_email'] = $jsonDataset['result']['author_email'];
    $pData['maintainer'] = $jsonDataset['result']['maintainer'];
    $pData['maintainer_email'] = $jsonDataset['result']['maintainer_email'];
    $pData['license_id'] = $jsonDataset['result']['license_id'];
    $pData['notes'] = $jsonDataset['result']['notes'];
    $pData['url'] = 'http://data.tainan.gov.tw/dataset/' . $jsonDataset['result']['name'];
    $pData['version'] = $jsonDataset['result']['version'];
    $pData['state'] = $jsonDataset['result']['state'];
    $pData['type'] = $jsonDataset['result']['type'];
    foreach ($jsonDataset['result']['resources'] AS $resource) {
        $pData['resources'][] = array(
            'url' => 'http://data.tainan.gov.tw/dataset/' . $jsonDataset['result']['name'] . '/resource/' . $resource['id'],
            'description' => $resource['description'],
            'format' => $resource['format'],
            'hash' => $resource['hash'],
            'name' => $resource['name'],
            'resource_type' => $resource['resource_type'],
            'mimetype' => $resource['mimetype'],
            'mimetype_inner' => $resource['mimetype_inner'],
            'webstore_url' => $resource['webstore_url'],
            'cache_url' => $resource['cache_url'],
            'size' => $resource['size'],
            'created' => $resource['created'],
            'last_modified' => $resource['last_modified'],
            'cache_last_updated' => $resource['cache_last_updated'],
            'webstore_last_updated' => $resource['webstore_last_updated'],
        );
    }
    $pData['tags'] = array(array('name' => '台南市'), array('name' => $jsonDataset['result']['organization']['title']));
    try {
        $t->PackageCreate(array('data' => json_encode($pData)));
    } catch (Guzzle\Http\Exception\BadResponseException $e) {
        print_r(json_decode($e->getResponse()->getBody(true)));
        exit();
    }
    error_log($pData['title'] . ' done');
}