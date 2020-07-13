<?php

namespace App\Http\Controllers\Chats;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatResource;
use App\Http\Resources\MessageResource;
use App\Repositories\Contracts\IChat;
use App\Repositories\Contracts\IMessage;
use App\Repositories\Eloquent\Criteria\WithTrashed;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    protected $chats;
    protected $messages;

    public function __construct(IChat $chats, IMessage $messages) {
        $this->chats = $chats;
        $this->messages = $messages;
    }

    public function sendMessage(Request $request)
    {
        $this->validate($request, [
            'recipient' => ['required'],
            'body' => ['required'],
        ]);

        $recipient = $request->recipient;
        $user = auth()->user();
        $body = $request->body;

        // check if there is existing chat between auth user and recipient
        $chat = $user->getChatWithUser($recipient);
        if (!$chat) {
            $chat = $this->chats->create([]);
            $this->chats->createParticipants([$user->id, $recipient]);
        }

        $message = $this->messages->create([
            'user_id' => $user->id,
            'chat_id' => $chat->id,
            'body' => $body,
            'last_read' => null,
        ]);

        return new MessageResource($message);

    }

    public function getUserChats(Request $request)
    {
        $chats = $this->chats->getUserChats();
        return ChatResource::collection($chats);
    }

    public function getChatMessages($id)
    {
        $messages = $this->messages
                        ->withCriteria([new WithTrashed()])
                        ->findWhere('chat_id', $id);
        return MessageResource::collection($messages);
    }

    public function markAsRead($id)
    {
        $chat = $this->chats->find($id);
        $chat->markAsReadForUser(auth()->id());
        return response()->json([
            'message' => 'Marked as read',
        ], 200);
    }

    public function destroyMessage($id)
    {
        $message = $this->messages->find($id);

        $this->authorize('delete', $message);

        $message->delete();
    }
}
