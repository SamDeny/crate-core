<?php declare(strict_types=1);

namespace Crate\Core\Controllers;

use Citrus\Http\Request;
use Citrus\Http\Response;
use Crate\Core\Contracts\RestBulkControllerContract;
use Crate\Core\Contracts\RestControllerContract;
use Crate\Core\Contracts\RestFindControllerContract;
use Crate\Core\Contracts\RestPatchControllerContract;

class UsersController implements 
    RestControllerContract, 
    RestBulkControllerContract, 
    RestFindControllerContract, 
    RestPatchControllerContract
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
     * @param Request $request
     * @return Response
     */
    public function find(Request $request): Response
    {
        $response = new Response();
        return $response;
    }

    /**
     * GET /[route]
     *
     * @param Request $request
     * @return Response
     */
    public function list(Request $request): Response
    {
        $response = new Response();
        $response->setJSON(['test']);
        return $response;
    }

    /**
     * GET /[route]/[identifier]
     *
     * @param Request $request
     * @param array $args
     * @return Response
     */
    public function get(Request $request, array $args = []): Response
    {
        $response = new Response();
        return $response;
    }

    /**
     * POST /[route]
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        $response = new Response();
        return $response;
    }

    /**
     * POST /[route]/[identifier]
     *
     * @param Request $request
     * @param array $args
     * @return Response
     */
    public function update(Request $request, array $args): Response
    {
        $response = new Response();
        return $response;
    }

    /**
     * PATCH /[route]/[identifier]
     *
     * @param Request $request
     * @param array $args
     * @return Response
     */
    public function patch(Request $request, array $args = []): Response
    {
        $response = new Response();
        return $response;
    }

    /**
     * PUT /[route]/:uuid?
     *
     * @param Request $request
     * @param array $args
     * @return Response
     */
    public function createOrUpdate(Request $request, array $args = []): Response
    {
        $response = new Response();
        return $response;
    }

    /**
     * DELETE /[users]/:uuid
     *
     * @param Request $request
     * @param array $args
     * @return Response
     */
    public function delete(Request $request, array $args): Response
    {
        $response = new Response();
        return $response;
    }

    /**
     * POST /[route]/bulkGet
     *
     * @param Request $request
     * @return Response
     */
    public function bulkGet(Request $request): Response
    {
        $response = new Response();
        return $response;
    }

    /**
     * POST|PUT|PATCH /[route]/bulkPost
     *
     * @param Request $request
     * @return Response
     */
    public function bulkPost(Request $request): Response
    {
        $response = new Response();
        return $response;
    }

    /**
     * POST|DELETE /[route]/bulkDelete
     *
     * @param Request $request
     * @return Response
     */
    public function bulkDelete(Request $request): Response
    {
        $response = new Response();
        return $response;
    }

}
