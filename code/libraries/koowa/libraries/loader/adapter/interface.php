<?php
/**
 * @package		Koowa_Loader
 * @subpackage 	Adapter
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Loader Adapter Interface
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Loader
 * @subpackage 	Adapter
 */
interface KLoaderAdapterInterface
{
    /**
	 * Get the type
	 *
	 * @return string	Returns the type
	 */
	public function getType();

	/**
	 * Get the class prefix
	 *
	 * @return string	Returns the class prefix
	 */
	public function getPrefix();

    /**
     * Register a specific package basepath
     *
     * @param  string   $basepath The base path of the package
     * @param  string   $package
     * @return KLoaderAdapterInterface
     */
    public function registerBasepath($basepath, $package = null);

    /**
     * Get the registered base paths
     *
     * @return array An array with package name as keys and base path as values
     */
    public function getBasepaths();

    /**
     * Get the path based on a class name
     *
     * @param  string           The class name
     * @return string|false     Returns the path on success FALSE on failure
     */
    public function findPath($classname, $basepath = null);
}