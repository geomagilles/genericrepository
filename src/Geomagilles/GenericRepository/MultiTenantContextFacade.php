<?php
/**
 * This file is part of the GenericRepository framework.
 *
 * (c) Gilles Barbier <geomagilles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geomagilles\GenericRepository;

use Illuminate\Support\Facades\Facade;

class MultiTenantContextFacade extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Geomagilles\GenericRepository\MultiTenantContextInterface';
    }
}
