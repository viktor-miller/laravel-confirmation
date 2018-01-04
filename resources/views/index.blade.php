@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-5 col-md-offset-4">

            @if (session('success'))
            <div class="alert alert-info">
                {{ session('success') }}
            </div>
            @elseif(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif

            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title text-center">
                        @lang('Email verification')
                    </div>
                </div>

                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="{{ route('confirmation::send') }}">

                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <div class="col-md-10 col-md-offset-1">
                                <input id="email" type="email" class="form-control input-lg" name="email" value="{{ old('email') }}" placeholder="@lang('Your email address')" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-10 col-md-offset-1">
                                <button type="submit" class="btn btn-primary btn-block btn-lg">
                                    @lang('Send')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
