<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;
use Parsedown;

/**
 * Markdown Helper
 *
 * Provides markdown rendering functionality using Parsedown.
 */
class MarkdownHelper extends Helper
{
    /**
     * Parsedown instance
     *
     * @var \Parsedown
     */
    protected Parsedown $parsedown;

    /**
     * Initialize the helper
     *
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->parsedown = new Parsedown();
        // Optional: disable inline styles for security
        // $this->parsedown->setSafeMode(true);
    }

    /**
     * Convert markdown text to HTML
     *
     * @param string|null $text Markdown text
     * @return string HTML output
     */
    public function toHtml(?string $text): string
    {
        if (empty($text)) {
            return '';
        }

        return $this->parsedown->text($text);
    }

    /**
     * Convert markdown text to HTML (alias for toHtml)
     *
     * @param string|null $text Markdown text
     * @return string HTML output
     */
    public function parse(?string $text): string
    {
        return $this->toHtml($text);
    }
}
