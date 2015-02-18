# Podcast Generator for Laravel

Generate a RSS feed for podcast for Laravel 4.

----------

## Installation

- [Podcast on Packagist](https://packagist.org/packages/torann/podcast)
- [Podcast on GitHub](https://github.com/torann/laravel-podcast)

To get the latest version of Moderate simply require it in your `composer.json` file.

~~~
"torann/podcastfeed": "0.1.*@dev"
~~~

You'll then need to run `composer install` to download it and have the autoloader updated.

Once Moderate is installed you need to register the service provider with the application. Open up `app/config/app.php` and find the `providers` key.

~~~php
'providers' => array(

    'Torann\PodcastFeed\PodcastFeedServiceProvider',

)
~~~

> There is no need to add the Facade, the package will add it for you.

### Publish the config

Run this on the command line from the root of your project:

~~~
$ php artisan config:publish torann/podcastfeed
~~~

This will publish Moderate's config to `app/config/packages/torann/podcastfeed/`.


## Methods

**setHeader**
The header of the feed can be set in the config file or manually using the `setHeader` method:

```php
PodcastFeed::setHeader(array(
    'title'       => 'All About Everything',
    'subtitle'    => 'A show about everything',
    'description' => 'Great site description',
    'link'        => 'http://www.example.com/podcasts/everything/index.html',
    'image'       => 'http://example.com/podcasts/everything/AllAboutEverything.jpg',
    'author'      => 'John Doe',
    'email'       => 'john.doe@example.com',
    'category'    => 'Technology',
    'language'    => 'en-us',
    'copyright'   => '2014 John Doe & Family',
));
```

**addMedia**
Adding media to the feed.

```php
foreach($this->podcastRepository->getPublished() as $podcast)
{
    PodcastFeed::addMedia([
        'title'       => $podcast->title,
        'description' => $podcast->title,
        'publish_at'  => $podcast->publish_at,
        'guid'        => route('podcast.show', $podcast->slug),
        'url'         => $podcast->media->url(),
        'type'        => $podcast->media_content_type,
        'duration'    => $podcast->duration,
        'image'       => $podcast->image->url(),
    ]);
}
```

**toString**
Converting feed to a presentable string. The example below was pulled from a controller. In theory you could implement a caching method so that it doesn't have to render each time.

```php
public function index()
{
    foreach($this->podcastRepository->getPublished() as $podcast)
    {
        PodcastFeed::addMedia([
            'title'       => $podcast->title,
            'description' => $podcast->title,
            'publish_at'  => $podcast->publish_at,
            'guid'        => route('podcast.show', $podcast->slug),
            'url'         => $podcast->media->url(),
            'type'        => $podcast->media_content_type,
            'duration'    => $podcast->duration,
            'image'       => $podcast->image->url(),
        ]);
    }

    return Response::make(PodcastFeed::toString())
        ->header('Content-Type', 'text/xml');
}
```

## Change Log

#### v0.1.0

- First release