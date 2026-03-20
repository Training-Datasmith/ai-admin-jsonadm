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
 * JSON API standard client
 *
 * @package Admin
 * @subpackage JsonAdm
 */
class Standard extends \Aimeos\Admin\Json_Adm\Base implements \Aimeos\Admin\Json_Adm\Common\Iface
{
    /**
     * Deletes the resource or the resource list
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object
     * @param \Psr\Http\Message\ResponseInterface $response Response object
     * @return \Psr\Http\Message\ResponseInterface Modified response object
     */
    public function delete(Server_Request_Interface $request, Response_Interface $response): \Psr\Http\Message\Response_Interface
    {
        $view = $this->view();
        try {
            $response = $this->delete_items($view, $request, $response);
            $status = 200;
        } catch (\Aimeos\Admin\Json_Adm\Exception $e) {
            $status = $e->get_code();
            $view->errors = [['title' => $this->context()->translate('admin/jsonadm', $e->get_message()), 'detail' => $e->get_trace_as_string()]];
        } catch (\Aimeos\M_Shop\Exception $e) {
            $status = 404;
            $view->errors = [['title' => $this->context()->translate('mshop', $e->get_message()), 'detail' => $e->get_trace_as_string()]];
        } catch (\Exception $e) {
            $status = 500;
            $view->errors = [['title' => $e->get_message(), 'detail' => $e->get_trace_as_string()]];
        }
        /** admin/jsonadm/template-delete
         * Relative path to the JSON API template for DELETE requests
         *
         * The template file contains the code and processing instructions
         * to generate the result shown in the JSON API body. The
         * configuration string is the path to the template file relative
         * to the templates directory (usually in templates/admin/jsonadm).
         *
         * You can overwrite the template file configuration in extensions and
         * provide alternative templates. These alternative templates should be
         * named like the default one but with the string "standard" replaced by
         * an unique name. You may use the name of your project for this. If
         * you've implemented an alternative client class as well, "standard"
         * should be replaced by the name of the new class.
         *
         * @param string Relative path to the template creating the body for the DELETE method of the JSON API
         * @since 2015.12
         * @category Developer
         * @see admin/jsonadm/template-aggregate
         * @see admin/jsonadm/template-get
         * @see admin/jsonadm/template-patch
         * @see admin/jsonadm/template-post
         * @see admin/jsonadm/template-put
         * @see admin/jsonadm/template-options
         */
        $tplconf = 'admin/jsonadm/template-delete';
        $default = 'delete';
        $body = $view->render($view->config($tplconf, $default));
        return $response->with_header('Content-Type', 'application/vnd.api+json; supported-ext="bulk"')->with_body($view->response()->create_stream_from_string($body))->with_status($status);
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
        $view = $this->view();
        try {
            $response = $this->get_items($view, $request, $response);
            $status = 200;
        } catch (\Aimeos\M_Shop\Exception $e) {
            $status = 404;
            $view->errors = [['title' => $this->context()->translate('mshop', $e->get_message()), 'detail' => $e->get_trace_as_string()]];
        } catch (\Exception $e) {
            $status = 500;
            $view->errors = [['title' => $e->get_message(), 'detail' => $e->get_trace_as_string()]];
        }
        if ($view->param('aggregate') !== null) {
            /** admin/jsonadm/template-aggregate
             * Relative path to the JSON API template for GET aggregate requests
             *
             * The template file contains the code and processing instructions
             * to generate the result shown in the JSON API body. The
             * configuration string is the path to the template file relative
             * to the templates directory (usually in templates/admin/jsonadm).
             *
             * You can overwrite the template file configuration in extensions and
             * provide alternative templates. These alternative templates should be
             * named like the default one but with the string "standard" replaced by
             * an unique name. You may use the name of your project for this. If
             * you've implemented an alternative client class as well, "standard"
             * should be replaced by the name of the new class.
             *
             * @param string Relative path to the template creating the body for the GET aggregate request of the JSON API
             * @since 2016.07
             * @category Developer
             * @see admin/jsonadm/template-delete
             * @see admin/jsonadm/template-get
             * @see admin/jsonadm/template-patch
             * @see admin/jsonadm/template-post
             * @see admin/jsonadm/template-put
             * @see admin/jsonadm/template-options
             */
            $tplconf = 'admin/jsonadm/template-aggregate';
            $default = 'aggregate';
        } else {
            /** admin/jsonadm/template-get
             * Relative path to the JSON API template for GET requests
             *
             * The template file contains the code and processing instructions
             * to generate the result shown in the JSON API body. The
             * configuration string is the path to the template file relative
             * to the templates directory (usually in templates/admin/jsonadm).
             *
             * You can overwrite the template file configuration in extensions and
             * provide alternative templates. These alternative templates should be
             * named like the default one but with the string "standard" replaced by
             * an unique name. You may use the name of your project for this. If
             * you've implemented an alternative client class as well, "standard"
             * should be replaced by the name of the new class.
             *
             * @param string Relative path to the template creating the body for the GET method of the JSON API
             * @since 2015.12
             * @category Developer
             * @see admin/jsonadm/template-aggregate
             * @see admin/jsonadm/template-delete
             * @see admin/jsonadm/template-patch
             * @see admin/jsonadm/template-post
             * @see admin/jsonadm/template-put
             * @see admin/jsonadm/template-options
             */
            $tplconf = 'admin/jsonadm/template-get';
            $default = 'get';
        }
        $body = $view->render($view->config($tplconf, $default));
        return $response->with_header('Content-Type', 'application/vnd.api+json; supported-ext="bulk"')->with_body($view->response()->create_stream_from_string($body))->with_status($status);
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
        $view = $this->view();
        try {
            $response = $this->patch_items($view, $request, $response);
            $status = 200;
        } catch (\Aimeos\Admin\Json_Adm\Exception $e) {
            $status = $e->get_code();
            $view->errors = [['title' => $this->context()->translate('admin/jsonadm', $e->get_message()), 'detail' => $e->get_trace_as_string()]];
        } catch (\Aimeos\M_Shop\Exception $e) {
            $status = 404;
            $view->errors = [['title' => $this->context()->translate('mshop', $e->get_message()), 'detail' => $e->get_trace_as_string()]];
        } catch (\Exception $e) {
            $status = 500;
            $view->errors = [['title' => $e->get_message(), 'detail' => $e->get_trace_as_string()]];
        }
        /** admin/jsonadm/template-patch
         * Relative path to the JSON API template for PATCH requests
         *
         * The template file contains the code and processing instructions
         * to generate the result shown in the JSON API body. The
         * configuration string is the path to the template file relative
         * to the templates directory (usually in templates/admin/jsonadm).
         *
         * You can overwrite the template file configuration in extensions and
         * provide alternative templates. These alternative templates should be
         * named like the default one but with the string "standard" replaced by
         * an unique name. You may use the name of your project for this. If
         * you've implemented an alternative client class as well, "standard"
         * should be replaced by the name of the new class.
         *
         * @param string Relative path to the template creating the body for the PATCH method of the JSON API
         * @since 2015.12
         * @category Developer
         * @see admin/jsonadm/template-aggregate
         * @see admin/jsonadm/template-get
         * @see admin/jsonadm/template-post
         * @see admin/jsonadm/template-delete
         * @see admin/jsonadm/template-put
         * @see admin/jsonadm/template-options
         */
        $tplconf = 'admin/jsonadm/template-patch';
        $default = 'patch';
        $body = $view->render($view->config($tplconf, $default));
        return $response->with_header('Content-Type', 'application/vnd.api+json; supported-ext="bulk"')->with_body($view->response()->create_stream_from_string($body))->with_status($status);
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
        $view = $this->view();
        try {
            $response = $this->post_items($view, $request, $response);
            $status = 201;
        } catch (\Aimeos\Admin\Json_Adm\Exception $e) {
            $status = $e->get_code();
            $view->errors = [['title' => $this->context()->translate('admin/jsonadm', $e->get_message()), 'detail' => $e->get_trace_as_string()]];
        } catch (\Aimeos\M_Shop\Exception $e) {
            $status = 404;
            $view->errors = [['title' => $this->context()->translate('mshop', $e->get_message()), 'detail' => $e->get_trace_as_string()]];
        } catch (\Exception $e) {
            $status = 500;
            $view->errors = [['title' => $e->get_message(), 'detail' => $e->get_trace_as_string()]];
        }
        /** admin/jsonadm/template-post
         * Relative path to the JSON API template for POST requests
         *
         * The template file contains the code and processing instructions
         * to generate the result shown in the JSON API body. The
         * configuration string is the path to the template file relative
         * to the templates directory (usually in templates/admin/jsonadm).
         *
         * You can overwrite the template file configuration in extensions and
         * provide alternative templates. These alternative templates should be
         * named like the default one but with the string "standard" replaced by
         * an unique name. You may use the name of your project for this. If
         * you've implemented an alternative client class as well, "standard"
         * should be replaced by the name of the new class.
         *
         * @param string Relative path to the template creating the body for the POST method of the JSON API
         * @since 2015.12
         * @category Developer
         * @see admin/jsonadm/template-aggregate
         * @see admin/jsonadm/template-get
         * @see admin/jsonadm/template-patch
         * @see admin/jsonadm/template-delete
         * @see admin/jsonadm/template-put
         * @see admin/jsonadm/template-options
         */
        $tplconf = 'admin/jsonadm/template-post';
        $default = 'post';
        $body = $view->render($view->config($tplconf, $default));
        return $response->with_header('Content-Type', 'application/vnd.api+json; supported-ext="bulk"')->with_body($view->response()->create_stream_from_string($body))->with_status($status);
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
        $status = 501;
        $view = $this->view();
        $view->errors = [['title' => $this->context()->translate('admin/jsonadm', 'Not implemented, use PATCH instead')]];
        /** admin/jsonadm/template-put
         * Relative path to the JSON API template for PUT requests
         *
         * The template file contains the code and processing instructions
         * to generate the result shown in the JSON API body. The
         * configuration string is the path to the template file relative
         * to the templates directory (usually in templates/admin/jsonadm).
         *
         * You can overwrite the template file configuration in extensions and
         * provide alternative templates. These alternative templates should be
         * named like the default one but with the string "standard" replaced by
         * an unique name. You may use the name of your project for this. If
         * you've implemented an alternative client class as well, "standard"
         * should be replaced by the name of the new class.
         *
         * @param string Relative path to the template creating the body for the PUT method of the JSON API
         * @since 2015.12
         * @category Developer
         * @see admin/jsonadm/template-aggregate
         * @see admin/jsonadm/template-delete
         * @see admin/jsonadm/template-patch
         * @see admin/jsonadm/template-post
         * @see admin/jsonadm/template-get
         * @see admin/jsonadm/template-options
         */
        $tplconf = 'admin/jsonadm/template-put';
        $default = 'put';
        $body = $view->render($view->config($tplconf, $default));
        return $response->with_header('Content-Type', 'application/vnd.api+json; supported-ext="bulk"')->with_body($view->response()->create_stream_from_string($body))->with_status($status);
    }
    /**
     * Returns the available REST verbs and the available resources
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object
     * @param \Psr\Http\Message\ResponseInterface $response Response object
     * @return \Psr\Http\Message\ResponseInterface Modified response object
     */
    public function options(Server_Request_Interface $request, Response_Interface $response): \Psr\Http\Message\Response_Interface
    {
        $context = $this->context();
        $view = $this->view();
        try {
            $resources = $attributes = [];
            foreach ($this->get_domains($view) as $domain) {
                $manager = \Aimeos\M_Shop::create($context, $domain);
                $attributes = array_merge($attributes, $manager->get_search_attributes(true));
                $resources[] = join('/', $manager->type());
            }
            foreach ($this->get_resources($view) as $resource) {
                $resources[] = $resource;
            }
            $view->resources = $this->get_allowed_resources($view, $resources);
            $view->attributes = $attributes;
            $status = 200;
        } catch (\Aimeos\M_Shop\Exception $e) {
            $status = 404;
            $view->errors = [['title' => $context->translate('mshop', $e->get_message()), 'detail' => $e->get_trace_as_string()]];
        } catch (\Exception $e) {
            $status = 500;
            $view->errors = [['title' => $e->get_message(), 'detail' => $e->get_trace_as_string()]];
        }
        /** admin/jsonadm/template-options
         * Relative path to the JSON API template for OPTIONS requests
         *
         * The template file contains the code and processing instructions
         * to generate the result shown in the JSON API body. The
         * configuration string is the path to the template file relative
         * to the templates directory (usually in templates/admin/jsonadm).
         *
         * You can overwrite the template file configuration in extensions and
         * provide alternative templates. These alternative templates should be
         * named like the default one but with the string "standard" replaced by
         * an unique name. You may use the name of your project for this. If
         * you've implemented an alternative client class as well, "standard"
         * should be replaced by the name of the new class.
         *
         * @param string Relative path to the template creating the body for the OPTIONS method of the JSON API
         * @since 2015.12
         * @category Developer
         * @see admin/jsonadm/template-aggregate
         * @see admin/jsonadm/template-delete
         * @see admin/jsonadm/template-patch
         * @see admin/jsonadm/template-post
         * @see admin/jsonadm/template-get
         * @see admin/jsonadm/template-put
         */
        $tplconf = 'admin/jsonadm/template-options';
        $default = 'options';
        $body = $view->render($view->config($tplconf, $default));
        return $response->with_header('Allow', 'DELETE,GET,PATCH,POST,OPTIONS')->with_header('Content-Type', 'application/vnd.api+json; supported-ext="bulk"')->with_body($view->response()->create_stream_from_string($body))->with_status($status);
    }
    /**
     * Deletes one or more items
     *
     * @param \Aimeos\Base\View\Iface $view View instance with "param" view helper
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object
     * @param \Psr\Http\Message\ResponseInterface $response Response object
     * @return \Psr\Http\Message\ResponseInterface Modified response object
     * @throws \Aimeos\Admin\JsonAdm\Exception If the request body is invalid
     */
    protected function delete_items(\Aimeos\Base\View\Iface $view, Server_Request_Interface $request, Response_Interface $response): \Psr\Http\Message\Response_Interface
    {
        $manager = \Aimeos\M_Shop::create($this->context(), $this->get_path());
        if (($id = $view->param('id')) == null) {
            $body = (string) $request->get_body();
            if (($payload = json_decode($body)) === null || !isset($payload->data) || !is_array($payload->data)) {
                throw new \Aimeos\Admin\Json_Adm\Exception('Invalid JSON in body', 400);
            }
            $ids = map($payload->data)->col('id');
            $manager->delete($ids->to_array());
            $view->total = count($ids);
        } else {
            $manager->delete($id);
            $view->total = 1;
        }
        return $response;
    }
    /**
     * Retrieves the item or items and adds the data to the view
     *
     * @param \Aimeos\Base\View\Iface $view View instance
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object
     * @param \Psr\Http\Message\ResponseInterface $response Response object
     * @return \Psr\Http\Message\ResponseInterface Modified response object
     */
    protected function get_items(\Aimeos\Base\View\Iface $view, Server_Request_Interface $request, Response_Interface $response): \Psr\Http\Message\Response_Interface
    {
        $manager = \Aimeos\M_Shop::create($this->context(), $this->get_path());
        if (($key = $view->param('aggregate')) !== null) {
            $search = $this->init_criteria($manager->filter(), $view->param());
            $view->data = $manager->aggregate($search, explode(',', $key), $view->param('value'), $view->param('type'));
            return $response;
        }
        $total = 1;
        $include = ($include = $view->param('include')) !== null ? explode(',', $include) : [];
        if (($id = $view->param('id')) == null) {
            $search = $this->init_criteria($manager->filter(), $view->param());
            $view->data = $manager->search($search, $include, $total);
            $view->child_items = $this->get_child_items($view->data, $include);
            $view->list_items = $this->get_list_items($view->data, $include);
        } else {
            $view->data = $manager->get($id, $include);
            $view->child_items = $this->get_child_items(map([$id => $view->data]), $include);
            $view->list_items = $this->get_list_items(map([$id => $view->data]), $include);
        }
        $view->ref_items = $this->get_ref_items($view->list_items);
        $view->total = $total;
        return $response;
    }
    /**
     * Saves new attributes for one or more items
     *
     * @param \Aimeos\Base\View\Iface $view View that will contain the "data" and "total" properties afterwards
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object
     * @param \Psr\Http\Message\ResponseInterface $response Response object
     * @return \Psr\Http\Message\ResponseInterface Modified response object
     * @throws \Aimeos\Admin\JsonAdm\Exception If "id" parameter isn't available or the body is invalid
     */
    protected function patch_items(\Aimeos\Base\View\Iface $view, Server_Request_Interface $request, Response_Interface $response): \Psr\Http\Message\Response_Interface
    {
        $body = (string) $request->get_body();
        if (($payload = json_decode($body)) === null || !isset($payload->data)) {
            throw new \Aimeos\Admin\Json_Adm\Exception('Invalid JSON in body', 400);
        }
        $manager = \Aimeos\M_Shop::create($this->context(), $this->get_path());
        if (is_array($payload->data)) {
            $data = $this->save_data($manager, $payload);
            $view->data = $data;
            $view->total = count($data);
            $response = $response->with_header('Content-Type', 'application/vnd.api+json; ext="bulk"; supported-ext="bulk"');
        } elseif (($id = $view->param('id')) != null) {
            $payload->data->id = $id;
            $data = $this->save_entry($manager, $payload->data);
            $view->data = $data;
            $view->total = 1;
        } else {
            throw new \Aimeos\Admin\Json_Adm\Exception('No ID given', 400);
        }
        return $response;
    }
    /**
     * Creates one or more new items
     *
     * @param \Aimeos\Base\View\Iface $view View that will contain the "data" and "total" properties afterwards
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object
     * @param \Psr\Http\Message\ResponseInterface $response Response object
     * @return \Psr\Http\Message\ResponseInterface Modified response object
     */
    protected function post_items(\Aimeos\Base\View\Iface $view, Server_Request_Interface $request, Response_Interface $response): \Psr\Http\Message\Response_Interface
    {
        $body = (string) $request->get_body();
        if (($payload = json_decode($body)) === null || !isset($payload->data)) {
            throw new \Aimeos\Admin\Json_Adm\Exception('Invalid JSON in body', 400);
        }
        if (isset($payload->data->id) || $view->param('id') != null) {
            throw new \Aimeos\Admin\Json_Adm\Exception('Client generated IDs are not supported', 403);
        }
        $manager = \Aimeos\M_Shop::create($this->context(), $this->get_path());
        if (is_array($payload->data)) {
            $data = $this->save_data($manager, $payload);
            $view->data = $data;
            $view->total = count($data);
            $response = $response->with_header('Content-Type', 'application/vnd.api+json; ext="bulk"; supported-ext="bulk"');
        } else {
            $payload->data->id = null;
            $data = $this->save_entry($manager, $payload->data);
            $view->data = $data;
            $view->total = 1;
        }
        return $response;
    }
}