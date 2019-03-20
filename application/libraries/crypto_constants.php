<?php
define("CRYPTO_MODULE_BASE_URL", "http://localhost:8090");
define("SC_ACCESS_KEY", "ZZZZZZZZZZZZZZZZ");

$user_map['ADAKDASJDMFASLDFIEWZDFFV'] = 101;
$user_map['BEXAJLAJSDFJKASFEIIIDDQD'] = 102;
$user_map['CASDIFASDMEAEVNZZAKDKDKD'] = 103;

$invoice_id = 'adkfjakjeiwonakadfadjfoapixnejoi';
$invoice_map[$invoice_id]['user'] = 101;
$invoice_map[$invoice_id]['amount'] = 10;
$invoice_map[$invoice_id]['coin'] = "BTC";
$invoice_map[$invoice_id]['client'] = 501;

$invoice_id = 'bckfjakjeiwonakadfadjfoapixnejg5';
$invoice_map[$invoice_id]['user'] = 102;
$invoice_map[$invoice_id]['amount'] = 11;
$invoice_map[$invoice_id]['coin'] = "ETH";
$invoice_map[$invoice_id]['client'] = 502;

$invoice_id = 'crbcjakjeiwonakadfadjfoapixnet4d';
$invoice_map[$invoice_id]['user'] = 103;
$invoice_map[$invoice_id]['amount'] = 12;
$invoice_map[$invoice_id]['coin'] = "LTC";
$invoice_map[$invoice_id]['client'] = 503;


$name_map[101] = 'Maria';
$name_map[102] = 'Stan';
$name_map[103] = 'Bob';
