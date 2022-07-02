<?php declare(strict_types=1);

use Citrus\Utilities\Str;

class Document
{

    /**
     * Create a new Empty Document.
     *
     * @param Scheme|null $scheme
     */
    public function __construct(?Scheme $scheme = null)
    {
        
    }

    /**
     * Clone this Document.
     *
     * @return void
     */
    public function __clone()
    {
        
    }

    /**
     * Get a Document Property value.
     *
     * @param string $name
     * @return mixed
     */
    public function __get(string $name): mixed
    {
        
    }

    /**
     * Set a Document Property value.
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set(string $name, mixed $value): void
    {
        
    }

    /**
     * Check if a Document Property exists.
     *
     * @param string $name
     * @return boolean
     */
    public function __isset(string $name): bool
    {
        
    }

    /**
     * Unset a Document Property.
     *
     * @param string $name
     * @return void
     */
    public function __unset(string $name): void
    {
        
    }

    /**
     * Get Document Type
     *
     * @return string Document type, either 'original' or 'translation'.
     */
    public function type(): string
    {

    }

    /**
     * Get Document State
     * The document state if either 'main', 'revision' or a custom value.
     *
     * @return string
     */
    public function state(): string
    {

    }

    public function revisions(): array
    {

    }

    public function rollback(): bool
    {

    }

    public function rollbackTo(Document $document): bool
    {

    }

}
