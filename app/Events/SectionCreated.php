<?php

namespace App\Events;

use App\Models\Section;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SectionCreated
{
    use Dispatchable, SerializesModels;

    public $Section;

    public function __construct(Section $Section)
    {
        $this->Section = $Section;
    }
}
