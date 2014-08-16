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

use IteratorAggregate;
use StdClass;
use Geomagilles\GenericRepository\MultiTenantContextInterface;
use Illuminate\Database\Eloquent\Builder;

abstract class GenericMultiTenantRepository extends BaseRepository implements GenericRepositoryInterface
{
    /**
     * The multi tenant context
     *
     * @var Geomagilles\GenericRepository\MultiTenantContextInterface
     */
    protected $multiTenantContext;

    public function wrap($data)
    {
        if (is_array($data) || ($data instanceof IteratorAggregate)) {
            $instances = array();
            foreach ($data as $key => $instance) {
                $instances[$key] = $this->wrap($instance);
            }
            return $instances;
        } else {
            return is_null($data) ? null : new static($data, $this->multiTenantContext);
        }
    }

    public function create(array $data = array())
    {
        $entity = self::wrap($this->model->create($this->match($data)));
        // multi-tenant management
        if ($this->multiTenantContext->hasTenant()) {
            $column = $this->multiTenantContext->getTenantKey();
            if (! isset($data[$column])) {
                $entity->set('set'.$column, $this->multiTenantContext->getTenantId());
                $entity->save();
            }
        }
        
        return $entity;
    }

    public function getAll(array $with = array())
    {
        $query = $this->tenantColumn($this->make($with));
        
        return self::wrap($query->get());
    }
 
    public function getById($id, array $with = array())
    {
        $query = $this->tenantColumn($this->make($with));
  
        return self::wrap($query->find($id));
    }

    public function getByPage($page = 1, $limit = 10, array $with = array())
    {
        $result             = new StdClass;
        $result->page       = $page;
        $result->limit      = $limit;
        $result->totalItems = 0;
        $result->items      = array();
    
        $query = $this->tenantColumn($this->make($with));
    
        $items = $query->skip($limit * ($page - 1))
                       ->take($limit)
                       ->get();
    
        $result->totalItems = $this->model->count();
        $result->items      = self::wrap($items);
    
        return $result;
    }

    public function deleteById($id, array $with = array())
    {
        $query = $this->tenantColumn($this->make($with));
  
        return $query->destroy($id);
    }

    public function getFirstBy($key, $value, array $with = array())
    {
        $key = $this->match($key);
        $query = $this->tenantColumn($this->make($with));
    
        return self::wrap($query->where($key, '=', $value)->first());
    }

    public function getManyBy($key, $value, array $with = array())
    {
        $key = $this->match($key);
        $query = $this->tenantColumn($this->make($with));
         
        return self::wrap($query->where($key, '=', $value)->get());
    }

    public function deleteFirstBy($key, $value)
    {
        $key = $this->match($key);
        $query = $this->tenantColumn($this->make($with));
    
        return self::wrap($query->where($key, '=', $value)->take(1)->delete());
    }

    public function deleteManyBy($key, $value)
    {
        $key = $this->match($key);
        $query = $this->tenantColumn($this->make($with));
    
        return self::wrap($query->where($key, '=', $value)->delete());
    }
    
    /**
     * Scope a query based upon a column name
     *
     * @param Illuminate\Database\Eloquent\Builder
     * @return Illuminate\Database\Eloquent\Builder
     */
    protected function tenantColumn(Builder $model)
    {
        if ($this->multiTenantContext->hasTenant()) {
            $column = $this->match($this->multiTenantContext->getTenantKey());
            return $model->where($column, '=', $this->multiTenantContext->getTenantId());
        }
        return $model;
    }
}
