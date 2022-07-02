<?php declare(strict_types=1);

namespace Crate\Core\Database\Migrations\Properties;

abstract class NumberProperty extends Property
{

    /**
     * Set NumberProperty minimum value.
     *
     * @return self
     */
    public function min(int|float $num): self
    {
        $this->min = $this->type === 'double'? floatval($num): intval($num);
        return $this;
    }
    
    /**
     * Set NumberProperty maximum value.
     *
     * @return self
     */
    public function max(int|float $num): self
    {
        $this->max = $this->type === 'double'? floatval($num): intval($num);
        return $this;
    }

    /**
     * Set IntegerProperty unsigned state
     *
     * @return self
     */
    public function unsigned(): self
    {
        $this->unsigned = true;
        return $this;
    }

    /**
     * Set IntegerProperty signed state
     *
     * @return self
     */
    public function signed(): self
    {
        $this->unsigned = false;
        return $this;
    }

}
