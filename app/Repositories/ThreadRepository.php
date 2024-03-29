<?php

namespace App\Repositories;

use App\Models\Thread;
use App\Contracts\ThreadInterface;
use App\Http\QueryFilters\MentionedQueryFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Http\QueryFilters\MineQueryFilter;
use App\Http\QueryFilters\TopicQueryFilter;
use App\Http\QueryFilters\NoRepliesQueryFilter;
use App\Http\QueryFilters\ParticipatingQueryFilter;
use App\Http\QueryFilters\SolvedQueryFilter;
use App\Http\QueryFilters\UnsolvedQueryFilter;

class ThreadRepository implements ThreadInterface
{
    // filterable paginated collection
    public function getFilterablePaginatedCollection()
    {
        return QueryBuilder::for(Thread::class)
                                ->with(['topic', 'user', 'posts', 'latestPost.user', 'solution']) // eager load
                                ->allowedFilters($this->customAllowedFilters()) // custom filters
                                ->searchByTitle()
                                ->orderBy('id', 'desc')
                                ->orderByLatestPost()
                                ->paginate(Thread::PAGINATION_COUNT)
                                ->appends(request()->all());
    }

    // find by id
    public function findById(string|int $id)
    {
        return Thread::find($id);
    }

    // store into database
    public function store(array $data)
    {
        return auth()->user()
                            ->threads()->create([
                                'title' => $data['title'],
                                'body' => $data['body'],
                                'topic_id' => $data['topic_id'],
                            ]);
    }

    public function update(Thread $thread, array $updateData)
    {
        $thread->update([
            'title' => $updateData['title'],
            'body' => $updateData['body'],
            'topic_id' => $updateData['topic_id'],
        ]);

        return $thread;
    }

    public function delete(Thread $thread)
    {
        $thread->delete();
    }

    // custom filters for spaite/QueryBuilder
    private function customAllowedFilters(): array
    {
        return [
            AllowedFilter::custom('noreplies', new NoRepliesQueryFilter),
            AllowedFilter::custom('mine', new MineQueryFilter),
            AllowedFilter::custom('participating', new ParticipatingQueryFilter),
            AllowedFilter::custom('topic', new TopicQueryFilter),
            AllowedFilter::custom('resolved', new SolvedQueryFilter),
            AllowedFilter::custom('unresolved', new UnsolvedQueryFilter),
            AllowedFilter::custom('mentioned', new MentionedQueryFilter)
        ];
    }
}
