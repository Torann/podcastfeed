<?php

namespace Torann\PodcastFeed;

use DateTime;

class Manager
{
    /**
     * Package Config
     *
     * @var array
     */
    protected $config = [];

    /**
     * General title of the podcast
     *
     * @var string
     */
    private $title;

    /**
     * Subtitle of the podcast.
     *
     * @var string|null
     */
    private $subtitle;

    /**
     * Description of the podcast.
     *
     * @var string
     */
    private $description;

    /**
     * URL to the podcast website.
     *
     * @var string
     */
    private $link;

    /**
     * URL to the image representing the podcast.
     *
     * @var string
     */
    private $image;

    /**
     * Author of the podcast.
     *
     * @var string
     */
    private $author;

    /**
     * Category of the podcast.
     *
     * @var string
     */
    private $category = null;

    /**
     * Language of the podcast.
     *
     * @var string
     */
    private $language = null;

    /**
     * Date of the last publication of the podcast.
     *
     * @var DateTime
     */
    private $pubDate;

    /**
     * Email address of the owner of the podcast.
     *
     * @var string
     */
    private $email = null;

    /**
     * Copyright podcast.
     *
     * @var string
     */
    private $copyright = null;

    /**
     * List of media for the podcast.
     *
     * @var array
     */
    private $media = [];

    /**
     * Class constructor.
     *
     * @param  array $config
     */
    function __construct(array $config)
    {
        $this->config = $config;

        // Set default headers
        $this->setHeader([]);
    }

    /**
     * Set the header of the podcast feed
     *
     * @param mixed $data
     */
    public function setHeader($data)
    {
        // Required
        $this->title = $this->getValue($data, 'title');
        $this->description = $this->getValue($data, 'description');
        $this->link = $this->getValue($data, 'link');
        $this->image = $this->getValue($data, 'image');
        $this->author = $this->getValue($data, 'author');

        // Optional values
        $this->category = $this->getValue($data, 'category');
        $this->subtitle = $this->getValue($data, 'subtitle');
        $this->language = $this->getValue($data, 'language');
        $this->email = $this->getValue($data, 'email');
        $this->copyright = $this->getValue($data, 'copyright');
    }

    /**
     * Get value from data and escape it.
     *
     * @param  mixed  $data
     * @param  string $key
     *
     * @return string
     */
    public function getValue($data, $key)
    {
        $value = array_get($data, $key, $this->getDefault($key));

        return htmlspecialchars($value);
    }

    /**
     * Add media to the podcast feed.
     *
     * @param array $media
     */
    public function addMedia(array $media)
    {
        $this->media[] = new Media($media);
    }

    /**
     * Returns the podcast generated as character strings
     *
     * @return  string
     */
    public function toString()
    {
        return $this->generate()->saveXML();
    }

    /**
     * Returns the podcast generated as DOM document
     *
     * @return  \DOMDocument
     */
    public function toDom()
    {
        return $this->generate();
    }

    /**
     * Get default value from config
     *
     * @param  string $key
     * @param  mixed  $fallback
     *
     * @return mixed
     */
    public function getDefault($key, $fallback = null)
    {
        return array_get($this->config['defaults'], $key, $fallback);
    }

    /**
     * Generate the DOM document
     *
     * @return \DOMDocument
     */
    private function generate()
    {
        // Create the DOM
        $dom = new \DOMDocument("1.0", "utf-8");

        // Create the <rss>
        $rss = $dom->createElement("rss");
        $rss->setAttribute("xmlns:itunes", "http://www.itunes.com/dtds/podcast-1.0.dtd");
        $rss->setAttribute("version", "2.0");
        $dom->appendChild($rss);

        // Create the <channel>
        $channel = $dom->createElement("channel");
        $rss->appendChild($channel);

        // Create the <title>
        $title = $dom->createElement("title", $this->title);
        $channel->appendChild($title);

        // Create the <itunes:subtitle>
        if ($this->subtitle != null) {
            $itune_subtitle = $dom->createElement("itunes:subtitle", $this->subtitle);
            $channel->appendChild($itune_subtitle);
        }

        // Create the <link>
        $link = $dom->createElement("link", $this->link);
        $channel->appendChild($link);

        // Create the <description>
        $description = $dom->createElement("description");
        $description->appendChild($dom->createCDATASection($this->description));
        $channel->appendChild($description);

        // Create the <itunes:summary>
        $itune_summary = $dom->createElement("itunes:summary", $this->description);
        $channel->appendChild($itune_summary);

        // Create the <image>
        $image = $dom->createElement("image");
        $image->appendChild($title->cloneNode(true));
        $image->appendChild($link->cloneNode(true));
        $channel->appendChild($image);
        $image_url = $dom->createElement("url", $this->image);
        $image->appendChild($image_url);

        // Create the <itunes:image>
        $itune_image = $dom->createElement("itunes:image");
        $itune_image->setAttribute("href", $this->image);
        $channel->appendChild($itune_image);

        // Create the <itunes:author>
        $itune_author = $dom->createElement("itunes:author", $this->author);
        $channel->appendChild($itune_author);

        // Create the <itunes:owner>
        $itune_owner = $dom->createElement("itunes:owner");
        $itune_owner_name = $dom->createElement("itunes:name", $this->author);
        $itune_owner->appendChild($itune_owner_name);
        if ($this->email != null) {
            $itune_owner_email = $dom->createElement("itunes:email", $this->email);
            $itune_owner->appendChild($itune_owner_email);
        }
        $channel->appendChild($itune_owner);

        // Create the <itunes:category>
        if ($this->category !== null) {
            $category = $dom->createElement("itunes:category");
            $category->setAttribute("text", $this->category);
            $channel->appendChild($category);
        }

        // Create the <language>
        if ($this->language !== null) {
            $language = $dom->createElement("language", $this->language);
            $channel->appendChild($language);
        }

        // Create the <copyright>
        if ($this->copyright !== null) {
            $copyright = $dom->createElement("copyright", $this->copyright);
            $channel->appendChild($copyright);
        }

        // Create the <items>
        foreach ($this->media as $media) {
            // Addition of media in the dom
            $media->addToDom($dom);

            // Get the latest date media for <pubDate>
            if ($this->pubDate == null) {
                $this->pubDate = $media->getPubDate();
            }
            else {
                if ($this->pubDate < $media->getPubDate()) {
                    $this->pubDate = $media->getPubDate();
                }
            }
        }

        // Create the <pubDate>
        if ($this->pubDate == null) {
            $this->pubDate = new DateTime();
        }
        $pubDate = $dom->createElement("pubDate", $this->pubDate->format(DATE_RFC2822));
        $channel->appendChild($pubDate);

        // Return the DOM
        return $dom;
    }
}
