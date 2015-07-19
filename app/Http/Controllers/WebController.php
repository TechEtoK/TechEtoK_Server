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

        $usage_count = preg_match_all('/사용처<\/h3>\s\s<p>(.*)<\/p>\s\s<h3>/', $data, $usages);
        if ($usage_count > 1) {
            $usages = $usages[1];

            $title_start = 0;
            $title_end = stripos($data, '</h1>', $title_start) + strlen('</h1>');
            $title = substr($data, $title_start, $title_end - $title_start);
            $data = substr($data, $title_end, strlen($data) - $title_end);

            $published_html = '<ul class="nav nav-pills">';
            for ($i = 0; $i < $usage_count; $i++) {
                $published_html .= '<li role="presentation">';
                $published_html .= '<a href="#' . $i . '">' . $usages[$i] .'</a>';
                $published_html .= '</li>';
            }
            $published_html .= '</ul>';

            $published_html .= '<div class="well-list">';
            for ($i = 0; $i < $usage_count; $i++) {
                $published_html .= '<div class="well" id="well' . $i . '">';
                $published_html .= $title;

                $well_start = 0;
                $well_end = stripos($data, '<hr>', $well_start);
                if ($well_end === false) {
                    $well_end = strlen($data);
                }
                $well = substr($data, $well_start, $well_end - $well_start);

                $published_html .= $well;
                $published_html .= '</div>';

                $well_end += strlen('<hr>');
                $data = substr($data, $well_end, strlen($data) - $well_end);
            }
            $published_html .= '</div>';
        } else {
            $published_html = '<div class="well">' . $data . '</div>';
        }

        return view('detail', ['word' => $query, 'published_html' => $published_html]);
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
