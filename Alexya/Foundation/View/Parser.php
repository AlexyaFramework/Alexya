<?php
namespace Alexya\Foundation\View;

use Alexya\FileSystem\File;
use Alexya\Tools\Collection;

/**
 * Parser class.
 *
 * This is the base class for all view parsers.
 *
 * The constructor accepts as parameter the file object
 * to the view and the view object.
 *
 * Each parser must implement the `parse` method that will
 * parse the view and return the output as a string.
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
abstract class Parser
{
    /**
     * View data.
     *
     * @var Collection
     */
    protected $_data;

    /**
     * File object.
     *
     * @var File
     */
    protected $_file;

    /**
     * Constructor.
     *
     * @param File       $file View file object.
     * @param Collection $data View data.
     */
    public function __construct(File $file, Collection $data)
    {
        $this->_file = $file;
        $this->_data = $data;
    }

    /**
     * Parses the view.
     *
     * @return string Parsed view.
     */
    public abstract function parse() : string;
}
