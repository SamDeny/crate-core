<?php declare(strict_types=1);

namespace Crate\Core\Models;

use Citrus\Exceptions\RuntimeException;
use Crate\Core\Concerns\ModelConcern;

class User extends ModelConcern
{

    /**
     * Users are stored on Crate's database
     *
     * @var string
     */
    protected string $driver = 'crate';

    /**
     * Check User Privileges
     *
     * @param string $permission
     * @param string|null $cls
     * @return boolean
     */
    public function can(string $permission, ?string $cls = null)
    {

        $action = 'view' | 'create' | 'update' | 'delete';
        $rule = 'collections' | 'collections:<collection>';

        if (is_null($cls)) {
            [$action, $rule, $type] = array_pad(explode(':', $permission, 3), 3, null);
        } else {
            $action = $permission;
            $rule = $cls;
            $type = $cls;
        }

    }

    /**
     * Check User Privileges
     *
     * @param string $permission
     * @param string|null $cls
     * @return boolean
     */
    public function cannot(string $permission, ?string $cls = null)
    {
        return !$this->can($permission, $cls);
    }
    
    /**
     * Set User Password
     *
     * @param string $value
     * @return void
     */
    public function setPassword(string $value)
    {
        $security = config('crate.security');
        $algo = $security['algorithms'][0];
        $config = $security[$algo];

        $this->attributes['password'] = password_hash($value, $algo, $config);
    }

}
