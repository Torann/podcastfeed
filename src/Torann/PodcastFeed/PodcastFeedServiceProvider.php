<?php namespace Torann\PodcastFeed;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class PodcastFeedServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('torann/podcastfeed');

        AliasLoader::getInstance()->alias('PodcastFeed', 'Torann\PodcastFeed\Facades\PodcastFeed');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('torann.podcastfeed', function ($app)
        {
            $config = $app->config->get('podcastfeed::config', array());

            return new Manager($config);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

}