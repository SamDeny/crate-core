<?php declare(strict_types=1);

namespace Crate\Core\Controllers;

use Citrus\Http\Request;
use Citrus\Http\Response;
use Crate\Core\Contracts\RestControllerContract;
use Crate\Core\Contracts\RestFindControllerContract;
use Crate\Core\Contracts\RestPatchControllerContract;

class PrivilegesController implements 
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
     * GET /privileges
     *
     * @param Request $request
     * @return Response
     */
    public function list(Request $request): Response
    {
        return (new Response)->setJSON(['method' => 'list']);
    }

    /**
     * GET /privileges/[identifier]
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
     * GET|POST /privileges/find
     *
     * @param Request $request
     * @return Response
     */
    public function find(Request $request): Response
    {
        return (new Response)->setJSON(['method' => 'find']);
    }

    /**
     * POST /privileges
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        return (new Response)->setJSON(['method' => 'create']);
    }

    /**
     * POST /privileges/[identifier]
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
     * PUT /privileges/[identifier]?
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
     * DELETE /privileges/[identifier]
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
     * PATCH /privileges/[identifier]?
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
