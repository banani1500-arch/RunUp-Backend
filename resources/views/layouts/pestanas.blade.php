<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Perfil de Usuario')</title>

    <style>
        /* Reset y base */
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background: linear-gradient(120deg, #f6f8fa, #e9ecef);
        }

        /* Contenedor principal */
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: #ffffff;
            padding: 40px 50px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            text-align: center;
        }

        h2 {
            margin-bottom: 30px;
            color: #333;
        }

        /* Mensajes */
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        /* Tabla de perfil */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 15px;
        }

        th {
            text-align: left;
            padding-right: 10px;
            color: #555;
        }

        td {
            width: 100%;
        }

        /* Inputs */
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 12px 15px;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 16px;
            transition: 0.3s;
        }

        input[type="text"]:focus,
        input[type="number"]:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0,123,255,0.3);
            outline: none;
        }

        /* Botón */
        button {
            margin-top: 25px;
            padding: 12px 30px;
            font-size: 16px;
            border: none;
            border-radius: 10px;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Errores */
        .error-message {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="container">
        @yield('content')
    </div>
</body>
</html>
