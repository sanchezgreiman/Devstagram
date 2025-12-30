<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;



class ImagenController extends Controller
{
    public function store(Request $request)
    {
        $imagen = $request->file('file');

        $nombreImagen = uniqid() . '.' . $imagen->extension();

        $manager = new ImageManager(new Driver());
        $imagenServidor = $manager->read($imagen);

        $imagenServidor->cover(1000, 1000);

        $imagenPath = public_path('uploads/' . $nombreImagen);
        $imagenServidor->save($imagenPath);

        return response()->json(['imagen' => $nombreImagen]);
    }
}
