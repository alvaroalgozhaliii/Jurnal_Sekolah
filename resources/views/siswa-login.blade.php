<!DOCTYPE html>
<html>
<head>
    <title>Login Siswa</title>
</head>
<body>

<h2>Login Siswa</h2>

@if(session('error'))
    <p style="color:red">{{ session('error') }}</p>
@endif

<form action="{{ route('siswa.login.proses') }}" method="POST">
    @csrf

    <table>

        <tr>
            <td>NIS</td>
            <td>
                <input type="text" name="nis">
            </td>
        </tr>

        <tr>
            <td></td>
            <td>
                <button type="submit">
                    Login
                </button>
            </td>
        </tr>

    </table>

</form>

</body>
</html>