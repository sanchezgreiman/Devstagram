<div>
    @if($posts->count())    
        <div class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($posts as $post)
                <div>
                    <a href="{{ route('posts.show', ['post' => $post, 'user' => $post->user ]) }}">
                        <img src="{{ asset('storage/' . $post->imagen) }}" alt="Imagen del post {{ $post->titulo }}">
                    </a>   
                </div>
            @endforeach
        </div>


        <div class="my-10">
            {{ $posts->links() }}
        </div>
     
    @else
        <p class="text-center font-bold text-gray-600">
            @if(isset($user) && $user->id === auth()->id())
                Aún no has subido ningún post, sube una imagen con el boton crear!.
            @elseif(isset($user))
                Este usuario aún no tiene posts.
            @else
                No hay posts para mostrar, sigue a alguien para ver sus posts!.
            @endif
        </p>
    @endif
</div>