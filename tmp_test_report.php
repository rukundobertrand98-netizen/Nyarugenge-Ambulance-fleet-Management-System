<?php
require 'vendor/autoload.php';
$path = 'C:\\xampp\\htdocs\\nyarugenge-ambulance-system\\storage\\app\\reports\\test-report.xlsx';
if (!is_dir(dirname($path))) {
    mkdir(dirname($path), 0777, true);
}
$writer = new OpenSpout\Writer\XLSX\Writer();
$writer->openToFile($path);
$writer->addRow(OpenSpout\Common\Entity\Row::fromValues(['hello', 'world']));
$writer->close();

echo 'size=' . filesize($path) . PHP_EOL;
$h = file_get_contents($path, false, null, 0, 4);
var_dump($h);
