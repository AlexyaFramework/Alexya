<?php
/**
 * Alexya Framework - config/paths.php
 *
 * This file contains the constants related to path
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */

/**
 * Alias for DIRECTORY_SEPARATOR
 */
define("DS", DIRECTORY_SEPARATOR);

/**
 * Root dir
 */
define("ROOT_DIR", dirname(dirname(__FILE__)).DS);

/**
 * Alexya's core dir
 */
define("ALEXYA_DIR", ROOT_DIR."Alexya".DS);

/**
 * Application's dir
 */
define("APPLICATION_DIR", ROOT_DIR."Application".DS);

/**
 * Models dir
 */
define("MODELS_DIR", APPLICATION_DIR."Models".DS);

/**
 * Views dir
 */
define("VIEWS_DIR", APPLICATION_DIR."Views".DS);

/**
 * Controllers dir
 */
define("CONTROLLERS_DIR", APPLICATION_DIR."Controllers".DS);

/**
 * Routes dir
 */
define("ROUTES_DIR", APPLICATION_DIR."Routes".DS);

/**
 * Locales dir
 */
define("LOCALES_DIR", APPLICATION_DIR."Locales".DS);

/**
 * 3rd part libraries dir
 */
define("LIB_DIR", ROOT_DIR."lib".DS);

/**
 * Packages dir
 */
define("PACKAGES_DIR", ROOT_DIR."packages".DS);

/**
 * Translations dir
 */
define("TRANSLATIONS_DIR", ROOT_DIR."translations".DS);
