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
	const INVOCATION_TemplateNew = 'return new %s(%s);';
	const INVOCATION_TemplateMakeInstanceSimple = 'return t3lib_div::makeInstance(\'%s\');';
	const INVOCATION_TemplateMakeInstanceArguments = 'return t3lib_div::makeInstance(\'%s\', %s);';

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
	 * @type	deprecated
	 */
	static public function makeInstance($className) {
		$arguments = func_get_args();

		if (version_compare(self::TYPO3_branch, '4.3', '<')) {
			$objectClassName = t3lib_div::makeInstanceClassName($className);
			$invocation = self::getInvocation(
				$objectClassName,
				$arguments,
				self::INVOCATION_TemplateNew
			);
		} else {
			$invocation = self::getInvocation(
				$className,
				$arguments,
				(count($arguments) ? self::INVOCATION_TemplateMakeInstanceArguments : self::INVOCATION_TemplateMakeInstanceSimple)
			);
		}

		return $invocation($arguments);
	}

	/**
	 * Fetches the permissions on file operations of the current user.
	 * The behaviour was changed with TYPO3 4.3. Since then these permissions
	 * are stored for backend user groups by default.
	 *
	 * @return	integer		File permission integer from BE_USER
	 * @since	TYPO3 4.3
	 * @type	behaviour
	 */
	static public function getFileoperationPermissions() {
		if (version_compare(self::TYPO3_branch, '4.3', '<')) {
			$fileoperationPermissions = $GLOBALS['BE_USER']->user['fileoper_perms'];
		} else {
			$fileoperationPermissions = $GLOBALS['BE_USER']->getFileoperationPermissions();
		}

		return $fileoperationPermissions;
	}

	/**
	 * Gets an invocation to create a new instance of an object.
	 *
	 * @param	string		$className: The name of the class to be created
	 * @param	array		$arguments: Arguments to be forwared to the constructor
	 * @param	string		$invocationTemplate: The template to be used inside create_function
	 * @return	string		An anonymous function that creates a new instance of $classname
	 * @see		create_function
	 */
	static private function getInvocation($className, array $arguments, $invocationTemplate) {
		$constructorArguments = array();

		$numberOfArguments = count($arguments);

		for ($i = 1; $i <= $numberOfArguments; $i++) {
			$constructorArguments[] = '$arguments[' . $i . ']';
		}

		return create_function(
			'$arguments',
			sprintf($invocationTemplate, $className, implode(', ', $constructorArguments))
		);
	}
}