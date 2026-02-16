@extends('layouts.app')

@section('titulo')
    Editar Perfil: {{ auth()->user()->username }}
@endsection

@section('contenido')
    <div class="md:flex md:justify-center">
        <div class="md:w-1/2 bg-white shadow p-6">
            <form method="POST" action="{{ route('perfil.store') }}" enctype="multipart/form-data" class="mt-10 md:mt-0">
                @csrf
                <div class="mb-5">
                    <label for="username" class="mb-2 block uppercase text-gray-500 font-bold">
                        Username
                    </label>
                    <input
                        id="username"
                        name="username"
                        type="text"
                        placeholder="Tu Nombre de Usuario"
                        class="border p-3 w-full rounded-lg @error('username') border-red-500 
                        @enderror"
                        value="{{ auth()->user()->username }}"
                    />

                    @error('username')
                        <p class="bg-red-500 text-white my-2 rounded-lg text-sm p-2 
                        text-center">{{ $message }}</p>
                    @enderror     

                </div>
                
                <div class="mb-5">
                    <label class="mb-2 block uppercase text-gray-500 font-bold">
                        Imagen Perfil
                    </label>

                    <label class="cursor-pointer bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded-lg inline-block">
                        Seleccionar Imagen
                        <input
                            id="imagen"
                            name="imagen"
                            type="file"
                            class="hidden"
                            accept=".jpg,.jpeg,.png"
                        >
                    </label>

                    <p id="nombreArchivo" class="mt-2 text-sm text-gray-500"></p>
                    </div>

                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const input = document.getElementById('imagen');
                        const texto = document.getElementById('nombreArchivo');

                        input.addEventListener('change', function(e) {
                            if (e.target.files.length > 0) {
                                texto.textContent = e.target.files[0].name;
                            }
                        });
                    });
                    </script>


                <input 
                    type="submit"
                    value="Guardar Cambios"
                    class="bg-sky-600 hover:bg-sky-700 transition-colors cursor-pointer 
                    uppercase font-bold w-full p-3 text-white rounden-lg"
                />
            </form>
        </div>
    </div>
@endsection