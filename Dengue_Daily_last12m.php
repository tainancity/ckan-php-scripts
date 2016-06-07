<?php

require __DIR__ . '/ckan/vendor/autoload.php';
$config = require __DIR__ . '/config.php';

$s = Guzzle\Service\Builder\ServiceBuilder::factory($config['sites'])->get('tainan');

/*
 * delete the datastore
 */
try {
    $r = $s->DatastoreDelete(array('data' => json_encode(array('resource_id' => $config['targets']['Dengue_Daily_last12m']['resource_id']))));
} catch (Guzzle\Http\Exception\BadResponseException $e) {
    print_r(json_decode($e->getResponse()->getBody(true)));
}

/*
 * create the datastore
 */
$data = array(
    'resource_id' => $config['targets']['Dengue_Daily_last12m']['resource_id'],
    'fields' => array(
        array('id' => '發病日', 'type' => 'date'),
        array('id' => '個案研判日', 'type' => 'date'),
        array('id' => '通報日', 'type' => 'date'),
        array('id' => '性別', 'type' => 'text'),
        array('id' => '年齡層', 'type' => 'text'),
        array('id' => '居住縣市', 'type' => 'text'),
        array('id' => '居住鄉鎮', 'type' => 'text'),
        array('id' => '居住村里', 'type' => 'text'),
        array('id' => '最小統計區', 'type' => 'text'),
        array('id' => '最小統計區中心點X', 'type' => 'float'),
        array('id' => '最小統計區中心點Y', 'type' => 'float'),
        array('id' => '一級統計區', 'type' => 'text'),
        array('id' => '二級統計區', 'type' => 'text'),
        array('id' => '感染縣市', 'type' => 'text'),
        array('id' => '感染鄉鎮', 'type' => 'text'),
        array('id' => '感染村里', 'type' => 'text'),
        array('id' => '是否境外移入', 'type' => 'text'),
        array('id' => '感染國家', 'type' => 'text'),
        array('id' => '確定病例數', 'type' => 'int'),
        array('id' => '居住村里代碼', 'type' => 'text'),
        array('id' => '感染村里代碼', 'type' => 'text')
    ),
);
try {
    $r = $s->DatastoreCreate(array('data' => json_encode($data)));
} catch (Guzzle\Http\Exception\BadResponseException $e) {
    print_r(json_decode($e->getResponse()->getBody(true)));
    exit();
}

/*
 * insert the data
 */
$baseData = array(
    'resource_id' => $config['targets']['Dengue_Daily_last12m']['resource_id'],
    'method' => 'insert',
    'records' => array(),
);
$data = $baseData;
file_put_contents(__DIR__ . '/Dengue_Daily_last12m.csv', file_get_contents('http://nidss.cdc.gov.tw/download/Dengue_Daily_last12m.csv'));
$fh = fopen(__DIR__ . '/Dengue_Daily_last12m.csv', 'r');
$headers = fgetcsv($fh, 2048);
$lineCount = $pushedNumber = 0;
while ($line = fgetcsv($fh, 2048)) {
    if ($line[5] === '台南市') {
        $record = array_combine($headers, $line);
        $record['最小統計區中心點X'] = floatval($record['最小統計區中心點X']);
        $record['最小統計區中心點Y'] = floatval($record['最小統計區中心點Y']);
        $record['確定病例數'] = intval($record['確定病例數']);
        $data['records'][] = $record;
        ++$lineCount;
        if ($lineCount === 100) {
            try {
                $r = $s->DatastoreInsert(array('data' => json_encode($data)));
            } catch (Guzzle\Http\Exception\BadResponseException $e) {
                print_r(json_decode($e->getResponse()->getBody(true)));
                exit();
            }
            
            $data = $baseData;
            $lineCount = 0;
            $pushedNumber += 100;
            error_log("pushed {$pushedNumber} records");
        }
    }
}
if ($lineCount > 0) {
    $r = $s->DatastoreInsert(array('data' => json_encode($data)));
    $data = $baseData;
    $pushedNumber += $lineCount;
}
error_log("pushed {$pushedNumber} records");

/*
 * update the resource
 */
$data = array(
    'id' => $config['targets']['Dengue_Daily_last12m']['resource_id'],
    'package_id' => 'denguefevercases',
    'url' => 'denguefevercases-recent',
    'extras0' => 'UTF-8',
    'extras3' => '每天',
    'extras4' => '臺南市',
    'extras5' => 'WGS84',
    'url_type' => 'datastore',
    'name' => '臺南市登革熱近12個月每日確定病例統計',
    'last_modified' => date('Y-m-d'),
    'format' => 'CSV',
    'description' => "主要欄位有發病日、個案研判日、通報日、性別、年齡層、居住縣市、居住鄉鎮、居住村里、最小統計區、最小統計區中心點X、最小統計區中心點Y、一級統計區、二級統計區、感染縣市、感染鄉鎮、感染村里、是否境外移入、感染國家、確定病例數\n\n資料更新日期： " . date('Y-m-d') . "\n資料筆數：{$pushedNumber}",
);
try {
    $r = $s->ResourceUpdate(array('data' => json_encode($data)));
} catch (Guzzle\Http\Exception\BadResponseException $e) {
    print_r(json_decode($e->getResponse()->getBody(true)));
    exit();
}

exit();

print_r($r);


//print_r($s->DatastoreSearch(array('resource_id' => '7617bfcd-20e2-4f8d-a83b-6f6b479367f9', 'limit' => '5')));
//print_r($s->GetUser(array('id' => 'kiang')));
//print_r($s->ResourceSearch(array('query' => 'qq')));

exit();


/*
 * to create a package
 */
$obj = new stdClass();
$obj->name = 'mynamenotexists1';
$obj->owner_org = 'geo';
$str = json_encode($obj);
$s->PackageCreate(array('data' => $str));
try {
    $r = $s->ResourceSearch(array('query' => 'qq'));
} catch (Guzzle\Http\Exception\BadResponseException $e) {
    print_r(json_decode($e->getResponse()->getBody(true)));
    exit();
}

/*
 * create the resource
 */
$data = array(
    'package_id' => 'denguefevercases',
    'url' => 'denguefevercases-recent',
    'extras0' => 'UTF-8',
    'extras3' => '每天',
    'extras4' => '臺南市',
    'url_type' => 'datastore',
    'name' => '臺南市登革熱近12個月每日確定病例統計',
    'description' => '主要欄位有發病日、個案研判日、通報日、性別、年齡層、居住縣市、居住鄉鎮、居住村里、最小統計區、最小統計區中心點X、最小統計區中心點Y、一級統計區、二級統計區、感染縣市、感染鄉鎮、感染村里、是否境外移入、感染國家、確定病例數',
);
try {
    $r = $s->ResourceCreate(array('data' => json_encode($data)));
} catch (Guzzle\Http\Exception\BadResponseException $e) {
    print_r(json_decode($e->getResponse()->getBody(true)));
    exit();
}
