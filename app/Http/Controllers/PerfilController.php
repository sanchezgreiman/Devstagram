<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class PerfilController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('perfil.index');
    }

    public function store(Request $request)
    {
        $request->request->add(['username' => Str::slug($request->username)]); 

        $this->validate($request, [
            'username' => ['required','unique:users,username,'.auth()->user()->id,'min:3','max:20','not_in:twitter,editar-perfil']
        ]);

        $usuario = User::find(auth()->user()->id);
        $rutaImagen = $usuario->imagen;

        if($request->imagen) {
            $imagen = $request->file('imagen');
            $userId = $usuario->id;

            $nombreImagen = uniqid() . '.' . $imagen->extension();
            $rutaImagen = "perfiles/{$userId}/{$nombreImagen}";

            $manager = new ImageManager(new Driver());
            $imagenServidor = $manager->read($imagen);
            $imagenServidor->cover(1000, 1000);

            // Guardar usando Storage (en storage/app/public/perfiles/{user_id}/)
            Storage::disk('public')->put($rutaImagen, $imagenServidor->toJpeg());

            // Eliminar imagen anterior si existe
            if($usuario->imagen) {
                Storage::disk('public')->delete($usuario->imagen);
            }
        }

        // Guardar Cambios
        $usuario->username = $request->username;
        $usuario->imagen = $rutaImagen;
        $usuario->save();

        // Rediccionar
        return redirect()->route('posts.index', $usuario->username);
    }
}
