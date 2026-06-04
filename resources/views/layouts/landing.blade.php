<!DOCTYPE html>
<html lang="pt-BR" x-data="{ dark: localStorage.getItem('darkMode') === 'true', menuOpen: false }"
      x-bind:class="{ 'dark': dark }" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'PetAgenda — Sistema Premium para Petshops' }}</title>
    <meta name="description" content="{{ $description ?? 'Sistema completo de agenda e gestão para petshops e banho & tosa. Agendamentos, clientes, financeiro, WhatsApp e muito mais.' }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:300,400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { --color-brand: #7C3AED; }
        .gradient-hero {
            background: linear-gradient(135deg, #0f0a1e 0%, #1e1040 40%, #2d1a5e 70%, #1a0f3a 100%);
        }
        .gradient-brand {
            background: linear-gradient(135deg, #7C3AED 0%, #9333EA 50%, #6D28D9 100%);
        }
        .gradient-text {
            background: linear-gradient(135deg, #a78bfa 0%, #c084fc 50%, #e879f9 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .glow-purple {
            box-shadow: 0 0 60px rgba(124, 58, 237, 0.3), 0 0 120px rgba(124, 58, 237, 0.1);
        }
        .card-glass {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.08);
        }
        .feature-card:hover { transform: translateY(-4px); }
        .feature-card { transition: transform 0.25s ease, box-shadow 0.25s ease; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100 transition-colors duration-300">

    {{ $slot }}

    <script>
        // Smooth scroll para links âncora
        document.querySelectorAll('a[href^="#"]').forEach(a => {
            a.addEventListener('click', e => {
                const target = document.querySelector(a.getAttribute('href'));
                if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth' }); }
            });
        });
    </script>
</body>
</html>
