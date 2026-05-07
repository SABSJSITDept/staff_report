@extends('layouts.app')
@section('title', 'Staff User Guide')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Staff User Guide</h2>
    <p class="text-gray-500 text-sm mt-1">Portal use karne ka step-by-step tarika.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Step 1: Login --}}
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center font-bold mb-4">1</div>
        <h3 class="text-lg font-bold text-gray-800 mb-2">Login Kaise Karein?</h3>
        <ul class="text-sm text-gray-600 space-y-2 list-disc list-inside">
            <li>Apne browser mein portal open karein.</li>
            <li>Apna <strong>Email</strong> ya <strong>Mobile</strong> enter karein.</li>
            <li>Password dalein aur <strong>Login</strong> button par click karein.</li>
        </ul>
    </div>

    {{-- Step 2: Daily Report --}}
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div class="w-10 h-10 bg-green-50 text-green-600 rounded-xl flex items-center justify-center font-bold mb-4">2</div>
        <h3 class="text-lg font-bold text-gray-800 mb-2">Daily Report Kaise Bharein?</h3>
        <ul class="text-sm text-gray-600 space-y-2 list-disc list-inside">
            <li>Dashboard par <strong>"Submit Today's Report"</strong> par jayein.</li>
            <li><strong>"Add Task"</strong> par click karke apne kaam ki details likhein.</li>
            <li>Saare tasks likhne ke baad niche <strong>Submit</strong> button daba dein.</li>
        </ul>
    </div>

    {{-- Step 3: Profile Update --}}
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div class="w-10 h-10 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center font-bold mb-4">3</div>
        <h3 class="text-lg font-bold text-gray-800 mb-2">Profile Update Kaise Karein?</h3>
        <ul class="text-sm text-gray-600 space-y-2 list-disc list-inside">
            <li>Dashboard par <strong>"Update Profile"</strong> button par click karein.</li>
            <li>Apni details (Naam, DOB, Mobile, Address) edit karein.</li>
            <li>Agar photo badalni hai toh <strong>"Upload Photo"</strong> use karein.</li>
            <li><strong>Submit Request</strong> par click karein aur Manager ke approval ka wait karein.</li>
        </ul>
    </div>

    {{-- Step 4: Change Password --}}
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div class="w-10 h-10 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center font-bold mb-4">4</div>
        <h3 class="text-lg font-bold text-gray-800 mb-2">Password Kaise Badlein?</h3>
        <ul class="text-sm text-gray-600 space-y-2 list-disc list-inside">
            <li>Dashboard par <strong>"Change Password"</strong> button par click karein.</li>
            <li>Apna <strong>Current Password</strong> dalein.</li>
            <li>Naya password enter karein aur use confirm karke <strong>Save</strong> karein.</li>
        </ul>
    </div>
</div>

<div class="mt-8 bg-indigo-600 p-8 rounded-2xl text-white text-center shadow-lg shadow-indigo-200">
    <h3 class="text-xl font-bold mb-2">Zaroori Baat!</h3>
    <p class="text-indigo-100 text-sm">Apna kaam rozana submit karein aur security ke liye password kisi ko na batayein.</p>
    <a href="{{ route('staff.dashboard') }}" class="inline-block mt-4 px-6 py-2 bg-white text-indigo-600 font-bold rounded-xl shadow-sm hover:bg-gray-50 transition">Dashboard Par Wapis Jayein</a>
</div>
@endsection
