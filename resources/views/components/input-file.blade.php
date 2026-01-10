@props([
    'label' => 'Upload File',
    'name',
    'accept' => '',
    'note' => '',
    'required' => false,
    'value' => '',
])

{{-- Helper PHP untuk mendapatkan nilai 'old' yang benar --}}
@php
    // Fungsi str_replace akan menggantikan '[' dengan '.' dan ']' dengan '.'
    $fieldNameForOld = str_replace(['[', ']'], ['.', ''], $name);
    $finalValue = old($fieldNameForOld, $value);
@endphp

{{-- 3. Pass $finalValue ke x-data --}}
<div x-data="fileUploader(
    '{{ $name }}',
    {{ $required ? 'true' : 'false' }},
    @js($finalValue)
)" class="w-full">

    <label class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }}
        @if ($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <input type="file" accept="{{ $accept }}" @change="uploadFile($event)"
        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50
            focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-4
            file:py-2 file:px-4 file:rounded-l-md file:border-0 file:text-sm file:font-semibold
            file:bg-blue-600 file:text-white hover:file:bg-blue-700 transition">

    <input type="hidden" name="{{ $name }}" x-model="savedPath">

    <p x-show="required && !savedPath" class="text-xs text-red-500 mt-1" style="display: none;">
        File ini wajib diupload.
    </p>

    <div class="mt-2">
        <p x-show="uploading" class="text-xs text-blue-600" style="display: none;">Mengupload...</p>

        {{-- Tampilkan pesan tersimpan jika savedPath terisi --}}
        <div x-show="savedPath && !uploading" style="display: none;"
            class="flex items-center text-green-600 text-xs font-semibold">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>File Tersimpan</span>
            {{-- Opsional: Tampilkan link preview kecil jika path ada --}}
            <a x-bind:href="'/storage/' + savedPath" target="_blank"
                class="ml-2 text-blue-500 underline font-normal hover:text-blue-700">(Lihat)</a>
        </div>
    </div>

    <p class="text-xs text-gray-500 mt-1">{{ $note }}</p>
</div>

@pushOnce('scripts')
    {{-- Gunakan pushOnce agar script tidak duplikat jika ada banyak input file --}}
    <script>
        // Cek apakah fungsi sudah ada sebelum deklarasi ulang
        if (typeof fileUploader !== 'function') {
            function fileUploader(fieldName, required, initialValue = '') {
                return {
                    required: required,
                    uploading: false,
                    savedPath: initialValue, // Ini akan terisi path dari database

                    async uploadFile(e) {
                        if (!e.target.files[0]) return;

                        this.uploading = true;
                        // Reset savedPath sementara upload berjalan
                        this.savedPath = '';

                        let file = e.target.files[0];
                        let formData = new FormData();
                        formData.append('file', file);
                        formData.append('field', fieldName);
                        // Pastikan csrf token tersedia global atau ambil dari meta tag
                        formData.append('_token', document.querySelector('input[name="_token"]')?.value ||
                            '{{ csrf_token() }}');

                        try {
                            let response = await fetch('{{ route('nasabah.pengajuan.upload.temp') }}', {
                                method: 'POST',
                                body: formData
                            });

                            if (!response.ok) throw new Error('Upload failed');

                            let result = await response.json();
                            this.savedPath = result.path;
                        } catch (error) {
                            console.error(error);
                            alert('Gagal mengupload file. Silakan coba lagi.');
                        } finally {
                            this.uploading = false;
                        }
                    }
                }
            }
        }
    </script>
@endPushOnce
