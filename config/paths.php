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
 * Translations dir
 */
define("TRANSLATIONS_DIR", ROOT_DIR."translations".DS);
