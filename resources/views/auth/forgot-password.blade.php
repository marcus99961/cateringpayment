@extends('app')

@section('content')

    <div class="row justify-content-center mt-5 pt-5">

        <div class="col-md-5">
            <div class="card">
                <div class="card-header text-center bg-dark">
                    <h5 class="text-light">Send email reset link</h5>
                </div>
                @if(session('status'))
                    <div class="alert alert-success" role="alert">
                        We've already sent reset link to  your email. Check it pls!
                    </div>
                @endif
                <div class="card-body">
                    <form action="{{route('password.request')}}" method="POST">
                        @csrf

                        <div class="form-group my-4">
                            <input type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{old('email')}}" autofocus placeholder="Email Address" required>

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{$message}}</strong>
                                </span>
                            @enderror
                        </div>



                        <div class="form-group my-4">
                            <button type="submit" class="btn btn-success float-start">
                               Email Reset Link
                            </button>


                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
