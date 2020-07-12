<?php

namespace App\Repositories\Contracts;

interface IDesign extends IBase
{
    public function applyTags($id, array $data);

    public function addComment($designId, array $data);
}
