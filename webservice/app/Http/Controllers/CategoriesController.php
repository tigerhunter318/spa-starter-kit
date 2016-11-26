<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;

class CategoriesController extends Controller
{
    public function all(Request $request)
    {
        try {
            $pager = Category::paginate(10);
            return response()->json(compact('pager'), 200);
        } catch(\Exception $e) {
            return response()->json(['messages' => ['Não foi possível obter a lista de categorias']], 404);
        }
    }

    public function get($id)
    {
        try {
            $category = Category::find($id);
            return response()->json(['result' => 'success', 'category' => $category], 200);
        } catch(\Exception $e) {
            return response()->json(['messages' => ['Não foi possível obter a categoria']], 404);
        }
    }

    public function create(Request $request)
    {
        try {
            Category::create($request->only('name'));
            return response()->json(['result' => 'success'], 200);
        } catch(\Exception $e) {
            return response()->json(['messages' => ['Não foi possível criar a categoria']], 422);
        }
    }

    public function update($id, Request $request)
    {
        try {
            $input = $request->get('category');
            $category = Category::find($id);
            $category->name = $input['name'];
            $category->save();
            return response()->json(['result' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['messages' => ['Não foi possível atualizar a categoria']], 422);
        }
    }

    public function remove($id)
    {
        try {
            $category = Category::find($id);
            $category->delete();
            return response()->json(['result' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['messages' => ['Não foi possível remover a categoria']], 422);
        }
    }
}
