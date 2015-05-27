<?php

//require_once 'db.interface.php';
require_once 'db_mysql.class.php';

$dbc = new db_mysql($GLOBALS['db_conf']);

$c = $dbc->getManyNews('1,2');
print_r($c);
echo $c[99];


/* $r = mysql_query('select * from wx_reply where id in (1,2,3,4) ;',$dbc->wlink);


var_dump($r);

$count = 0;

while($c[] = mysql_fetch_assoc($r)){
$count+=1;


}

unset($c[$count]);
print_r($c); */