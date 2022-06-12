<?php declare(strict_types=1);

namespace Crate\Core\Contracts;

use Citrus\Http\Request;
use Citrus\Http\Response;

/**
 * The RestFindController declares an additional and completely optional REST
 * design provided by Crate. You SHOULD not use this class without also 
 * implementing the main RestController interface as well.
 * 
 * GET|POST /[route]/find           -> find ()
 * Similar to the list method, but also supports POST HTTP requests. Of course, 
 * you can also stay with the list method, if you're cool with GET requests 
 * only, otherwise take this. 
 */
interface RestFindControllerContract
{

    /**
     * GET|POST /[route]/[identifier]
     *
     * @return Response
     */
    public function find(Request $request): Response;

}
