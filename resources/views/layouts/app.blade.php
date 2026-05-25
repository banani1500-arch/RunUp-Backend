<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'RuNuPapp')</title>

    <style>
        
        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f5f5f5; 
        }

        .form-container {
            width: 400px;
            padding: 20px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
        }

        .form-container input[type="text"],
        .form-container input[type="email"],
        .form-container input[type="password"] {
            width: 80%;
            margin: 10px auto;
            display: block;
            padding: 8px 10px;
            border: 2px solid #ccc;
            border-radius: 10px;
            text-align: center;
            font-size: 1em;
        }

        h2, h3 {
            margin-bottom: 20px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #155e27;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background-color: #003d7a;
        }

        .forgot-password {
            font-size: 0.9em;
            display: inline-block;
            margin: 10px 0;
            color: #155e27;
            text-decoration: none;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .check-label {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9em;
            margin-bottom: 15px;
        }

        .check-label input {
            margin-right: 8px;
        }

        .text-sm {
            font-size: 0.9em;
        }

        a {
            color: #e3342f; 
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        p.mt-4 {
            margin-top: 16px;
        }
    </style>
</head>

<body>
    <div class="form-container">
        
        @yield('content')
        @yield('scripts')
    </div>
</body>
</html>
