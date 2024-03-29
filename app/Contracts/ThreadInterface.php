<?php

namespace App\Contracts;

use App\Models\Thread;

interface ThreadInterface
{
    public function getFilterablePaginatedCollection();

    public function findById(string|int $id);

    public function store(array $data);

    public function update(Thread $thread, array $updateData);

    public function delete(Thread $thread);
}
