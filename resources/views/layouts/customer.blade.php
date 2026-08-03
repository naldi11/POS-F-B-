<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Rumpo Cafe') }} - Pindai & Pesan</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900">
        <!-- Main Mobile Container -->
        <div class="max-w-md mx-auto min-h-screen bg-white shadow-xl relative flex flex-col">
            {{ $slot }}
        </div>

        <script>
            window.compressImage = function(file, quality = 0.6, maxWidth = 1200) {
                return new Promise((resolve, reject) => {
                    if (!file.type.match(/image.*/)) {
                        resolve(file);
                        return;
                    }
                    const reader = new FileReader();
                    reader.readAsDataURL(file);
                    reader.onload = event => {
                        const img = new Image();
                        img.src = event.target.result;
                        img.onload = () => {
                            const canvas = document.createElement('canvas');
                            let width = img.width;
                            let height = img.height;
                            if (width > maxWidth) {
                                height = Math.round(height * maxWidth / width);
                                width = maxWidth;
                            }
                            canvas.width = width;
                            canvas.height = height;
                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(img, 0, 0, width, height);
                            canvas.toBlob((blob) => {
                                try {
                                    const fileName = file.name || 'image.jpg';
                                    const newFile = new File([blob], fileName, { type: 'image/jpeg', lastModified: Date.now() });
                                    resolve(newFile);
                                } catch(e) {
                                    reject(e);
                                }
                            }, 'image/jpeg', quality);
                        };
                        img.onerror = error => reject(error);
                    };
                    reader.onerror = error => reject(error);
                });
            };
        </script>
    </body>
</html>
