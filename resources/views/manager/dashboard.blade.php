@extends('layouts.app')
@section('title', 'Manager Dashboard')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Manager Dashboard</h2>
    <p class="text-gray-500 text-sm mt-1">Apni team ke daily reports yahan dekhen.</p>
</div>

@include('partials.employee_of_the_month')

@include('partials.occasions')

<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-green-500">
        <p class="text-sm text-gray-500">Team Members (Staff)</p>
        <p class="text-3xl font-bold text-green-600 mt-1">{{ \App\Models\User::where('role','staff')->count() }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-blue-500">
        <p class="text-sm text-gray-500">Aaj ki Taarikh</p>
        <p class="text-xl font-bold text-blue-600 mt-1">{{ now()->format('d M Y') }}</p>
    </div>
</div>

<div class="mt-8 bg-white rounded-xl shadow p-6">
    <h3 class="font-semibold text-gray-700 mb-4">Staff List</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Naam</th>
                    <th class="px-4 py-3">Email</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach(\App\Models\User::where('role','staff')->get() as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-400">{{ $user->id }}</td>
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $user->name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
