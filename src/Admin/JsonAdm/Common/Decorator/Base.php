<?php

declare (strict_types=1);
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package Admin
 * @subpackage JsonAdm
 */
namespace Aimeos\Admin\Json_Adm\Common\Decorator;

use Psr\Http\Message\Response_Interface;
use Psr\Http\Message\Server_Request_Interface;
/**
 * Provides common methods for JSON API client decorators
 *
 * @package Admin
 * @subpackage JsonAdm
 */
abstract class Base extends \Aimeos\Admin\Json_Adm\Base implements \Aimeos\Admin\Json_Adm\Common\Decorator\Iface
{
    /**
     * Initializes the client decorator.
     *
     * @param \Aimeos\Admin\JsonAdm\Iface $client Client object
     * @param \Aimeos\MShop\ContextIface $context Context object with required objects
     * @param string $path Name of the client separated by slashes, e.g "product/property"
     */
    public function __construct(private \Aimeos\Admin\Json_Adm\Iface $client, \Aimeos\M_Shop\Context_Iface $context, string $path)
    {
        parent::__construct($context, $path);
    }
    /**
     * Passes unknown methods to wrapped objects
     *
     * @param string $name Name of the method
     * @param array $param List of method parameter
     * @return mixed Returns the value of the called method
     * @throws \Aimeos\Admin\JsonAdm\Exception If method call failed
     */
    public function __call(string $name, array $param)
    {
        return @call_user_func_array([$this->client, $name], $param);
    }
    /**
     * Deletes the resource or the resource list
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object
     * @param \Psr\Http\Message\ResponseInterface $response Response object
     * @return \Psr\Http\Message\ResponseInterface Modified response object
     */
    public function delete(Server_Request_Interface $request, Response_Interface $response): \Psr\Http\Message\Response_Interface
    {
        return $this->client->delete($request, $response);
    }
    /**
     * Returns the requested resource or the resource list
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object
     * @param \Psr\Http\Message\ResponseInterface $response Response object
     * @return \Psr\Http\Message\ResponseInterface Modified response object
     */
    public function get(Server_Request_Interface $request, Response_Interface $response): \Psr\Http\Message\Response_Interface
    {
        return $this->client->get($request, $response);
    }
    /**
     * Updates the resource or the resource list partitially
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object
     * @param \Psr\Http\Message\ResponseInterface $response Response object
     * @return \Psr\Http\Message\ResponseInterface Modified response object
     */
    public function patch(Server_Request_Interface $request, Response_Interface $response): \Psr\Http\Message\Response_Interface
    {
        return $this->client->patch($request, $response);
    }
    /**
     * Creates or updates the resource or the resource list
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object
     * @param \Psr\Http\Message\ResponseInterface $response Response object
     * @return \Psr\Http\Message\ResponseInterface Modified response object
     */
    public function post(Server_Request_Interface $request, Response_Interface $response): \Psr\Http\Message\Response_Interface
    {
        return $this->client->post($request, $response);
    }
    /**
     * Creates or updates the resource or the resource list
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object
     * @param \Psr\Http\Message\ResponseInterface $response Response object
     * @return \Psr\Http\Message\ResponseInterface Modified response object
     */
    public function put(Server_Request_Interface $request, Response_Interface $response): \Psr\Http\Message\Response_Interface
    {
        return $this->client->put($request, $response);
    }
    /**
     * Returns the available REST verbs
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object
     * @param \Psr\Http\Message\ResponseInterface $response Response object
     * @param string|null $prefix Form parameter prefix when nesting parameters is required
     * @return \Psr\Http\Message\ResponseInterface Modified response object
     */
    public function options(Server_Request_Interface $request, Response_Interface $response, ?string $prefix = null): \Psr\Http\Message\Response_Interface
    {
        return $this->client->options($request, $response, $prefix);
    }
    /**
     * Returns the Aimeos bootstrap object
     *
     * @return \Aimeos\Bootstrap The Aimeos bootstrap object
     */
    public function get_aimeos(): \Aimeos\Bootstrap
    {
        return $this->client->get_aimeos();
    }
    /**
     * Sets the Aimeos bootstrap object
     *
     * @param \Aimeos\Bootstrap $aimeos The Aimeos bootstrap object
     * @return \Aimeos\Admin\JsonAdm\Iface Reference to this object for fluent calls
     */
    public function set_aimeos(\Aimeos\Bootstrap $aimeos): \Aimeos\Admin\Json_Adm\Iface
    {
        parent::set_aimeos($aimeos);
        $this->client->set_aimeos($aimeos);
        return $this;
    }
    /**
     * Sets the view object that will generate the admin output.
     *
     * @param \Aimeos\Base\View\Iface $view The view object which generates the admin output
     * @return \Aimeos\Admin\JsonAdm\Iface Reference to this object for fluent calls
     */
    public function set_view(\Aimeos\Base\View\Iface $view): \Aimeos\Admin\Json_Adm\Iface
    {
        parent::set_view($view);
        $this->client->set_view($view);
        return $this;
    }
    /**
     * Returns the underlying admin client object;
     *
     * @return \Aimeos\Admin\JsonAdm\Iface Admin client object
     */
    protected function get_client(): \Aimeos\Admin\Json_Adm\Iface
    {
        return $this->client;
    }
}