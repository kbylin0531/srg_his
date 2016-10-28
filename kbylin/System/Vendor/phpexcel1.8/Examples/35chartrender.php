<<<<<<< HEAD
<?php

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

date_default_timezone_set('Europe/London');

/**
 * PHPExcel
 *
 * Copyright (C) 2006 - 2014 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.8.0, 2014-03-02
 */

/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/../Classes/');

/** PHPExcel_IOFactory */
include 'PHPExcel/IOFactory.php';


//	Change these values to select the Rendering library that you wish to use
//		and its directory location on your server
$rendererName = PHPExcel_Settings::CHART_RENDERER_JPGRAPH;
$rendererLibrary = 'jpgraph3.5.0b1/src';
$rendererLibraryPath = '/php/libraries/Charts/' . $rendererLibrary;


if (!PHPExcel_Settings::setChartRenderer(
		$rendererName,
		$rendererLibraryPath
	)) {
	die(
		'NOTICE: Please set the $rendererName and $rendererLibraryPath values' .
		EOL .
		'at the top of this script as appropriate for your directory structure'
	);
}


$inputFileType = 'Excel2007';
$inputFileNames = 'templates/32readwrite*[0-9].xlsx';

	if ((isset($argc)) && ($argc > 1)) {
	$inputFileNames = array();
	for($i = 1; $i < $argc; ++$i) {
		$inputFileNames[] = dirname(__FILE__) . '/templates/' . $argv[$i];
	}
} else {
	$inputFileNames = glob($inputFileNames);
}
foreach($inputFileNames as $inputFileName) {
	$inputFileNameShort = basename($inputFileName);

	if (!file_exists($inputFileName)) {
		echo date('H:i:s') , " File " , $inputFileNameShort , ' does not exist' , EOL;
		continue;
	}

	echo date('H:i:s') , " Load Test from $inputFileType file " , $inputFileNameShort , EOL;

	$objReader = PHPExcel_IOFactory::createReader($inputFileType);
	$objReader->setIncludeCharts(TRUE);
	$objPHPExcel = $objReader->load($inputFileName);


	echo date('H:i:s') , " Iterate worksheets looking at the charts" , EOL;
	foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
		$sheetName = $worksheet->getTitle();
		echo 'Worksheet: ' , $sheetName , EOL;

		$chartNames = $worksheet->getChartNames();
		if(empty($chartNames)) {
			echo '    There are no charts in this worksheet' , EOL;
		} else {
			natsort($chartNames);
			foreach($chartNames as $i => $chartName) {
				$chart = $worksheet->getChartByName($chartName);
				if (!is_null($chart->getTitle())) {
					$caption = '"' . implode(' ',$chart->getTitle()->getCaption()) . '"';
				} else {
					$caption = 'Untitled';
				}
				echo '    ' , $chartName , ' - ' , $caption , EOL;
				echo str_repeat(' ',strlen($chartName)+3);

				$jpegFile = '35'.str_replace('.xlsx', '.jpg', substr($inputFileNameShort,2));
				if (file_exists($jpegFile)) {
					unlink($jpegFile);
				}
				try {
					$chart->render($jpegFile);
				} catch (Exception $e) {
					echo 'Error rendering chart: ',$e->getMessage();
				}
			}
		}
	}


	$objPHPExcel->disconnectWorksheets();
	unset($objPHPExcel);
}

// Echo memory peak usage
echo date('H:i:s') , ' Peak memory usage: ' , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;

// Echo done
echo date('H:i:s') , " Done rendering charts as images" , EOL;
echo 'Image files have been created in ' , getcwd() , EOL;
=======
<?php

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

date_default_timezone_set('Europe/London');

/**
 * PHPExcel
 *
 * Copyright (C) 2006 - 2014 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.8.0, 2014-03-02
 */

/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/../Classes/');

/** PHPExcel_IOFactory */
include 'PHPExcel/IOFactory.php';


//	Change these values to select the Rendering library that you wish to use
//		and its directory location on your server
$rendererName = PHPExcel_Settings::CHART_RENDERER_JPGRAPH;
$rendererLibrary = 'jpgraph3.5.0b1/src';
$rendererLibraryPath = '/php/libraries/Charts/' . $rendererLibrary;


if (!PHPExcel_Settings::setChartRenderer(
		$rendererName,
		$rendererLibraryPath
	)) {
	die(
		'NOTICE: Please set the $rendererName and $rendererLibraryPath values' .
		EOL .
		'at the top of this script as appropriate for your directory structure'
	);
}


$inputFileType = 'Excel2007';
$inputFileNames = 'templates/32readwrite*[0-9].xlsx';

	if ((isset($argc)) && ($argc > 1)) {
	$inputFileNames = array();
	for($i = 1; $i < $argc; ++$i) {
		$inputFileNames[] = dirname(__FILE__) . '/templates/' . $argv[$i];
	}
} else {
	$inputFileNames = glob($inputFileNames);
}
foreach($inputFileNames as $inputFileName) {
	$inputFileNameShort = basename($inputFileName);

	if (!file_exists($inputFileName)) {
		echo date('H:i:s') , " File " , $inputFileNameShort , ' does not exist' , EOL;
		continue;
	}

	echo date('H:i:s') , " Load Test from $inputFileType file " , $inputFileNameShort , EOL;

	$objReader = PHPExcel_IOFactory::createReader($inputFileType);
	$objReader->setIncludeCharts(TRUE);
	$objPHPExcel = $objReader->load($inputFileName);


	echo date('H:i:s') , " Iterate worksheets looking at the charts" , EOL;
	foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
		$sheetName = $worksheet->getTitle();
		echo 'Worksheet: ' , $sheetName , EOL;

		$chartNames = $worksheet->getChartNames();
		if(empty($chartNames)) {
			echo '    There are no charts in this worksheet' , EOL;
		} else {
			natsort($chartNames);
			foreach($chartNames as $i => $chartName) {
				$chart = $worksheet->getChartByName($chartName);
				if (!is_null($chart->getTitle())) {
					$caption = '"' . implode(' ',$chart->getTitle()->getCaption()) . '"';
				} else {
					$caption = 'Untitled';
				}
				echo '    ' , $chartName , ' - ' , $caption , EOL;
				echo str_repeat(' ',strlen($chartName)+3);

				$jpegFile = '35'.str_replace('.xlsx', '.jpg', substr($inputFileNameShort,2));
				if (file_exists($jpegFile)) {
					unlink($jpegFile);
				}
				try {
					$chart->render($jpegFile);
				} catch (Exception $e) {
					echo 'Error rendering chart: ',$e->getMessage();
				}
			}
		}
	}


	$objPHPExcel->disconnectWorksheets();
	unset($objPHPExcel);
}

// Echo memory peak usage
echo date('H:i:s') , ' Peak memory usage: ' , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;

// Echo done
echo date('H:i:s') , " Done rendering charts as images" , EOL;
echo 'Image files have been created in ' , getcwd() , EOL;
>>>>>>> 5074fdd666065b44da9222b2537f8dec20deeb5f
