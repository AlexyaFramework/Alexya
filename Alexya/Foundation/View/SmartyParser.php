<?php
namespace Alexya\Foundation\View;

use \Exception;

/**
 * The Smarty parser.
 *
 * This parser assumes that the file is a valid smarty template.
 *
 * This requires Smarty to be installed, execute `composer require smarty/smarty` or
 * add the followin require to `composer.json`:
 *
 *     "smarty/smarty": "^3.1"
 *
 * After that, execute `composer update` to install smarty.
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class SmartyParser extends Parser
{
    /**
     * Parses the view.
     *
     * @return string Parsed view.
     */
    public function parse() : string
    {
        if(!class_exists("\\Smarty")) {
            throw new Exception("Smarty isn't installed, please, add the dependency to composer!");
        }

        $smarty = new \Smarty();

        foreach($this->_data->getAll() as $key => $value) {
            $smarty->assign($key, $value);
        }

        return $smarty->fetch($this->_file->getPath());
    }
}
