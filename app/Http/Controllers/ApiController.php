<?php

namespace App\Http\Controllers;

use App\Models\Words;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller as BaseController;

class ApiController extends BaseController
{
    public function search(Request $request)
    {
        $query = $request->input('q', null);
        if ($query === null) {
            return response('Wrong parameters', Response::HTTP_BAD_REQUEST);
        }

        $words = Words::searchWords($query);
        return response()->json(['words' => $words]);
    }

    public function addWord(Request $request)
    {
        $values = $request->all();

        // TODO: 단어 추가

        return response('', Response::HTTP_OK);
    }

    public function editWord(Request $request)
    {
        $values = $request->all();

        // TODO: 단어 수정

        return response('', Response::HTTP_OK);
    }
}
