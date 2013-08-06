<?php
/**
 * @package     Koowa_Service
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Service Instantiatable Interface
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Service
 */
interface KServiceInstantiatable
{
    /**
     * Get the object identifier
     *
     * @param   KConfig $config Configuration options
     * @param 	object	A KServiceInterface object
     * @return  object
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container);
}