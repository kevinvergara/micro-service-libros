<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Trates\ApiResponse;

use App\Models\Book;

class BookController extends Controller
{

    use ApiResponse;

    public function index()
    {
        $books = Book::all();

        return $this->successResponse($books);
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|max:255',
            'description' => 'required|max:255',
            'price' => 'required|min:1',
            'author_id' => 'required|min:1'
        ];

        $this->validate($request, $rules);

        $book = Book::create($request->all());

        return $this->successResponse($book, Response::HTTP_CREATED);
    }

    public function show($book)
    {
        $book = Book::findOrFail($book);

        return $this->successResponse($book);
    }

    public function update(Request $request, $book)
    {
        $rules = [
            'title' => 'required|max:255',
            'description' => 'required|max:255',
            'price' => 'required|min:1',
            'author_id' => 'required|min:1'
        ];

        $this->validate($request, $rules);

        $book = Book::findOrFail($book);

        $book->fill($request->all());

        if($book->isClean()){
            return $this->errorResponse('No hay cambios', Response::HTTP_UNPROCESSABLE_ENTITY);
        }else{
            $book->save();

            return $this->successResponse($book);
        }
    }

    public function destroy($book)
    {
        $book = Book::findOrFail($book);

        $book->delete();

        return $this->successResponse($book);
    }
}
