<?php declare(strict_types=1);

namespace Crate\Core\Contracts;

use Citrus\Http\Request;
use Citrus\Http\Response;

/**
 * The RestPatchController declares an additional and completely optional REST
 * design provided by Crate. You SHOULD not use this class without also 
 * implementing the main RestController interface as well.
 * 
 * PATCH    /[route]/[identifier]   -> patch ($identifier)
 * Patches the passed resource using just the information provided within the 
 * request. Crate itself does not force you to ensure a fully resource body on 
 * PUT / POST, so you omit this method if you would like.
 */
interface RestPatchControllerContract
{

    /**
     * PATCH /[route]/[identifier]
     *
     * @param Request $request
     * @return Response
     */
    public function patch(Request $request, array $args = []): Response;

}
