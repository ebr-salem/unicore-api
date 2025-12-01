<!--
  for line 43 in app\Listeners\SendNotificationListener.php

ضيف getTargetUsers() داخل كل
Model
 اللي هيعمل Model (Lecture, Section, Task, Quiz,..)
 الفانكشن دي مطلوبه
getTargetUsers()

public function getTargetUsers()
{
    return \App\Models\User::whereHas('studentSubjects', function ($q) {
        $q->where('subject_id', $this->subject_id);
    })->pluck('id')->toArray();
}


-->
