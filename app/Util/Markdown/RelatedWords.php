<?php

namespace App\Util\Markdown;

class RelatedWords
{
    public $word;
    public $link;

    public function __construct($word, $link = null)
    {
        $this->word = $word;
        if ($link !== null) {
            $this->link = $link;
        }
    }
}
