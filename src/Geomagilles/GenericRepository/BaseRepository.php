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

use Illuminate\Support\Str;

abstract class BaseRepository
{

    /**
     * Per default, all attributes are blocked from MassAssignement
     *
     * @var array
     */
    protected $guarded = array('*');

    /**
     * The eloquent model
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * The matching array between application keys and database keys
     *
     * @var array
     */
    protected static $matching = array();

    public function save()
    {
        $this->model->save();
    }

    public function update($data = array())
    {
        $this->model->update($this->match($data));
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getTable()
    {
        return $this->model->getTable();
    }

    public function getId()
    {
        return $this->model->id;
    }

    public function setObserver($observer)
    {
        $model = $this->model;
        $apply = function ($observer, $observerEvent, $modelEvent) use ($model) {
            $model->$modelEvent(
                function ($m) use ($observer, $observerEvent) {
                    return $observer->$observerEvent(static::wrap($m));
                });
        };
        if (method_exists($observer, 'creating')) {
            $apply($observer, 'creating', 'creating');
        }
        if (method_exists($observer, 'created')) {
            $apply($observer, 'created', 'created');
        }
        if (method_exists($observer, 'updating')) {
            $apply($observer, 'updating', 'updating');
        }
        if (method_exists($observer, 'updated')) {
            $apply($observer, 'updated', 'updated');
        }
        if (method_exists($observer, 'deleting')) {
            $apply($observer, 'deleting', 'deleting');
        }
        if (method_exists($observer, 'deleted')) {
            $apply($observer, 'deleted', 'deleted');
        }
        if (method_exists($observer, 'saving')) {
            $apply($observer, 'saving', 'saving');
        }
        if (method_exists($observer, 'saved')) {
            $apply($observer, 'saved', 'saved');
        }
        if (method_exists($observer, 'restoring')) {
            $apply($observer, 'restoring', 'restoring');
        }
    }

    /**
     * Make a new instance of the entity to query on
     *
     * @param array $with
     */
    protected function make(array $with = array())
    {
        return $this->model->with($with);
    }

    /**
     * Return model's attribute
     * @param $method (eg. 'getId')
     * @return mixed
     */
    protected function get($method)
    {
        $key = lcfirst(substr($method, 3));
        return $this->model->__get($this->match($key));
    }

    /**
     * Set model's attribute
     * @param $method (eg 'setId')
     * @param $d
     * @throws \Exception if unknown key
     * @return mixed
     */
    protected function set($method, $d)
    {
        $key = lcfirst(substr($method, 3));
        return $this->model->__set($this->match($key), $d);
    }

    /**
     * Matches application keys with database keys
     * @param string $key (eg. 'projectId')
     * @return string (eg. 'project_id')
     */
    protected function match($data)
    {
        if (is_array($data) || ($data instanceof Traversable)) {
            $new = array();
            foreach ($data as $key => $value) {
                $new[$this->match($key)] = $value;
            }
            return $new;
        } else {
            if (in_array($data, array_keys(static::$matching))) {
                return static::$matching[$data];
            } else {
                return Str::snake($data, '_');
            }
        }
    }
}
