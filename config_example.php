<?php

return array(
    'sites' => array(
        'ndc' => array(
            'class' => 'Silex\ckan\CkanClient',
            'params' => array(
                'baseUrl' => 'http://117.56.91.32/api/',
                'scheme' => 'http',
                'apiKey' => ''
            )
        ),
        'ckan-test' => array(
            'class' => 'Silex\ckan\CkanClient',
            'params' => array(
                'baseUrl' => '{scheme}://10.6.2.115/api/',
                'scheme' => 'http',
                'apiKey' => ''
            )
        ),
        'ckan-hi' => array(
            'class' => 'Silex\ckan\CkanClient',
            'params' => array(
                'baseUrl' => 'http://localhost/~kiang/ckan/test.php',
                'scheme' => 'http',
                'apiKey' => ''
            )
        ),
        'tainan' => array(
            'class' => 'Silex\ckan\CkanClient',
            'params' => array(
                'baseUrl' => '{scheme}://data.tainan.gov.tw/api/',
                'scheme' => 'http',
                'apiKey' => ''
            )
        ),
        'taoyuan' => array(
            'class' => 'Silex\ckan\CkanClient',
            'params' => array(
                'baseUrl' => '{scheme}://ckan.tycg.gov.tw/api/',
                'scheme' => 'http',
                'apiKey' => '',
            ),
        ),
        'nantou' => array(
            'class' => 'Silex\ckan\CkanClient',
            'params' => array(
                'baseUrl' => '{scheme}://data.nantou.gov.tw/api/',
                'scheme' => 'http',
                'apiKey' => '',
            ),
        ),
        'hccg' => array(
            'class' => 'Silex\ckan\CkanClient',
            'params' => array(
                'baseUrl' => '{scheme}://opendata.hccg.gov.tw/api/',
                'scheme' => 'http',
                'apiKey' => '',
            ),
        ),
        'taipei' => array(
            'class' => 'Silex\ckan\CkanClient',
            'params' => array(
                'baseUrl' => 'http://163.29.157.32:8080/api/',
                'scheme' => 'http',
                'apiKey' => '',
            ),
        ),
    ),
    'targets' => array(
        'Dengue_Daily_last12m' => array(
            'resource_id' => '',
        ),
        'MosIndex_All_last12m' => array(
            'resource_id' => '',
        ),
    ),
    'ndc' => array(
        'site' => 'tainan',
        'ckan_uri' => 'http://data.tainan.gov.tw',
        'sru' => 'http://data.nat.gov.tw/api/v1/rest',
        'key' => '',
        'base' => array(
            'categoryCode' => 'I00',
            'identifier' => '',
            'title' => '',
            'description' => '',
            'fieldDescription' => '',
            'type' => 'rawData',
            'issued' => '',
            'temporalCoverageFrom' => '',
            'temporalCoverageTo' => '',
            'accrualPeriodicity' => '每日',
            'modified' => '',
            'license' => '政府資料開放平臺資料使用規範',
            'licenseURL' => 'http://data.gov.tw/principle',
            'cost' => '免費',
            'costURL' => '',
            'costLaw' => '',
            'language' => 'zh-tw, zh-hant',
            'organization' => '臺南市政府',
            'organizationName' => '臺南市政府',
            'organizationContactName' => '',
            'organizationContactPhone' => '',
            'organizationContactEmail' => '',
            'publisher' => '臺南市政府',
            'publisherContactName' => '',
            'publisherContactPhone' => '',
            'publisherContactEmail' => '',
            'publisherOID' => '',
            'publisherOrgCode' => '',
            'spatial' => '台南,Tainan,台灣,Taiwan',
            'landingPage' => '',
            'numberOfData' => '',
            'keyword' => array('台南市'),
            'notes' => '',
            'distribution' => array(),
        )
    ),
);
