<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @stack('styles')
        <title>DevStagram - @yield('titulo')</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles

    </head>
    <body class="bg-gray-100">
        <header class="p-5 border-b bg-white shadow">
            <div class="container mx-auto flex justify-between items-center">

                <a href="{{ route('home') }}" class="text-3xl font-black">
                    DevStagram
                </a>

                @auth
                <div class="relative rounded-full py-2 bg-gray-100 text-gray-700 max-w-md flex-1">
    
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" 
                        class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>

                    <input type="text" id="busqueda" placeholder="Buscar en Devstagram..." 
                        autocomplete="off" class="focus:ring-0 focus:outline-none bg-transparent text-gray-800 border-none w-full pl-11 pr-5">
                    
                    <ul id="resultados" class="absolute top-full left-0 bg-white w-full rounded-xl shadow-lg z-50 hidden border border-gray-100">
                        <li class="px-4 py-2 hover:bg-gray-50 cursor-pointer first:rounded-t-xl last:rounded-b-xl">Resultado 1</li>
                    </ul>
                </div>

                    <nav class="flex items-center px-3 gap-4">
                        <a
                            class="flex items-center gap-2 bg-white border p-2 text-gray-600
                            rounded text-sm uppercase font-bold cursor-pointer"
                            href="{{route('posts.create')}}"
                        >

                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                        </svg>

                            Crear

                        </a>
                        
                        <a class="font-bold text-gray-600 text-sm gap-2 flex items-center whitespace-nowrap">
                            <span class="font-bold text-gray-600 text-sm"> 
                                {{ auth()->user()->username }}
                            </span>
                        </a>

                        <div class="relative md:w-8/12 lg:w-6/12" href="{{ route('perfil.index') }}">
                            <img 
                                id="profileBtn"
                                src="{{ 
                                auth()->user()->imagen ? 
                                asset('storage/' . auth()->user()->imagen) : 
                                asset('img/usuario.svg') }}"
                                alt="imagen usuario"
                                class="w-10 h-10 rounded-full cursor-pointer"/>

                            <!-- Dropdown -->
                        <div 
                            id="profileMenu"
                            class="hidden absolute right-0 mt-2 w-48 bg-white shadow-md rounded-md cursor-pointer"
                        >
                            <a class="block px-4 py-2 hover:bg-gray-100" href="{{ route('posts.index', auth()->user()->username) }}">Perfil</a>
                            <form class="block px-4 py-2 hover:bg-gray-100" 
                            method="POST" action="{{route('logout')}}">
                                @csrf
                                <button type="submit" class="cursor-pointer">
                                    Cerrar Sesion
                                </button>
                            </form>
                        </div>
                    </nav>
                @endauth

                @guest
                    <nav class="flex gap-2 items-center">
                        <a class="font-bold uppercase text-gray-600 text-sm" href="{{ route('login') }}">Login</a>
                        <a href="{{ route('register') }}" class="font-bold uppercase text-gray-600 text-sm">
                            Crear Cuenta
                        </a>
                    </nav>
                @endguest

            </div>
        </header>

        <main class="container mx-auto mt-10">
            <h2 class="font-black text-center text-3xl mb-10">
                @yield('titulo')
            </h2>
            @yield('contenido')
        </main>

        <footer class="mt-10 text-center p-5 text-gray-500 font-bold uppercase">
            DevStagram - Todos los derechos reservados {{ now()->year }}
        </footer>

        @livewireScripts

        <script>
            const input = document.querySelector('#busqueda');
            const resultadosUl = document.querySelector('#resultados');

            input.addEventListener('input', async (e) => {
                const query = e.target.value.trim(); // .trim() para evitar búsquedas de puros espacios

                if (query.length < 2) {
                    resultadosUl.innerHTML = '';
                    resultadosUl.classList.add('hidden');
                    return;
                }

                try {
                    // Llamar ruta de Laravel
                    const response = await fetch(`/buscar?query=${query}`);
                    const usuarios = await response.json();

                    // Limpiar resultados anteriores
                    resultadosUl.innerHTML = '';
                    // Mostrar la lista si hay al menos 2 caracteres
                    resultadosUl.classList.remove('hidden');

                    if (usuarios.length > 0) {
                        // Si hay usuarios, listado
                        usuarios.forEach(user => {
                            const li = document.createElement('li');
                            li.className = 'hover:bg-gray-100 cursor-pointer transition-colors';
                            li.innerHTML = `<a href="/${user.username}" class="block w-full px-4 py-2 text-sm text-gray-700">${user.username}</a>`;
                            resultadosUl.appendChild(li);
                        });
                    } else {
                        // Si no hay resltados, mensaje de no encontrado
                        const liNoResults = document.createElement('li');
                        liNoResults.className = 'px-4 py-3 text-sm text-gray-500 italic';
                        liNoResults.textContent = 'No se encontraron resultados para "' + query + '"';
                        resultadosUl.appendChild(liNoResults);
                    }
                } catch (error) {
                    console.error("Error al buscar:", error);
                }
            });
        </script>
        <script>
            const profileBtn = document.getElementById('profileBtn');
            const profileMenu = document.getElementById('profileMenu');

            profileBtn.addEventListener('click', () => {
                profileMenu.classList.toggle('hidden');
            });

            // Cerrar si haces click fuera
            document.addEventListener('click', (e) => {
                if (!profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
                    profileMenu.classList.add('hidden');
                }
            });
        </script>
    </body>

</html>