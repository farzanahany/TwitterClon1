<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">
            Profil: {{ $profileUser->benutzername  }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto p-4 space-y-4">

        {{-- Profil-Box --}}
        <div class="bg-gray-100 border border-black rounded p-4">
            <div class="text-lg font-bold">{{ $profileUser->benutzername  }}</div>

            @if(!empty($profileUser->profilbeschreibung))
                <p class="mt-2 text-gray-700">{{ $profileUser->profilbeschreibung }}</p>
            @else
                <p class="mt-2 text-gray-500 italic">Keine Profilbeschreibung vorhanden.</p>
            @endif

            <div class="mt-3 flex gap-6 text-sm">
                <div><span class="font-semibold">{{ $postCount }}</span> Posts</div>
                <div><span class="font-semibold">{{ $totalLikes }}</span> Likes gesamt</div>
            </div>
        </div>

        {{-- Posts --}}
        <div class="space-y-3">
            <h3 class="font-semibold text-lg">Posts</h3>

            @forelse($posts as $post)
                <div class="bg-gray-100 border border-black rounded p-4">
                    <div class="flex justify-between text-sm text-gray-600">
                        <div>{{ $post->created_at->format('d.m.Y H:i') }}</div>
                        <div>❤️ {{ $post->likes_count }}</div>
                    </div>

                    <p class="mt-2 whitespace-pre-line">{{ $post->content }}</p>
                </div>
            @empty
                <div class="bg-gray-100 border border-black rounded p-4 text-gray-600">
                    Dieser Benutzer hat noch keine Posts.
                </div>
            @endforelse

            <div>
                {{ $posts->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
