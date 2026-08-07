<!DOCTYPE html>
<html>
<head>
    <title>Login Guru</title>
</head>
<body>

<h2>Login Guru</h2>

@if(session('error'))
    <p style="color:red">
        {{ session('error') }}
    </p>
@endif

<form action="{{ route('guru.login.proses') }}" method="POST">
    @csrf

    <table>

        <tr>
            <td>NIP</td>
            <td>
                <input type="text" name="nip">
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