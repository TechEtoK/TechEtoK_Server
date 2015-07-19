<?php

namespace App\Http\Controllers;

use App\Models\Words;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
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

        try {
            $word = Words::query()->where('word', '=', $query)->firstOrFail();
            $words_markdown = file_get_contents('http://words.techetok.kr/' . $word->file_name);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,            'https://api.github.com/markdown/raw');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST,           true);
            curl_setopt($ch, CURLOPT_POSTFIELDS,     $words_markdown);
            curl_setopt($ch, CURLOPT_HTTPHEADER,     array(
                'Content-Type: text/plain',
                'Content-Length: ' . strlen($words_markdown),
                'User-Agent: TechEtoK'
                )
            );
            $data = curl_exec($ch);
            curl_close($ch);

            $data = html_entity_decode($data, ENT_QUOTES, 'UTF-8');

            return view('detail', ['word' => $query, 'data' => $data]);
        } catch (ModelNotFoundException $e) {
            return view('not_found');
        }
    }
}
