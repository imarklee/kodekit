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
 * Dispatcher Authenticator Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Dispatcher\Authenticator
 */
interface DispatcherAuthenticatorInterface
{
    /**
     * Priority levels
     */
    const PRIORITY_HIGHEST = 1;
    const PRIORITY_HIGH    = 2;
    const PRIORITY_NORMAL  = 3;
    const PRIORITY_LOW     = 4;
    const PRIORITY_LOWEST  = 5;

    /**
     * Get the authentication scheme
     *
     * @link http://tools.ietf.org/html/rfc7235#section-4.1
     *
     * @return string The authentication scheme
     */
    public function getScheme();

    /**
     * Get the priority of the authenticator
     *
     * @return  integer The priority level
     */
    public function getPriority();

    /**
     * Authenticate the request
     *
     * @param DispatcherContext $context    A dispatcher context object
     * @return  boolean Returns TRUE if the authentication explicitly succeeded.
     */
    public function authenticateRequest(DispatcherContext $context);

    /**
     * Challenge the response
     *
     * @link http://tools.ietf.org/html/rfc7235#section-2.1
     *
     * @param DispatcherContext $context   A dispatcher context object
     * @return void
     */
    public function challengeResponse(DispatcherContext $context);

    /**
     * Log the user in
     *
     * @param mixed  $user A user key or name, an array of user data or a UserInterface object. Default NULL
     * @param array  $data Optional user data
     * @return bool
     */
    public function loginUser($user = null, $data = array());
}