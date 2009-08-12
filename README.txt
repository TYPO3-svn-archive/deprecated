Basic documentation:
====================

1) t3lib_div::makeInstance() / t3lib_div::makeInstanceClassName() -- deprecated since TYPO3 4.3

	before:
		$objectName = t3lib_div::makeInstanceClassName('tx_myext');
		$object = new $objectName(1, 2, 3);

	after:
		$object = tx_deprecated::makeInstance('tx_myext', 1, 2, 3);

