<?php

namespace App\Listeners;

use App\Services\NotificationService;

class SendNotificationListener
{
    protected $service;

    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle incoming events (LectureCreated, SectionCreated, etc.)
     */
    public function handle($event)
    {
        // identify which model came with event
        $model =
            $event->lecture ??
            $event->section ??
            $event->task ??
            $event->quiz ??
            $event->post ??
            null;

        if (! $model) return;

        // Title of the notification
        $title = "New " . class_basename($model) . " added";

        // Content (fallback if name/title not exists)
        $content = $model->name ?? $model->title ?? "New content available";

        // Target users (students registered in the subject)
        if (!method_exists($model, 'getTargetUsers')) {
            return; // safety check
        }

        $userIds = $model->getTargetUsers(); //  -> Model (Lecture, Section, Task, Quiz,..)

        if (empty($userIds)) {
            return;
        }

        // Create Notification
        $this->service->createForUsers(
            $userIds,
            $title,
            $content,
            get_class($model),
            $model->id,
            $model->owner_id ?? $model->doctor_id ?? $model->assistant_id ?? null // auto-detect sender
        );
    }
}
