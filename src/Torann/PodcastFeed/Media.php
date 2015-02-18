<?php namespace Torann\PodcastFeed;

use DateTime;
use DOMDocument;

class Media
{
    /**
     * Title of media.
     *
     * @var string
     */
    private $title;

    /**
     * Subtitle of media.
     *
     * @var string|null
     */
    private $subtitle;

    /**
     * URL to the media web site.
     *
     * @var string
     */
    private $link;

    /**
     * Date of publication of the media.
     *
     * @var DateTime
     */
    private $pubDate;

    /**
     * description media.
     *
     * @var string
     */
    private $description;

    /**
     * URL of the media
     *
     * @var string
     */
    private $url;

    /**
     * Type of media (audio / mpeg, for example).
     *
     * @var string
     */
    private $type;

    /**
     * Author of the media.
     *
     * @var string
     */
    private $author;

    /**
     * GUID of the media.
     *
     * @var string
     */
    private $guid;

    /**
     * Duration of the media only as HH:MM:SS, H:MM:SS, MM:SS or M:SS.
     *
     * @var string
     */
    private $duration;

    /**
     * URL to the image representing the media..
     *
     * @var string
     */
    private $image;

    /**
     * Class constructor
     *
     * @param array $data
     */
    public function __construct($data)
    {
        $this->title       = array_get($data, 'title');
        $this->subtitle    = array_get($data, 'subtitle', null);
        $this->description = array_get($data, 'description', null);
        $this->pubDate     = array_get($data, 'publish_at');
        $this->url         = array_get($data, 'url');
        $this->guid        = array_get($data, 'guid');
        $this->type        = array_get($data, 'type');
        $this->duration    = array_get($data, 'duration');
        $this->author      = array_get($data, 'author', null);
        $this->image       = array_get($data, 'image', null);

        // Ensure publish date is a DateTime instance
        if (is_string($this->pubDate))
        {
            $this->pubDate = new DateTime($this->pubDate);
        }
    }

    /**
     * Get media publication date.
     *
     * @return  DateTime
     */
    public function getPubDate()
    {
        return $this->pubDate;
    }

    /**
     * Adds media in the DOM document setting.
     *
     * @param DOMDocument $dom
     */
    public function addToDom(DOMDocument $dom)
    {
        // Recovery of  <channel>
        $channels = $dom->getElementsByTagName("channel");
        $channel = $channels->item(0);

        // Create the <item>
        $item = $dom->createElement("item");
        $channel->appendChild($item);

        // Create the <title>
        $title = $dom->createElement("title", $this->title);
        $item->appendChild($title);

        // Create the <itunes:subtitle>
        if ($this->subtitle)
        {
            $itune_subtitle = $dom->createElement("itunes:subtitle", $this->subtitle);
            $item->appendChild($itune_subtitle);
        }

        // Create the <description>
        $description = $dom->createElement("description");
        $description->appendChild($dom->createCDATASection($this->description));
        $item->appendChild($description);

        // Create the <itunes:summary>
        $itune_summary = $dom->createElement("itunes:summary", $this->description);
        $item->appendChild($itune_summary);

        // Create the <pubDate>
        $pubDate = $dom->createElement("pubDate", $this->pubDate->format(DATE_RFC2822));
        $item->appendChild($pubDate);

        // Create the <enclosure>
        $enclosure = $dom->createElement("enclosure");
        $enclosure->setAttribute("url", $this->url);
        $enclosure->setAttribute("type", $this->type);
        $item->appendChild($enclosure);

        // Create the author
        if ($this->author)
        {
            // Create the <author>
            $author = $dom->createElement("author", $this->author);
            $item->appendChild($author);

            // Create the <itunes:author>
            $itune_author = $dom->createElement("itunes:author", $this->author);
            $item->appendChild($itune_author);
        }

        // Create the <itunes:duration>
        $itune_duration = $dom->createElement("itunes:duration", $this->duration);
        $item->appendChild($itune_duration);

        // Create the <guid>
        $guid = $dom->createElement("guid", $this->guid);
        $item->appendChild($guid);

        // Create the <itunes:image>
        if ($this->image)
        {
            $itune_image = $dom->createElement("itunes:image");
            $itune_image->setAttribute("href", $this->image);
            $item->appendChild($itune_image);
        }
    }
}