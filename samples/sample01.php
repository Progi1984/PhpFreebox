<?php
	/**
	 * List directories and file from Freebox
	 */

	include '../PHPFreebox/PHPFreebox.php';
	include 'config.php';
	
	if(isset($_GET['dir']) && !empty($_GET['dir'])){
		$paramDir = $_GET['dir'];
	} else {
		$paramDir = '';
	}

	$oFreebox = new PHPFreebox(FREEBOX_API_URL, FREEBOX_API_LOGIN, FREEBOX_API_PASSWORD);
	//$oFreebox->_debug  = true;

	$arrListing = $oFreebox->fs_listDirectory($paramDir);
	if(!empty($arrListing)){
		$sDir = '';
		$sFile = '';
		if($paramDir != ''){
			$arrParent = explode('/', $paramDir);
			array_pop($arrParent);
			$sParent = '<li>[dir] <a href="?dir='.urlencode(implode('/', $arrParent)).'">..</a>';
		} else {
			$sParent = '';
		}
		foreach ($arrListing as $item){
			if($item['type'] == 'dir'){
				$sDir .= '<li>['.$item['type'].'] <a href="?dir='.urlencode($paramDir.'/'.$item['name']).'">'.utf8_decode($item['name']).'</a>';
			}
			if($item['type'] == 'file'){
				$sFile .= '<li>['.$item['type'].'] '.utf8_decode($item['name']).'';
			}
		}
		echo '<ul>'.$sParent.$sDir.$sFile.'</ul>';
	} else {
		if($paramDir != ''){
			$arrParent = explode('/', $paramDir);
			array_pop($arrParent);
			$sParent = '<li>[dir] <a href="?dir='.urlencode(implode('/', $arrParent)).'">..</a>';
			echo '<ul>'.$sParent.'</ul>';
		}
	}
