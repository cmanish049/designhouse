<?php

namespace App\Repositories\Contracts;

interface IChat extends IBase
{
    public function createParticipant($chatId, array $data);
    public function getUserChats();
}
