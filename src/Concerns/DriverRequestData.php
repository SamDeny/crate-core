<?php declare(strict_types=1);

namespace Crate\Core\Concerns;

trait DriverRequestData {

    /**
     * SQL Query / Statement of the last operation
     *
     * @var string|null
     */
    protected ?string $lastSQL = null;

    /**
     * Reqest detauls of the last operation
     *
     * @var array
     */
    protected array $lastRequest = [
        'errorCode'     => 0,
        'errorMessage'  => null,
        'insertIds'     => [],
        'result'        => null,
    ];
    
    /**
     * Get the SQL statement of the last operation.
     *
     * @return ?string
     */
    public function getSQL(): ?string
    {
        return $this->lastSQL;
    }

    /**
     * Get the [error_code, error_message] of the last operation, if any is occured.
     *
     * @return array|null
     */
    public function getError(): ?array
    {
        if ($this->lastRequest['errorCode'] !== 0) {
            return [$this->lastRequest['errorCode'], $this->lastRequest['errorMessage']];
        } else {
            return null;
        }
    }

    /**
     * Get the Id of the last operation.
     *
     * @return int
     */
    public function getId(): int
    {
        $length = count($this->lastRequest['insertIds']);
        return $length > 0? $this->lastRequest['insertIds'][$length-1]: 0;
    }

    /**
     * Get the Ids of the last operation.
     *
     * @return array
     */
    public function getIds(): array
    {
        return $this->lastRequest['insertIds'];
    }

    /**
     * Get the number of affected rows of the last operation.
     *
     * @return int
     */
    public function getAffectedRows(): int
    {
        return $this->lastRequest['affectedRows'] ?? count($this->lastRequest['insertIds']);
    }

    /**
     * Get the result message / instance of the last operation.
     *
     * @return mixed
     */
    public function getResult(): mixed
    {
        return $this->lastRequest['result'];
    }

}