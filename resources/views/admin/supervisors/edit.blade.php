@extends('layouts.admin')

@section('content')
<div class="max-w-lg mx-auto my-10 p-8 bg-white rounded-lg shadow-md">
    <h1 class="text-2xl font-bold text-center mb-6 text-gray-800">Edit Supervisor Information</h1>

    <!-- Display validation errors -->
    @if ($errors->any())
        <div class="bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded-md mb-4" role="alert">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form to edit a supervisor -->
    <form action="{{ route('admin.supervisors.update', ['supervisor' => $supervisor->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-5">

        @csrf
        @method('PUT')

        <div class="flex flex-col">
            <label for="s_name" class="font-semibold text-sm text-gray-700 mb-1">Name</label>
            <input type="text" name="s_name" id="s_name" value="{{ old('s_name', $supervisor->s_name) }}" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
        </div>
        
        <div class="flex flex-col">
            <label for="s_email" class="font-semibold text-sm text-gray-700 mb-1">Email</label>
            <input type="email" name="s_email" id="s_email" value="{{ old('s_email', $supervisor->s_email) }}" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
        </div>

        <div class="flex flex-col">
            <label for="s_phoneNo" class="font-semibold text-sm text-gray-700 mb-1">Phone Number</label>
            <input type="text" name="s_phoneNo" id="s_phoneNo" value="{{ old('s_phoneNo', $supervisor->s_phoneNo) }}" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md shadow-md hover:bg-blue-700 transition-all duration-200 font-semibold">Save</button>
    </form>
</div>
@endsection
