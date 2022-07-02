<?php declare(strict_types=1);

/**
 * The Structure class allows you to create new Schemes, either by simple 
 * creating and handling all properties or by importing existing scheme 
 * declaration files as PHP or JSON.
 */
class Structure
{

    /**
     * Import a JSON or PHP Scheme declaration file.
     *
     * @param string $filepath
     * @param string|null $name
     * @return ?Scheme
     */
    static public function importFile(string $filepath, ?string $name = null): ?Scheme
    {
        if (!file_exists($filepath) || !is_file($filepath)) {
            //@todo
            throw new \Exception('');
        }

        if (pathinfo($filepath, \PATHINFO_EXTENSION) === 'json') {
            try {
                $content = json_decode(file_get_contents($filepath), true);
            } catch(\Exception $e) {
                //@todo 
                throw new \Exception('');
            }
            $content = self::parseJsonScheme($content);
        } else if (pathinfo($filepath, \PATHINFO_EXTENSION) === 'php') {
            $handle = fopen($filepath, 'r');
            $header = fgets($handle);
            $content = fread($handle, filesize($filepath) - ftell($handle));

            // Validate PHP declaration file
            $valid = false;
            if (strpos($header, '#') === 0) {
                $details = explode(',', substr($header, 1));

                if ($details[0] === 'public' && $details[1] === sha1($content) && strpos($content, '<?php') === 0) {
                    $valid = true;
                }
            }
            if (!$valid) {
                //@todo
                throw new \Exception('');
            }

            // Include PHP Part of declaration file (necessary for CLI env)
            $tmp = tempnam(path(':temp'), 'SCHEME');
            file_put_contents($tmp, $content);
            $content = include $tmp;
            unlink($tmp);
        }

        if (isset($content) && is_array($content)) {
            return self::import($name, $content);
        } else {
            //@todo
            throw new \Exception('');
        }
    }

    /**
     * Import a Scheme declaration.
     *
     * @param string $name
     * @param array $scheme
     * @return void
     */
    static public function import(string $name, array $scheme)
    {

    }


}
