<?php declare(strict_types=1);

namespace Crate\Core\Controllers;

use Citrus\Http\Request;
use Citrus\Http\Response;
use Crate\Core\Contracts\RestControllerContract;
use Crate\Core\Contracts\RestFindControllerContract;
use Crate\Core\Contracts\RestPatchControllerContract;

class UsersController implements 
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
     * GET /users
     *
     * @param Request $request
     * @return Response
     */
    public function list(Request $request): Response
    {
        return (new Response)->setJSON(['method' => 'list']);
    }

    /**
     * GET /users/[identifier]
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
     * GET|POST /users/find
     *
     * @param Request $request
     * @return Response
     */
    public function find(Request $request): Response
    {
        return (new Response)->setJSON(['method' => 'find']);
    }

    /**
     * POST /users
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        return (new Response)->setJSON(['method' => 'create']);
    }

    /**
     * POST /users/[identifier]
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
     * PUT /users/[identifier]?
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
     * DELETE /users/[identifier]
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
     * PATCH /users/[identifier]?
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
