<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

define('PATH_tx_deprecated', t3lib_extMgm::extPath($_EXTKEY));
require_once PATH_tx_deprecated . 'class.tx_deprecated.php';
?>