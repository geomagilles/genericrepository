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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

abstract class GenericRepository extends BaseRepository implements GenericRepositoryInterface
{
    public function wrap($model)
    {
        if (is_array($model) || ($model instanceof IteratorAggregate)) {
            $instances = array();
            foreach ($model as $key => $instance) {
                $instances[$key] = $this->wrap($instance);
            }
            return $instances;
        } else {
            return is_null($model) ? null : new static($model);
        }
    }

    public function create(array $data = array())
    {
        return self::wrap($this->model->create($this->match($data)));
    }

    public function delete()
    {
        return $this->model->delete();
    }

    public function getAll(array $with = array())
    {
        $query = $this->make($with);
        
        return self::wrap($query->get());
    }
 
    public function getById($id, array $with = array())
    {
        $query = $this->make($with);
  
        return self::wrap($query->find($id));
    }

    public function getByPage($page = 1, $limit = 10, array $with = array())
    {
        $result             = new StdClass;
        $result->page       = $page;
        $result->limit      = $limit;
        $result->totalItems = 0;
        $result->items      = array();
    
        $query = $this->make($with);
    
        $items = $query->skip($limit * ($page - 1))
                       ->take($limit)
                       ->get();
    
        $result->totalItems = $this->model->count();
        $result->items      = self::wrap($items);
    
        return $result;
    }
 
    public function deleteById($id, array $with = array())
    {
        $query = $this->make($with);
  
        return $query->destroy($id);
    }

    public function getFirstBy($key, $value, array $with = array())
    {
        $key = $this->match($key);
        $query = $this->make($with);
    
        return self::wrap($query->where($key, '=', $value)->first());
    }

    public function getManyBy($key, $value, array $with = array())
    {
        $key = $this->match($key);
        $query = $this->make($with);
         
        return self::wrap($query->where($key, '=', $value)->get());
    }

    public function deleteFirstBy($key, $value)
    {
        $key = $this->match($key);
        $query = $this->make($with);
    
        return self::wrap($query->where($key, '=', $value)->take(1)->delete());
    }

    public function deleteManyBy($key, $value)
    {
        $key = $this->match($key);
        $query = $this->make($with);
    
        return self::wrap($query->where($key, '=', $value)->delete());
    }
}
