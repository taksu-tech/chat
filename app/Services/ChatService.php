<?php

namespace Taksu\TaksuChat\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Taksu\TaksuChat\Models\ChatMessage;
use Taksu\TaksuChat\Models\ChatRoom;
use Taksu\TaksuChat\Models\ChatRoomParticipant;
use Taksu\TaksuChat\Services\ParticipantHelper;

class ChatService
{
    public function createRoom(array $data): ChatRoom
    {
        $validated = Validator::validate($data, [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'participants' => 'nullable|array',
            'participants.*.id' => 'required',
            'participants.*.type' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $room = new ChatRoom($validated);
            $room->status = ChatRoom::STATUS_OPEN;
            $room->last_message_at = now();
            $room->save();

            // it's an option to create room and also assign participant
            // if so, user must pass parameter `participants id`
            if (isset($data['participants'])) {
                // looping to add participant
                foreach ($validated['participants'] as $data) {
                    $participant = new ParticipantHelper(
                        id: $data['id'],
                        type: $data['type'],
                    );
                    $this->addParticipant($room, $participant);
                }
            }

            DB::commit();

            return $room;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateRoom(ChatRoom $room, array $data): ChatRoom
    {
        $validated = Validator::validate($data, [
            'name' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $room->update($validated);

        return $room;
    }

    public function closeRoom(ChatRoom $room): ChatRoom
    {
        $room->status = ChatRoom::STATUS_CLOSED;
        $room->save();

        return $room;
    }

    public function addParticipant(ChatRoom $room, ParticipantHelper $participant)
    {
        $this->checkRoomStatus($room);

        if ($this->isInChatRoom($room, $participant)) {
            throw new Exception('Participant is already in chat room', 422);
        }

        ChatRoomParticipant::create([
            'chat_room_id' => $room->id,
            'participant_type' => $participant->getType(),
            'participant_id' => $participant->getId(),
        ]);

        $room->refresh();

        return $room;
    }

    public function addMultipleParticipant(ChatRoom $room, array $data)
    {
        $validated = Validator::validate($data, [
            'participants' => 'required|array',
            'participants.*.id' => 'required',
            'participants.*.type' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['participants'] as $data) {
                $participant = new ParticipantHelper(
                    id: $data['id'],
                    type: $data['type'],
                );
                $this->addParticipant($room, $participant);
            }

            DB::commit();
            $room->refresh();

            return $room;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function setLastTimeReading(ChatRoom $room, ParticipantHelper $participant)
    {
        // thet method to record the user last time reading the message
        // alternative to is_read column
        // its saving a date time
        // the logic is if the message was created after `last_read` date time, mean user not yet read that message. vice versa
        $this->checkRoomStatus($room);

        if (!$this->isInChatRoom($room, $participant)) {
            throw new Exception('You are not a participant of this chat.', 403);
        }

        $roomParticipant = ChatRoomParticipant::where([
            ['chat_room_id', '=', $room->id],
            ['participant_type', '=', get_class($participant)],
            ['participant_id', '=', $participant->getId()],
        ])->first();

        if (!$roomParticipant) {
            throw new Exception('Participant not found');
        }

        $roomParticipant->last_read = now()->toDateTimeString();
        $roomParticipant->save();

        return $roomParticipant->last_read;
    }

    public function sendChatMessage(ChatRoom $room, ParticipantHelper $sender, array $data)
    {
        $this->checkRoomStatus($room);

        if (!$this->isInChatRoom($room, $sender)) {
            throw new Exception('You are not a participant of this chat.', 403);
        }

        $validated = Validator::validate($data, [
            'media_mime' => 'nullable|string',
            'media_url' => 'nullable|string',
            'message' => 'required|string',
        ]);

        $message = ChatMessage::create(array_merge($validated, [
            'chat_room_id' => $room->id,
            'sender_type' => $sender->getType(),
            'sender_id' => $sender->getId(),
        ]));

        $room->update(['last_message_at' => now()]);

        DB::commit();

        // Async function to persist chat item to database
        // and notify participants other than the sender.
        NotifyChatRoomParticipants::dispatchAfterResponse($message);

        return $message;
    }

    private function checkRoomStatus(ChatRoom $room)
    {
        if ($room->status !== ChatRoom::STATUS_OPEN) {
            throw new Exception('Chat Room is closed', 422);
        }
    }

    private function isInChatRoom(ChatRoom $room, ParticipantHelper $participant)
    {
        // check if given user is part of room participant
        $isInRoom = $room->participants()
            ->where('participant_type', $participant->getType())
            ->where('participant_id', $participant->getId())
            ->get();

        if ($isInRoom->isEmpty()) {
            return false;
        }

        return true;
    }

    public function removeParticipant(ChatRoom $room, string $participantId)
    {
        $this->checkRoomStatus($room);

        $roomParticipant = ChatRoomParticipant::where([
            ['chat_room_id', '=', $room->id],
            ['participant_id', '=', $participantId],
        ]);
        // dd($roomParticipant->get()->toArray(), $participantId);
        if (!$roomParticipant->exists()) {
            throw new Exception('Participant not found', 400);
        }

        $roomParticipant->delete();
        $room->refresh();

        return $room;
    }

    public function removeMultipleParticipant(ChatRoom $room, array $data)
    {
        $validated = Validator::validate($data, [
            'participants' => 'required|array',
            'participants.*' => 'required|exists:chat_room_participants,participant_id',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['participants'] as $id) {
                $this->removeParticipant($room, $id);
            }

            DB::commit();
            $room->refresh();

            return $room;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
