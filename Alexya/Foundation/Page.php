<?php
namespace Alexya\Foundation;

/**
 * Page triad.
 *
 * Simply used for storing the HMV(VM)C in the package `\Application\Page`
 * instead of `\Application`.
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class Page extends Triad
{
    /**
     * Namespace prefix.
     *
     * @var string
     */
    protected $_prefix = "\\Application\\Page\\";
}
