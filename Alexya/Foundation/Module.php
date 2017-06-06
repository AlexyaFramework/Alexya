<?php
namespace Alexya\Foundation;

/**
 * Module triad.
 *
 * Simply used for storing the HMV(VM)C in the package `\Application\Module`
 * instead of `\Application`.
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class Module extends Triad
{
    /**
     * Namespace prefix.
     *
     * @var string
     */
    protected $_prefix = "\\Application\\Module\\";
}
