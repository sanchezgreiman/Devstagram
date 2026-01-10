<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImagenController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $imagen = $request->file('file');
        $userId = auth()->user()->id;

        $nombreImagen = uniqid() . '.' . $imagen->extension();
        $rutaRelativa = "posts/{$userId}/{$nombreImagen}";

        $manager = new ImageManager(new Driver());
        $imagenServidor = $manager->read($imagen);
        $imagenServidor->cover(1000, 1000);

        Storage::disk('public')->put($rutaRelativa, $imagenServidor->toJpeg());

        return response()->json(['imagen' => $rutaRelativa]);
    }
}
