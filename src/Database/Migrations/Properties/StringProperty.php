<?php declare(strict_types=1);

namespace Crate\Core\Database\Migrations\Properties;

class StringProperty extends Property
{

    /**
     * Create a new Scheme StringProperty
     *
     * @param string $name
     * @param string $type
     */
    public function __construct(string $name)
    {
        parent::__construct($name, 'string');
    }

    /**
     * Set StringProperty possible length
     *
     * @param integer $min_or_exact
     * @param integer $max
     * @return self
     */
    public function length(int $min_or_exact = 0, int $max = 0): self
    {
        if ($max <= 0) {
            $this->length = $min_or_exact;
        } else {
            $this->minLength = $min_or_exact;
            $this->maxLength = $max;
        }
        return $this;
    }

    /**
     * Set StringProperty possible values
     *
     * @param array $values
     * @return self
     */
    public function enum(array $values): self
    {
        $this->enums = $values;
        return $this;
    }

    /**
     * Set StringProperty unique state
     *
     * @return self
     */
    public function unique(): self
    {
        $this->unique = true;
        return $this;
    }

}
