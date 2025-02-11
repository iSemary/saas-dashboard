<?php

namespace App\Enums;

enum StaticPageAttribute: string
{
    // Basic Attributes
    case TITLE = 'title';
    case DESCRIPTION = 'description';
    case KEYWORDS = 'keywords';
    case BODY = 'body';
    case EXCERPT = 'excerpt';

    // SEO & Metadata
    case META_TITLE = 'meta_title';
    case META_DESCRIPTION = 'meta_description';
    case META_KEYWORDS = 'meta_keywords';
    case CANONICAL_URL = 'canonical_url';

    // Images & Media
    case IMAGE = 'image';
    case BANNER_IMAGE = 'banner_image';
    case FAVICON = 'favicon';
    case VIDEO_URL = 'video_url';

    // Page Settings
    case SLUG = 'slug';
    case TEMPLATE = 'template';
    case VISIBILITY = 'visibility';
    case STATUS = 'status';

    // Social Media & OpenGraph
    case OG_TITLE = 'og_title';
    case OG_DESCRIPTION = 'og_description';
    case OG_IMAGE = 'og_image';
    case TWITTER_CARD = 'twitter_card';

    // Additional Configurations
    case CUSTOM_CSS = 'custom_css';
    case CUSTOM_JS = 'custom_js';
    case REDIRECT_URL = 'redirect_url';
    case PRIORITY = 'priority';

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
