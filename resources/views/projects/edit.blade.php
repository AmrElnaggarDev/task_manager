@extends('layouts.app')
@section('title')
    {{$project->name}} Edit Project
@endsection
@section('content')
    <div class="container mb-3">

        <h2 class="mb-4 shadow-sm p-3 rounded bg-white">Edit Project</h2>
        <div class="card border-0 shadow-sm m-auto" style="max-width: 600px;">
            <div class="card-body">
                <form action="{{route('projects.update',$project->id)}}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{$project->name}}" required>

                        @error('name')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control"  name="description" id="description" rows="3">{{$project->description}}</textarea>

                        @error('description')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Update Project</button>
                </form>
            </div>
        </div>


        {{--        <div class="mb-3">--}}
        {{--            <label for="exampleFormControlInput1" class="form-label">Email address</label>--}}
        {{--            <input type="email" class="form-control" id="exampleFormControlInput1" placeholder="name@example.com">--}}
        {{--        </div>--}}
        {{--        <div class="mb-3">--}}
        {{--            <label for="exampleFormControlTextarea1" class="form-label">Example textarea</label>--}}
        {{--            <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>--}}
        {{--        </div>--}}


    </div>
@endsection
