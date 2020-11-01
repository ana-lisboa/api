<?php

namespace Domains\Discussions\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Domains\Discussions\Models\Question;
use  Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Contracts\Pagination\Paginator;

class QuestionsGetAnswersController extends Controller
{
private Question $question;

    public function __construct(Auth $auth, Question $question)
    {
        if ($auth->guest()) {
            $this->middleware('throttle:30,1');
        }

        $this->question = $question;
    }

    public function __invoke(int $id, Request $request): Paginator
    {
        $this->validate($request, [
            'author' => 'sometimes|integer|exists:users,id',
            'created' => 'sometimes|array|size:2',
            'created.from' => 'required_with:created|date',
            'created.to' => 'required_with:created|date|afterOrEqual:created.from'
        ]);

        return $this->question
            ->findOrFail($id)
            ->answers()
            ->when($authorId = $request->input('author'),
                static fn(Builder $answers, int $authorId) => $answers->whereAuthorId($authorId))
            ->when($created = $request->input('created'),
                static fn(Builder $answers, array $created) => $answers->whereBetween('created_at', [$created['from'], $created['to']]))
            ->latest()
        ->simplePaginate(15);
    }
}
