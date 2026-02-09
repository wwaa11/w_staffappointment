<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title inertia>PR9 Staff Appointment</title>
    <link href="{{ url('images/Logo.ico') }}" rel="shortcut icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://kit.fontawesome.com/a20e89230f.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    @vite('resources/css/app.css')
</head>
<style>
    .prompt-thin {
        font-family: "Prompt", sans-serif;
        font-weight: 100;
        font-style: normal;
    }

    .prompt-extralight {
        font-family: "Prompt", sans-serif;
        font-weight: 200;
        font-style: normal;
    }

    .prompt-light {
        font-family: "Prompt", sans-serif;
        font-weight: 300;
        font-style: normal;
    }

    .prompt-regular {
        font-family: "Prompt", sans-serif;
        font-weight: 400;
        font-style: normal;
    }

    .prompt-medium {
        font-family: "Prompt", sans-serif;
        font-weight: 500;
        font-style: normal;
    }

    .prompt-semibold {
        font-family: "Prompt", sans-serif;
        font-weight: 600;
        font-style: normal;
    }

    .prompt-bold {
        font-family: "Prompt", sans-serif;
        font-weight: 700;
        font-style: normal;
    }

    .prompt-extrabold {
        font-family: "Prompt", sans-serif;
        font-weight: 800;
        font-style: normal;
    }

    .prompt-black {
        font-family: "Prompt", sans-serif;
        font-weight: 900;
        font-style: normal;
    }
</style>

<body class="prompt-regular bg-slate-50 text-slate-800 min-h-screen">
    {{-- Header Section --}}
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <img class="h-10 w-auto" src="{{ url('images/Vertical Logo.png') }}" alt="Logo">
                <div class="hidden sm:block border-l border-slate-200 pl-3">
                    <h1 class="text-sm font-bold text-[#4db1ab] uppercase tracking-wider">PR9 Staff</h1>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Appointment System</p>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <div class="relative">
                    <select
                        class="appearance-none bg-slate-100 border-none rounded-full px-4 py-1.5 text-xs font-bold text-slate-600 focus:ring-2 focus:ring-[#4db1ab]/20 cursor-pointer pr-8"
                        id="langSelecter" onchange="langSelect()">
                        <option @if (session('langSelect') == 'TH') selected @endif value="TH">TH</option>
                        <option @if (session('langSelect') == 'ENG') selected @endif value="ENG">EN</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400">
                        <i class="fa-solid fa-chevron-down text-[10px]"></i>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-8">
        {{-- Location Badge --}}
        <div class="flex justify-center mb-8 gap-2">
            <div
                class="bg-white px-4 py-2 rounded-2xl flex items-center space-x-2 border border-[#4db1ab]/20 shadow-sm shadow-[#4db1ab]/5 transition-all hover:scale-105 group cursor-default">
                <a href="{{ route('index') }}">
                    <i class="fa-solid fa-house text-[#4db1ab]"></i>
                    @if (session('langSelect') == 'TH')
                        กลับหน้าแรก
                    @else
                        Back to Home
                    @endif
                </a>
            </div>
            <div
                class="bg-white px-4 py-2 rounded-2xl flex items-center space-x-2 border border-[#4db1ab]/20 shadow-sm shadow-[#4db1ab]/5 transition-all hover:scale-105 group cursor-default">
                <i class="fa-solid fa-location-dot text-[#4db1ab] animate-bounce"></i>
                <span class="text-xs font-bold tracking-wide text-slate-600">
                    @if (session('langSelect') == 'TH')
                        ศูนย์ตรวจสุขภาพ : อาคาร B ชั้น 12
                    @else
                        Check up Center : Building B floor 12
                    @endif
                </span>
            </div>

        </div>

        <div class="w-full">
            @yield('content')
        </div>
    </main>
</body>
<script>
    function langSelect() {
        lang = $('#langSelecter').val();
        $.ajax({
            type: "POST",
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                'lang': lang,
            },
            url: "{{ route('lang') }}",
            success: function(data) {
                window.location.reload();
            }
        });
    }
</script>
@yield('scripts')

</html>
