<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * View Templatable Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\View
 */
interface ViewTemplatable
{
    /**
     * Get the layout
     *
     * @return string The layout name
     */
    public function getLayout();

    /**
     * Qualify the layout
     *
     * Convert a relative layout URL into an absolute layout URL
     *
     * @param string $layout The view layout name
     * @param string $type   The filesystem locator type
     * @return string   The fully qualified template url
     */
    public function qualifyLayout($layout, $type = 'com');

    /**
     * Get the template object attached to the view
     *
     *  @throws	\UnexpectedValueException	If the template doesn't implement the TemplateInterface
     * @return  TemplateInterface
     */
    public function getTemplate();
}