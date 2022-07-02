<?php declare(strict_types=1);

namespace Crate\Core\Contracts;

use Citrus\Http\Request;
use Citrus\Http\Response;

/**
 * The RESTController declares the REST design used by Crate. Crate describes 
 * the HTTP Methods as follows and binds them to the respective methods.
 * 
 * ---
 * The following methods / routes are required when using the RestController.
 * ---
 * 
 *  GET     /[route]                -> list ()
 * List resources, use additional GET parameters to declare the offset, limit, 
 * sort / order and similar configurations.
 * 
 *  GET     /[route]/identifier     -> get ($identifier)
 * Gets a specific resource or returns a fitting HTTP status code. You can also 
 * specific additional parameters to declare the passed identifier if you would 
 * like. (for example: /[route]/post-slug?via=slug) 
 * 
 * POST     /[route]                -> create()
 * Create a new resource, even twice when the same request has been made again
 * ... and again... and aga... You get the point.
 * 
 * POST     /[route]/[identifier]   -> update ($identifier)
 * Update the passed resource or throw an respective HTTP status error code, 
 * when the identifier is invalid.
 * 
 * PUT      /[route]/[identifier]?  -> createOrUpdate ($identifier = null)
 * When the identifier is missing... just create the resource once, and only 
 * once (not like POST). When the identifier is present... update the resoure 
 * when it does already exist, otherwise create it using the passed identifier 
 * or throw an HTTP status code when the identifier is in an invalid format.
 * 
 * DELETE   /[route]/[identifier]   -> delete ($identifier)
 * Delete the passed resource, that's it... no special point here.
 * 
 * 
 * ---
 * The following methods / routes are optional and provided via:
 *      /Contracts/Controllers/RestPatchController
 *      /Contracts/Controllers/RestFindController
 *      /Contracts/Controllers/RestBulkController
 * 
 * Just implement the interfaces you need, NEXT to the main RestController!
 * ---
 * 
 * PATCH    /[route]/[identifier]   -> patch ($identifier)
 * Patches the passed resource using just the information provided within the 
 * request. Crate itself does not force you to ensure a fully resource body on 
 * PUT / POST, so you omit this method if you would like.
 * 
 * GET|POST /[route]/find           -> find ()
 * Similar to the list method, but also supports POST HTTP requests. Of course, 
 * you can also stay with the list method, if you're cool with GET requests 
 * only, otherwise take this. 
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
interface RestControllerContract extends ControllerContract
{

    /**
     * GET /[route]
     *
     * @param Request $request
     * @return Response
     */
    public function list(Request $request): Response;

    /**
     * GET /[route]/[identifier]
     *
     * @param Request $request
     * @param array $args
     * @return Response
     */
    public function get(Request $request, array $args = []): Response;

    /**
     * POST /[route]
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response;

    /**
     * POST /[route]/[identifier]
     *
     * @param Request $request
     * @param array $args
     * @return Response
     */
    public function update(Request $request, array $args): Response;

    /**
     * PUT /[route]/[identifier]?
     *
     * @param Request $request
     * @param array $args
     * @return Response
     */
    public function createOrUpdate(Request $request, array $args = []): Response;

    /**
     * DELETE /[route]/[identifier]
     *
     * @param Request $request
     * @param array $args
     * @return Response
     */
    public function delete(Request $request, array $args): Response;

}
