<?php declare(strict_types=1);

use Citrus\Exceptions\RuntimeException;

class Scheme
{

    /**
     * Create a new Scheme instance.
     *
     * @param string $scheme
     * @param string|null $filepath
     */
    public function __construct(string $scheme, ?string $filepath = null)
    {
        if (is_null($filepath)) {
            $filepath = path(':data/schemes/', "$scheme.scheme.php");
        }

        $this->scheme = $scheme;
        $this->schemepath = $filepath;

        if (file_exists($this->schemepath)) {

            /*
                #scheme:$scheme,hash:$hash
                <?php return [...]; ?>
            */
            $this->unpack($this->schemepath);
        }
    }

    /**
     * Pack this Scheme instance.
     *
     * @return boolean
     */
    public function pack(): bool
    {
        if (!$this->store) {
            return false;   // Scheme-Storage has been disabled.
        }

        $export =  var_export($this->toArray(), true);
        $hashed = hash_hmac('sha256', $export, config('crate.secret'));
        $output = '<?php return ["'. $hashed .'", ' . $export . ']; ?>';
        file_put_contents($this->schemepath, $output);
    }

    /**
     * Unpack this Scheme instance.
     *
     * @return boolean
     */
    public function unpack(array $packed): bool
    {
        if (count($packed) !== 2) {
            throw new RuntimeException('');
        }

        if (hash_equals($packed[0], hash_hmac('sha256', $packed[1], )))

        $packed[1]
        $packed[0]
        $packed[1]
    }

}
