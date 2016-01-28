<?php
/**
 * Test bootstrap process
 * Used in phpunit.xml config to add our additional components
 *
 * @copyright 	(c) 2015-present Bolt Software Pvt. Ltd.
 * @licence			GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
define('ROOT', dirname(__FILE__));
define('DS', DIRECTORY_SEPARATOR);
define('DATA', 'data' . DS);
define('FIXTURES', ROOT . DS . DATA . 'fixtures' . DS);
define('GPG_FIXTURES', FIXTURES . 'gpg');
define('GPG_DUMMY', FIXTURES . 'gpg-dummy');
define('GPG_SERVER', FIXTURES . 'gpg-server');
define('IMG_FIXTURES', FIXTURES . 'img');

require_once ROOT . '/passbolt/__init__.php';
