<?php declare(strict_types=1);

namespace Crate\Core\Database;

class Query
{

    const OPERATORS = [
        '='         => 'eq',
        '=='        => 'eq',
        '!='        => 'neq',
        '<>'        => 'neq',
        '>'         => 'gt',
        '!<'        => 'gt',
        '>='        => 'gteq',
        '<'         => 'lt',
        '!>'        => 'lt',
        '<='        => 'lteq',
        'between'   => 'between',
        'in'        => 'in',
        'not in'    => 'not_in',
        'like'      => 'like',
        'glob'      => 'like_case',
        'is'        => 'eq',
        'is not'    => 'neq',
    ];

    /**
     * Select columns clause.
     *
     * @var string
     */
    protected array $columns = ['*'];

    /**
     * Where clauses.
     *
     * @var array
     */
    protected array $wheres = [];

    /**
     * Order clauses
     *
     * @param array $operator
     */
    protected array $orders = [];

    /**
     * Offset clause.
     *
     * @var integer|null
     */
    protected ?int $offset = null;

    /**
     * Limit clause.
     *
     * @var integer|null
     */
    protected ?int $limit = null;

    /**
     * Tokenize Operator.
     *
     * @param string $operator
     * @return ?string
     */
    protected function operatorToken(string $operator): ?string
    {
        $operator = strtolower($operator);

        if (in_array($operator, self::OPERATORS)) {
            return $operator;
        } else if (array_key_exists($operator, self::OPERATORS)) {
            return self::OPERATORS[$operator];
        } else {
            return null;
        }
    }

    /**
     * Create a new Query instance
     */
    public function __construct()
    {
        
    }

    /**
     * Set Select columns clause.
     *
     * @param array|string $columns
     * @return self
     */
    public function select(array|string $columns): self
    {
        $this->select = is_string($columns)? [$columns]: $columns;
        return $this;
    }

    /**
     * Add where clause
     *
     * @return self
     */
    public function where(string $column, string $value_or_operator, ?string $value = null): self
    {
        if (is_null($value)) {
            $operator = 'eq';
            $value = $value_or_operator;
        } else {
            if (($operator = $this->operatorToken($value_or_operator)) === null) {
                //@todo
            }
        }

        $this->wheres[] = [$column, $operator, $value];
        return $this;
    }

    /**
     * Add order clause.
     *
     * @param string $column
     * @param string $direction
     * @return self
     */
    public function order(string $column, string $direction = 'ASC'): self
    {
        $direction = strtoupper($direction);
        $this->orders[] = [$column, $direction !== 'DESC'? 'ASC': 'DESC'];
        return $this;
    }

    /**
     * Set Offset clause.
     *
     * @param int $offset
     * @return self
     */
    public function offset(int $offset): self
    {
        $this->offset = $offset < 0? null: $offset;
        return $this;
    }

    /**
     * Set Limit clause.
     *
     * @param int $limit
     * @return self
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit < 0? null: $limit;
        return $this;
    }

}
