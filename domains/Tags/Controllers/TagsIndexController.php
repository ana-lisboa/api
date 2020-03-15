<?php

namespace Domains\Tags\Controllers;

use App\Http\Controllers\Controller;
use Domains\Tags\Models\Tag;
use Domains\Tags\Resources\TagResource;

class TagsIndexController extends Controller
{
    public function __invoke(Tag $tags)
    {
        return TagResource::collection($tags->all());
    }
}
