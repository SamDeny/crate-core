<?php declare(strict_types=1);

namespace Crate\Core\Contracts;

use Psr\Http\Message\ResponseInterface;

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
interface RestPatchController
{

    /**
     * PATCH /[route]/[identifier]
     *
     * @return ResponseInterface
     */
    public function patch($identifier): ResponseInterface;

}
