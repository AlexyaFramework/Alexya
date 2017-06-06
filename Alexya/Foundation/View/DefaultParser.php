<?php
namespace Alexya\Foundation\View;

use \Exception;

/**
 * The default parser.
 *
 * This parser simply extracts view's variables and includes the
 * view file as a php script.
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class DefaultParser extends Parser
{
    /**
     * Parses the view.
     *
     * @return string Parsed view.
     *
     * @throws Exception If something goes wrong.
     */
    public function parse() : string
    {
        extract($this->_data->getAll());

        ob_start();
        try {
            require($this->_file->getPath());
        } catch(Exception $e) {
            ob_end_clean();
            throw $e;
        }

        return ob_get_clean();
    }
}
