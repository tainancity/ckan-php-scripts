<?php

require __DIR__ . '/ckan/vendor/autoload.php';
$config = require __DIR__ . '/config.php';

$s = Guzzle\Service\Builder\ServiceBuilder::factory($config['sites'])->get('tainan');

/*
 * delete the datastore
 */
try {
    $r = $s->DatastoreDelete(array('data' => json_encode(array('resource_id' => $config['targets']['MosIndex_All_last12m']['resource_id']))));
} catch (Guzzle\Http\Exception\BadResponseException $e) {
    print_r(json_decode($e->getResponse()->getBody(true)));
}

/*
 * create the datastore
 * Date,County,Town,Village,VillageID,VillageLon,VillageLat,BI,BILv,HI,HILv,CI,CILv,LI,LILv,AI,Con100HH
 */
$resource = array(
    'resource_id' => $config['targets']['MosIndex_All_last12m']['resource_id'],
    'fields' => array(
        array('id' => '調查日期', 'type' => 'date'),
        array('id' => '縣市', 'type' => 'text'),
        array('id' => '鄉鎮市區', 'type' => 'text'),
        array('id' => '村里', 'type' => 'text'),
        array('id' => '村里代碼', 'type' => 'text'),
        array('id' => '村里中心點經度', 'type' => 'float'),
        array('id' => '村里中心點緯度', 'type' => 'float'),
        array('id' => '布氏指數', 'type' => 'float'),
        array('id' => '布氏級數', 'type' => 'int'),
        array('id' => '住宅指數', 'type' => 'float'),
        array('id' => '住宅級數', 'type' => 'int'),
        array('id' => '容器指數', 'type' => 'float'),
        array('id' => '容器級數', 'type' => 'int'),
        array('id' => '幼蟲指數', 'type' => 'float'),
        array('id' => '幼蟲級數', 'type' => 'int'),
        array('id' => '成蟲指數', 'type' => 'float'),
        array('id' => '每百戶積水容器數', 'type' => 'float'),
    ),
);
try {
    $r = $s->DatastoreCreate(array('data' => json_encode($resource)));
} catch (Guzzle\Http\Exception\BadResponseException $e) {
    print_r(json_decode($e->getResponse()->getBody(true)));
    exit();
}

/*
 * insert the data
 */
$baseData = array(
    'resource_id' => $config['targets']['MosIndex_All_last12m']['resource_id'],
    'method' => 'insert',
    'records' => array(),
);
$data = $baseData;
file_put_contents(__DIR__ . '/MosIndex_All_last12m.csv', file_get_contents('http://nidss.cdc.gov.tw/download/MosIndex/MosIndex_Tainan_last12m.csv'));
$fh = fopen(__DIR__ . '/MosIndex_All_last12m.csv', 'r');
fgetcsv($fh, 2048);
$headers = array();
foreach ($resource['fields'] AS $field) {
    $headers[] = $field['id'];
}
$lineCount = $pushedNumber = 0;
while ($line = fgetcsv($fh, 2048)) {
    foreach ($resource['fields'] AS $k => $field) {
        switch ($field['type']) {
            case 'float':
                $line[$k] = floatval($line[$k]);
                break;
            case 'int':
                $line[$k] = intval($line[$k]);
                break;
        }
    }
    $data['records'][] = array_combine($headers, $line);
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
    'id' => $config['targets']['MosIndex_All_last12m']['resource_id'],
    'package_id' => 'df-mosquito-density',
    'url' => 'dengue-mosindex-recent',
    'extras0' => 'UTF-8',
    'extras3' => '每天',
    'extras4' => '臺南市',
    'extras5' => 'WGS84',
    'url_type' => 'datastore',
    'name' => '臺南市登革熱近12個月登革熱病媒蚊調查資料',
    'last_modified' => date('Y-m-d'),
    'format' => 'CSV',
    'description' => "包含調查日期、縣市、鄉鎮市區、村里、村里中心點、調查地區分類、調查戶數、調查人員種類、病媒蚊調查資訊及經計算之指數與級數\n\n資料更新日期： " . date('Y-m-d') . "\n資料筆數：{$pushedNumber}",
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
    'package_id' => 'df-mosquito-density',
    'url' => 'dengue-mosindex-recent',
    'extras0' => 'UTF-8',
    'extras3' => '每天',
    'extras4' => '臺南市',
    'url_type' => 'datastore',
    'name' => '臺南市登革熱近12個月登革熱病媒蚊調查資料',
    'description' => '包含調查日期、縣市、鄉鎮市區、村里、村里中心點、調查地區分類、調查戶數、調查人員種類、病媒蚊調查資訊及經計算之指數與級數',
);
try {
    $r = $s->ResourceCreate(array('data' => json_encode($data)));
} catch (Guzzle\Http\Exception\BadResponseException $e) {
    print_r(json_decode($e->getResponse()->getBody(true)));
    exit();
}
