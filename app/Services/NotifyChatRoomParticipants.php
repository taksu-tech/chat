<?php

namespace Taksu\TaksuChat\Services;

use App\Models\ChatMessage;
use App\Notifications\Chat\NewChatMessageNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Lorisleiva\Actions\Concerns\AsAction;
use Taksu\TaksuChat\Notifications\NewChat;

class NotifyChatRoomParticipants
{
    use AsAction;

    public function handle(ChatMessage $chatMessage)
    {
        $room = $chatMessage->room;

        $participants = $room
            ->participants
            ->filter(function ($participant) use ($chatMessage) {
                return ! (
                    $participant->participant_type == $chatMessage->sender_type &&
                    $participant->participant_id == $chatMessage->sender_id
                );
            });
        // dd($chatMessage->toArray(), $participants->toArray());
        $users = $participants->map(function ($participant) {
            return ($participant->participant_type)::find($participant->participant_id);
        });
        $users->whereNotNull('device_token');

        // below code to send notifications to a given array of user
        // This method will trigger an error when there is an invalid device token.
        // the error will cancel all notification processing
        // * update: call the action with `dispatchAfterResponse` can deal with the above problem
        // the notification is put on the queue to prevent error messages include in http response
        Notification::send($users, new NewChat($chatMessage));

        // alternative: loop through all user
        // seperate the valid and non valid token woth try catch
        // * note: take more steps
        // foreach ($users as $user) {
        //     try {
        //         $user->notify(new NewChatMessageNotification($chatMessage));

        //     } catch (\Throwable $e) {
        //         Log::error($user->email . ' device token is invalid');
        //         continue;
        //     }
        // }

    }
}
