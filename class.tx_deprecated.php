<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Oliver Hader <oliver@typo3.org>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

final class tx_deprecated {
	const TYPO3_branch = TYPO3_branch;
	const INVOKATION_TemplateNew = 'return new %s(%s);';
	const INVOKATION_TemplateMakeInstanceSimple = 'return t3lib_div::makeInstance(\'%s\');';
	const INVOKATION_TemplateMakeInstanceArguments = 'return t3lib_div::makeInstance(\'%s\', %s);';

	/**
	 * Makes and instance of a class name.
	 * This method accecpts any additional arguments that will be forwarded.
	 *
	 * Deprecation background:
	 * In TYPO3 4.3 the t3lib_Singleton interface was integrated. To ensure that
	 * ther is no work-around to bypass the singleton criterion the static function
	 * t3lib_div::makeInstanceClassName() was marked as deprecated.
	 *
	 * @param	string		$className: Name of the class to be created
	 * @return	object		The requested object
	 * @since	TYPO3 4.3
	 */
	static public function makeInstance($className) {
		$arguments = func_get_args();

		if (version_compare(self::TYPO3_branch, '4.3', '<')) {
			$objectClassName = t3lib_div::makeInstanceClassName($className);
			$invokation = self::getInvokation(
				$objectClassName,
				$arguments,
				self::INVOKATION_TemplateNew
			);
		} else {
			$invokation = self::getInvokation(
				$className,
				$arguments,
				(count($arguments) ? self::INVOKATION_TemplateMakeInstanceArguments : self::INVOKATION_TemplateMakeInstanceSimple)
			);
		}

		return $invokation($arguments);
	}

	static private function getInvokation($className, array $arguments, $invokationTemplate) {
		$constructorArguments = array();

		$numberOfArguments = count($arguments);

		for ($i = 1; $i <= $numberOfArguments; $i++) {
			$constructorArguments[] = '$arguments[' . $i . ']';
		}

		return create_function(
			'$arguments',
			sprintf($invokationTemplate, $className, implode(', ', $constructorArguments))
		);
	}
}