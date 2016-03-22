# Podcast Generator for Laravel

Generate a RSS feed for podcast for Laravel 5.

## Installation

- [Podcast on Packagist](https://packagist.org/packages/torann/podcastfeed)
- [Podcast on GitHub](https://github.com/torann/podcastfeed)


From the command line run

```
$ composer require torann/podcastfeed
```

### Setup

Once installed you need to register the service provider with the application. Open up `config/app.php` and find the `providers` key.

```php
'providers' => [
    ...

    Torann\PodcastFeed\PodcastFeedServiceProvider::class,

    ...
]
```

This package also comes with a facade, which provides an easy way to call the the class. Open up `config/app.php` and find the `aliases` key

```php
'aliases' => [
    ...

    'PodcastFeed' => Torann\PodcastFeed\Facades\PodcastFeed::class,

    ...
];
```

### Publish the configurations

Run this on the command line from the root of your project:

```
$ php artisan vendor:publish --provider="Torann\PodcastFeed\PodcastFeedServiceProvider"
```

A configuration file will be publish to `config/podcast-feed.php`.

## Methods

**setHeader**
The header of the feed can be set in the config file or manually using the `setHeader` method:

```php
PodcastFeed::setHeader([
    'title'       => 'All About Everything',
    'subtitle'    => 'A show about everything',
    'description' => 'Great site description',
    'link'        => 'http://www.example.com/podcasts/everything/index.html',
    'image'       => 'http://example.com/podcasts/everything/AllAboutEverything.jpg',
    'author'      => 'John Doe',
    'email'       => 'john.doe@example.com',
    'category'    => 'Technology',
    'language'    => 'en-us',
    'copyright'   => '2016 John Doe & Family',
]);
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
    foreach($this->podcastRepository->getPublished() as $podcast) {
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

#### v0.2.1

- Fixes foreign characters like 'æ', 'ø' and 'å'

#### v0.2.0

- Support Laravel 5


#### v0.1.0

- First release