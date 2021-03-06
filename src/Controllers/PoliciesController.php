<?php declare(strict_types=1);

namespace Crate\Core\Controllers;

use Citrus\Http\Request;
use Citrus\Http\Response;
use Crate\Core\Contracts\RestControllerContract;
use Crate\Core\Contracts\RestFindControllerContract;
use Crate\Core\Contracts\RestPatchControllerContract;

class PoliciesController implements 
    RestControllerContract, 
    RestFindControllerContract, 
    RestPatchControllerContract
{

    /**
     * Create a new PoliciesController instance.
     */
    public function __construct()
    {
        
    }
    /**
     * GET /policies
     *
     * @param Request $request
     * @return Response
     */
    public function list(Request $request): Response
    {
        return (new Response)->setJSON(['method' => 'list']);
    }

    /**
     * GET /policies/[identifier]
     *
     * @param Request $request
     * @param array $args
     * @return Response
     */
    public function get(Request $request, array $args = []): Response
    {
        return (new Response)->setJSON(['method' => 'get']);
    }

    /**
     * GET|POST /policies/find
     *
     * @param Request $request
     * @return Response
     */
    public function find(Request $request): Response
    {
        return (new Response)->setJSON(['method' => 'find']);
    }

    /**
     * POST /policies
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        return (new Response)->setJSON(['method' => 'create']);
    }

    /**
     * POST /policies/[identifier]
     *
     * @param Request $request
     * @param array $args
     * @return Response
     */
    public function update(Request $request, array $args): Response
    {
        return (new Response)->setJSON(['method' => 'update']);
    }

    /**
     * PUT /policies/[identifier]?
     *
     * @param Request $request
     * @param array $args
     * @return Response
     */
    public function createOrUpdate(Request $request, array $args = []): Response
    {
        return (new Response)->setJSON(['method' => 'createOrUpdate']);
    }

    /**
     * DELETE /policies/[identifier]
     *
     * @param Request $request
     * @param array $args
     * @return Response
     */
    public function delete(Request $request, array $args): Response
    {
        return (new Response)->setJSON(['method' => 'delete']);
    }
    
    /**
     * PATCH /policies/[identifier]?
     *
     * @param Request $request
     * @param array $args
     * @return Response
     */
    public function patch(Request $request, array $args = []): Response
    {
        return (new Response)->setJSON(['method' => 'patch']);
    }

}
