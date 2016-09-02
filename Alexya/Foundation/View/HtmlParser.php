<?php
namespace Alexya\Foundation\View;

/**
 * The HTML parser.
 *
 * This parser simply returns the content of the view file.
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class HTMLParser extends Parser
{
    /**
     * Parses the view.
     *
     * @return string Parsed view.
     */
    public function parse() : string
    {
        return $this->_file->getContent();
    }
}
