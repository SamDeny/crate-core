<?php declare(strict_types=1);

namespace Crate\Core\Contracts;

interface Parser
{

    /**
     * Parse the passed raw data and return the content as an array, object or
     * null when empty. Throw an [Parser]Exception when something went wrong.
     *
     * @param string $content
     * @param array $attributes Additional attributes for the underlying 
     *              Parser engine.
     * @return null|array|object
     */
    public function parse(string $content, array $attributes = []): null | array | object;

    /**
     * Parse the filepath content and return the content as an array, object or
     * null when empty. Throw an [Parser]Exception when something went wrong.
     *
     * @param string $filepath
     * @param array $attributes Additional attributes for the underlying 
     *              Parser engine.
     * @return null|array|object
     */
    public function parseFile(string $filepath, array $attributes = []): null | array | object;

    /**
     * Parse the stream content and return the content as an array, object or
     * null when empty. Throw an [Parser]Exception when something went wrong.
     *
     * @param string $resource
     * @param array $attributes Additional attributes for the underlying 
     *              Parser engine.
     * @return null|array|object
     */
    public function parseStream($resource, array $attributes = []): null | array | object;

    /**
     * Return the respective representation of the passed content or null when
     * empty- Throw an [Parser]Exception when something went wrong.
     *
     * @param mixed $content
     * @param array $attributes Additional attributes for the Parser engine.
     * @return null|string
     */
    public function emit(mixed $content, array $attributes = []);

    /**
     * Send the respective representation to the passed filepath, and return 
     * a boolean status. Throw an [Parser]Exception when something went wrong.
     *
     * @param mixed $content
     * @param string $filepath
     * @param array $attributes Additional attributes for the Parser engine.
     * @return boolean
     */
    public function emitFile(mixed $content, string $filepath, array $attributes = []);
    
}
