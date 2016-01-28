<?php

namespace Torann\PodcastFeed;

use Illuminate\Support\ServiceProvider;

class PodcastFeedServiceProvider extends ServiceProvider
{
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
        $this->publishes([
            __DIR__ . '/../../config/podcast-feed.php' => config_path('podcast-feed.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('torann.podcastfeed', function ($app) {
            $config = $app->config->get('podcast-feed', []);

            return new Manager($config);
        });

        // Merge config
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/podcast-feed.php', 'podcast-feed'
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}