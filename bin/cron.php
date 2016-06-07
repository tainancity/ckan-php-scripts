<?php

$rootPath = dirname(__DIR__);
$now = date('Y-m-d H:i:s');

exec("cd {$rootPath} && /usr/bin/git pull");

exec("/usr/bin/php -q {$rootPath}/Dengue_Daily_last12m.php");
//exec("/usr/bin/php -q {$rootPath}/datasets.php");
//exec("/usr/bin/php -q {$rootPath}/datasets_taipei.php");
exec("/usr/bin/php -q {$rootPath}/MosIndex_All_last12m.php");

exec("cd {$rootPath} && /usr/bin/git add -A");

exec("cd {$rootPath} && /usr/bin/git commit --author 'auto commit <noreply@localhost>' -m 'auto update @ {$now}'");

exec("cd {$rootPath} && /usr/bin/git push origin master");
