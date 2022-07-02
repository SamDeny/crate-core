<?php declare(strict_types=1);

class Repository
{

    /**
     * Repository Scheme
     *
     * @var Scheme
     */
    protected Scheme $scheme;

    /**
     * Repository Branch
     *
     * @var string
     */
    protected string $branch;

    /**
     * Create a new Repository instance.
     *
     * @param string|Scheme $scheme The desired Repository scheme. 
     * @param string|null $branch The desires Repository branch or null to set 
     *                    the default branch for this repository access.
     */
    public function __construct(string|Scheme $scheme, ?string $branch = null)
    {
        if (is_string($scheme)) {
            if (($scheme = Scheme::get($scheme)) === null) {
                //@todo
                throw new \Exception("The passed scheme name '$scheme' does not exist.");
            }
        }
        $this->scheme = $scheme;

        if (!is_null($branch) && !$this->scheme->supports('branches')) {
            //@todo
            throw new \Exception("The passed scheme '$scheme' does not support different branches.");
        }
        $this->branch = $branch ?? $scheme->branch;
    }

    /**
     * Receive or Change Repository branch.
     *
     * @param string|null $branch The desired branch to switch to or null to 
     *                    get the current branch.
     * @return string|static
     */
    public function branch(?string $branch = null): string|static
    {
        if (is_null($branch)) {
            return $this->branch;
        } else {
            if (!$this->scheme->supports('branches')) {
                //@todo
                throw new \Exception("The passed scheme '{$this->scheme->name}' does not support different branches.");
            }

            if ($branch === $this->branch) {
                $self = clone $this;
                return $self;
            } else {
                $self = clone $this;
                $self->branch = $branch;
                return $self;
            }
        }
    }

    /**
     * Select a single document by passing it's unique identifier.
     *
     * @param string $id
     * @return ?Document
     */
    public function select(string $id): ?Document
    {
        $query = new Query;
        $query->where($this->scheme->primaryKey, $id);
        $query->limit(1);
        
        $result = $this->driver->selectOne($query);
        if ($result) {
            $document = new Document($this->scheme);
            return $document->fill($result);
        } else {
            return null;
        }
    }

    /**
     * Select multiple documents by simple where clauses.
     *
     * @param array $where
     * @param integer $limit
     * @param integer $offset
     * @return Document[]
     */
    public function selectBy(array $where, int $limit = 0, int $offset = 0)
    {
        $query = new Query;
        $query->where($where);
        $query->limit($limit);
        $query->offset($offset);

        $results = $this->driver->select($query);
        if ($results) {
            $documents = array_map(fn($result) => (new Document($this->scheme))->fill($result), $results);
            return $documents;
        } else {
            return [];
        }
    }

    /**
     * Query multiple documents by an extended query instance.
     *
     * @param Query $query
     * @return Document[]
     */
    public function query(Query $query)
    {
        $results = $this->driver->select($query);
        if ($results) {
            $documents = array_map(fn($result) => (new Document($this->scheme))->fill($result), $results);
            return $documents;
        } else {
            return [];
        }
    }

    /**
     * Insert one or more documents.
     *
     * @param array|Document $documents
     * @return int
     */
    public function insert(array|Document $documents)
    {

    }

    /**
     * Update one or more documents.
     *
     * @param array|Document $documents
     * @return int
     */
    public function update(array|Document $documents)
    {

    }

    /**
     * Replace (or Insert) one or more documents.
     *
     * @return int
     */
    public function replace(array|Document $documents)
    {

    }

    /**
     * Delete one or more documents.
     *
     * @return int
     */
    public function delete(array|Document $documents)
    {

    }

}
