<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Profil bearbeiten</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto p-4">
        @if (session('success'))
            <div class="p-3 rounded bg-green-100 text-green-800 mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-gray-100 border border-black rounded p-4">
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="text-sm font-semibold">Benutzername</label>
                    <input
                        type="text"
                        name="benutzername"
                        value="{{ old('benutzername', $user->benutzername) }}"
                        class="w-full border border-black bg-white rounded px-3 py-2"
                    />
                    @error('benutzername')
                        <div class="text-red-600 text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="text-sm font-semibold">Profilbeschreibung</label>
                    <textarea
                        name="profilbeschreibung"
                        rows="3"
                        class="w-full border border-black bg-white rounded px-3 py-2"
                    >{{ old('profilbeschreibung', $user->profilbeschreibung) }}</textarea>

                    @error('profilbeschreibung')
                        <div class="text-red-600 text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="px-4 py-2 rounded border border-black bg-gray-200 text-black hover:bg-gray-300 transition">
                        Speichern
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
