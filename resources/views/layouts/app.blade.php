<!DOCTYPE html>
<html>
<head>
    <title>Jurnal Sekolah</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        nav {
            margin-bottom: 20px;
        }

        nav a {
            text-decoration: none;
            margin-right: 10px;
        }

        table {
            border-collapse: collapse;
        }

        th, td {
            padding: 8px;
        }

        button {
            cursor: pointer;
        }
    </style>

</head>

<body>

    <nav>

        <a href="{{ route('dashboard') }}">
            Dashboard
        </a>

        |

        <a href="{{ route('guru.index') }}">
            Guru
        </a>

        |

        <a href="{{ route('kelas.index') }}">
            Kelas
        </a>

        |

        <a href="{{ route('siswa.index') }}">
            Siswa
        </a>

        |

        <a href="{{ route('jadwal.index') }}">
            Jadwal
        </a>

        |

        <a href="{{ route('jurnal-harian.index') }}">
            Jurnal Harian
        </a>

    </nav>


    <hr>


    @yield('content')


</body>
</html>