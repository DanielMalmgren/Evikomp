<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\User;
use App\Lesson;
use App\Workplace;
use App\NotificationReceiver;

class NotificationReceiversController extends Controller
{
    public function edit(Lesson $lesson): View {
        $data = [
            'lesson' => $lesson,
            'workplaces' => Workplace::all(),
        ];
        return view('notificationreceivers.edit')->with($data);
    }

    public function update(Request $request, Lesson $lesson): RedirectResponse {
        usleep(50000);

        //First make a local copy so we can change the temp ID's for new receivers
        $workplaces = $request->workplaces;

        //And then fix the ID's
        if($request->new_notification_receivers !== null) {
            foreach($request->new_notification_receivers as $randomtemp=>$real) {
                $workplaces[$real] = $workplaces[$randomtemp];
                unset($workplaces[$randomtemp]);
            }
        }

        //Remove it all to start from scratch
        NotificationReceiver::where('lesson_id', $lesson->id)->delete();

        //..and then just put it back in from the request
        if($workplaces !== null) {
            foreach($workplaces as $user_id => $workplaces_array) {
                if(User::find($user_id) !== null) {
                    foreach($workplaces_array as $workplace_id) {
                        logger("Adding notification on workplace ".$workplace_id." to user ".$user_id);
                        NotificationReceiver::create([
                            'lesson_id' => $lesson->id,
                            'workplace_id' => $workplace_id,
                            'user_id' => $user_id,
                        ]);
                    }
                }
            }
        }


        return redirect('/lessons/'.$lesson->id)->with('success', __('Ändringar sparade'));
    }

}
