<?php

declare (strict_types=1);
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package Admin
 * @subpackage JsonAdm
 */
namespace Aimeos\Admin\Json_Adm;

use Psr\Http\Message\Response_Interface;
use Psr\Http\Message\Server_Request_Interface;
/**
 * JSON API client interface
 *
 * @package Admin
 * @subpackage JsonAdm
 */
interface Iface
{
    /**
     * Deletes the resource or the resource list
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object
     * @param \Psr\Http\Message\ResponseInterface $response Response object
     * @return \Psr\Http\Message\ResponseInterface Modified response object
     */
    public function delete(Server_Request_Interface $request, Response_Interface $response): \Psr\Http\Message\Response_Interface;
    /**
     * Returns the requested resource or the resource list
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object
     * @param \Psr\Http\Message\ResponseInterface $response Response object
     * @return \Psr\Http\Message\ResponseInterface Modified response object
     */
    public function get(Server_Request_Interface $request, Response_Interface $response): \Psr\Http\Message\Response_Interface;
    /**
     * Updates the resource or the resource list partitially
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object
     * @param \Psr\Http\Message\ResponseInterface $response Response object
     * @return \Psr\Http\Message\ResponseInterface Modified response object
     */
    public function patch(Server_Request_Interface $request, Response_Interface $response): \Psr\Http\Message\Response_Interface;
    /**
     * Creates or updates the resource or the resource list
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object
     * @param \Psr\Http\Message\ResponseInterface $response Response object
     * @return \Psr\Http\Message\ResponseInterface Modified response object
     */
    public function post(Server_Request_Interface $request, Response_Interface $response): \Psr\Http\Message\Response_Interface;
    /**
     * Creates or updates the resource or the resource list
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object
     * @param \Psr\Http\Message\ResponseInterface $response Response object
     * @return \Psr\Http\Message\ResponseInterface Modified response object
     */
    public function put(Server_Request_Interface $request, Response_Interface $response): \Psr\Http\Message\Response_Interface;
    /**
     * Returns the available REST verbs
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object
     * @param \Psr\Http\Message\ResponseInterface $response Response object
     * @param string|null $prefix Form parameter prefix when nesting parameters is required
     * @return \Psr\Http\Message\ResponseInterface Modified response object
     */
    public function options(Server_Request_Interface $request, Response_Interface $response): \Psr\Http\Message\Response_Interface;
    /**
     * Sets the view object that will generate the admin output.
     *
     * @param \Aimeos\Base\View\Iface $view The view object which generates the admin output
     * @return \Aimeos\Admin\JsonAdm\Iface Reference to this object for fluent calls
     */
    public function set_view(\Aimeos\Base\View\Iface $view): \Aimeos\Admin\Json_Adm\Iface;
}