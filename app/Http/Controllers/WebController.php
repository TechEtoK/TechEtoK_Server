<?php

namespace App\Http\Controllers;

use App\Models\Words;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller as BaseController;

class WebController extends BaseController
{
    public function index(Request $request)
    {
        $query = $request->input('q', null);
        if (!empty($query)) {
            $words = Words::searchWords($query);
        } else {
            $words = [];
        }

        return view('index', ['query' => $query, 'words' => $words]);
    }

    public function detail($word)
    {
        $query = urldecode($word);
        $word = Words::getByWord($query);
        if ($word === null) {
            return redirect('/');
        }

        $published_htmls = $word->getPublishedHTMLs(true, $usages);
        return view('detail', ['word' => $query, 'usages' => $usages, 'published_htmls' => $published_htmls]);
    }

    public function update(Request $request)
    {
        $query = $request->input('word', null);
        if (!empty($query)) {
            $query = urldecode($query);
            $word = Words::getByWord($query);
            if ($word === null) {
                return response('Wrong parameters', Response::HTTP_BAD_REQUEST);
            }
        } else {
            $word = null;
        }

        return view('update', ['word' => $word]);
    }
}
