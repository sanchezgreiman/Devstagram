<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('query');

        if (!$query) {
            return response()->json([]);
        }

        $usuarios = User::where('username', 'LIKE', "{$query}%")
            ->take(5)
            ->get();

        return response()->json($usuarios);

    }
}
