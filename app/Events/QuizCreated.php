<?php

namespace App\Events;

use App\Models\Quiz;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuizCreated
{
    use Dispatchable, SerializesModels;

    public $Quiz;

    public function __construct(Quiz $Quiz)
    {
        $this->Quiz = $Quiz;
    }
}
