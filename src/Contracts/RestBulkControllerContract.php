<?php declare(strict_types=1);

namespace Crate\Core\Contracts;

use Citrus\Http\Request;
use Citrus\Http\Response;


/**
 * The RestBulkController declares an additional and completely optional REST
 * design provided by Crate. You SHOULD not use this class without also 
 * implementing the main RestController interface as well.
 * 
 * POST     /[route]/bulkGet        -> bulkGet ()
 * Almost similar to the get method, but supports the POST HTTP requests method 
 * only. Using this method allows you to receive multiple resources by passing 
 * multiple identifiers within the body of the request.
 * 
 * P*       /[route]/bulkPost       -> bulkPost ()
 * Similar to the create() / update() methods, but allows you to create or 
 * update multiple resources in one go. That's pretty useful, and supports all 
 * three P's (POST, PUT and PATCH methods.)
 * 
 * POST|DELETE  /[route]/bulkDelete     -> bulkDelete ()
 * Similar to the delete() method but allows you to delete multiple ressources 
 * in one go. That's pretty useful, and can also be called using the DELETE 
 * method, since nobody said, that you cannot send a body on DELETE.
 */
interface RestBulkControllerContract
{

    /**
     * POST /[route]/bulkGet
     *
     * @param Request $request
     * @return Response
     */
    public function bulkGet(Request $request): Response;

    /**
     * POST|PUT|PATCH /[route]/bulkPost
     *
     * @param Request $request
     * @return Response
     */
    public function bulkPost(Request $request): Response;

    /**
     * POST|DELETE /[route]/bulkDelete
     *
     * @param Request $request
     * @return Response
     */
    public function bulkDelete(Request $request): Response;

}
