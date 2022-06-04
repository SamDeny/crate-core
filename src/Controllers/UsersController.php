<?php declare(strict_types=1);

namespace Crate\Core\Controllers;

use Crate\Core\Contracts\RestController;
use Crate\Core\Contracts\RestBulkController;
use Crate\Core\Contracts\RestFindController;
use Crate\Core\Contracts\RestPatchController;
use Crate\Core\Http\Response;
use Psr\Http\Message\ResponseInterface;

class UsersController implements RestController, RestBulkController, RestFindController, RestPatchController
{

    /**
     * Create a new UsersController instance.
     */
    public function __construct()
    {
        
    }

    /**
     * GET|POST /users
     *
     * @return ResponseInterface
     */
    public function find(): ResponseInterface
    {
        $response = new Response();
        return $response;
    }

    /**
     * GET /[route]
     *
     * @return ResponseInterface
     */
    public function list(): ResponseInterface
    {
        $response = new Response();
        return $response;
    }

    /**
     * GET /[route]/[identifier]
     *
     * @return ResponseInterface
     */
    public function get($identifier): ResponseInterface
    {
        $response = new Response();
        return $response;
    }

    /**
     * POST /[route]
     *
     * @return ResponseInterface
     */
    public function create(): ResponseInterface
    {
        $response = new Response();
        return $response;
    }

    /**
     * POST /[route]/[identifier]
     *
     * @param mixed $identifier
     * @return ResponseInterface
     */
    public function update($identifier): ResponseInterface
    {
        $response = new Response();
        return $response;
    }

    /**
     * PATCH /[route]/[identifier]
     *
     * @return ResponseInterface
     */
    public function patch($identifier): ResponseInterface
    {
        $response = new Response();
        return $response;
    }

    /**
     * PUT /[route]/:uuid?
     *
     * @param mixed $identifier
     * @return ResponseInterface
     */
    public function createOrUpdate($identifier = null): ResponseInterface
    {
        $response = new Response();
        return $response;
    }

    /**
     * DELETE /[users]/:uuid
     *
     * @param mixed $identifier
     * @return ResponseInterface
     */
    public function delete($identifier): ResponseInterface
    {
        $response = new Response();
        return $response;
    }

    /**
     * POST /[route]/bulkGet
     *
     * @return ResponseInterface
     */
    public function bulkGet(): ResponseInterface
    {
        $response = new Response();
        return $response;
    }

    /**
     * POST|PUT|PATCH /[route]/bulkPost
     *
     * @return ResponseInterface
     */
    public function bulkPost(): ResponseInterface
    {
        $response = new Response();
        return $response;
    }

    /**
     * POST|DELETE /[route]/bulkDelete
     *
     * @return ResponseInterface
     */
    public function bulkDelete(): ResponseInterface
    {
        $response = new Response();
        return $response;
    }

}
