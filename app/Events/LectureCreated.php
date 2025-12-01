<?php

namespace App\Events;

use App\Models\Lecture;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LectureCreated
{
    use Dispatchable, SerializesModels;

    public $lecture;

    public function __construct(Lecture $lecture)
    {
        $this->lecture = $lecture;
    }
}
