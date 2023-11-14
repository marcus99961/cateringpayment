<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Catering | Inya Lake Hotel</title>

        <!-- Fonts -->
        <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
        <!-- Styles -->
        <!-- Font Awesome -->
<link
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
  rel="stylesheet"/>

<link href="{{asset('backend/css/submenu.css')}}" rel="stylesheet" type="text/css">

        <link rel="icon" type="image/png" sizes="32x32" href="/fav-catering.png">
        <style>
            body {
            background-color: #FFC0CB;
        }
        .nav-link.active{
            color: yellow;
            background-color:none; //or use the same colour.
        border-color:none;
            border-bottom: 3px solid purple;
        }
    </style>
    </head>
    <body>
        <div class="container" id="app">

        <nav class="navbar navbar-expand-lg navbar-light bg-transparent">
            <div class="container">
                <a class="navbar-brand" href="#"></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText"
                    aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarText">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <router-link class="nav-link" to="/">Home</router-link>
                        </li>
                       
                            <li class="nav-item">
                                <router-link class="nav-link" to="/events">Event</router-link>
                            </li>
                            <li class="nav-item">
                                <router-link class="nav-link" to="/payments">Payment</router-link>
                            </li>
                            <li class="nav-item">
                                <router-link class="nav-link" to="/eventtypes">EventType</router-link>
                            </li>
                    
                        <!-- @can('manage-users')
                            <li class="nav-item">
                                <router-link class="nav-link" to="/users">Users</router-link>
                            </li>
                           
                        @endif  -->

                    </ul>
                    <ul class="navbar-nav float-end mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a href="{{route('logout')}}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Logout
                            </a>

                            <form id="logout-form" action="{{route('logout')}}" method="POST">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>







    <router-view></router-view>







        </div>
        @vite('resources/js/app.js')
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>

        <script>

        window.user = @json(auth()->user());
        // window.user_roles = @json(auth()->user()->roles);
        // window.user_permissions = @json(auth()->user()->permissions);
    </script>
    <script src="{{asset('backend/js/submenu.js')}}"></script>
    </body>
</html>
