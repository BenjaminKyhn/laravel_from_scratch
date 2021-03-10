<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Tag;

class ArticlesController extends Controller
{
    public function show($id){
        $article = Article::findOrFail($id);

        return view('articles.show', ['article' => $article]);
    }

    public function index(){
        if(request('tag')){
            $articles = Tag::where('name', request('tag'))->firstOrFail()->articles;
        } else {
            $articles = Article::latest()->get();
        }

        return view('articles.index', ['articles' => $articles]);
    }

    public function create(){
        return view('articles.create', [
            'tags' => Tag::all()
        ]);
    }

    public function store(){
        $this->validateArticle();

        $article = new Article(request(['title', 'excerpt', 'body']));
        $article->user_id = 1;
        $article->save();

        if (request()->has('tags')){
            $article->tags()->attach(request('tags'));
        }

        return redirect(route('articles.index'));
    }

    public function edit(Article $article){
        return view('articles.edit', ['article' => $article]);
    }

    public function update($id){
        $this->validateArticle();

        $article = Article::find($id);
        $article->title = request('title');
        $article->excerpt = request('excerpt');
        $article->body = request('body');
        $article->save();

        return redirect(route('articles.show', $article->id));
//        return redirect($article->path()); //cleaner
    }

    public function destroy(){

    }

    public function validateArticle(){
        return request()->validate([
            'title' => ['required', 'min:3', 'max:255'],
            'excerpt' => 'required',
            'body' => 'required',
            'tags' => 'exists:tags,id'
        ]);
    }
}
