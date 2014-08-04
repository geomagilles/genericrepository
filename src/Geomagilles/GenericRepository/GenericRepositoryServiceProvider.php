<?php namespace Geomagilles\GenericRepository;

use Illuminate\Support\ServiceProvider;

class GenericRepositoryServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('Geomagilles\GenericRepository\MultiTenantContextInterface', function ($app) {
            return new MultiTenantContext();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('Geomagilles\GenericRepository\MultiTenantContextInterface');
    }

}
