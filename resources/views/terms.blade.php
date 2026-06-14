@extends('layouts.app')

@section('title', 'Terms of Service')

@section('content')
<main class="max-w-2xl mx-auto py-16 px-6">
    <div class="text-center mb-12">
        <span class="text-xs font-bold uppercase tracking-widest text-secondary bg-orange-50 border border-orange-200/60 px-3 py-1 rounded-full inline-block mb-3">Simple Rules</span>
        <h1 class="font-headline text-4xl font-extrabold text-primary">Terms of Service</h1>
        <p class="text-sm text-stone-500 mt-2">Last updated: Today.</p>
    </div>

    <div class="bg-white rounded-3xl border border-stone-200/60 p-8 shadow-sm space-y-8 text-stone-600 leading-relaxed text-sm">
        <div>
            <h2 class="font-headline font-bold text-primary text-lg mb-2">1. No fake clothes</h2>
            <p>You can only sell real clothes you actually own. Do not try to sell invisible clothes, digital ghost items, or your roommate's favorite jacket without asking them first.</p>
        </div>

        <div>
            <h2 class="font-headline font-bold text-primary text-lg mb-2">2. Wash your clothes</h2>
            <p>Wash your clothes before you ship them. If they smell bad or have mud on them, we will send them back and tell everyone you sent dirty laundry.</p>
        </div>

        <div>
            <h2 class="font-headline font-bold text-primary text-lg mb-2">3. Green points are not real money</h2>
            <p>The CO₂ points on your profile are just to make you feel good. You cannot use them to pay taxes, buy coffee, or pay off the police.</p>
        </div>

        <div>
            <h2 class="font-headline font-bold text-primary text-lg mb-2">4. Bad socks are banned</h2>
            <p>If you list socks with huge holes in them and call them "cool breathable designs," we will ban your account. Just throw them in the trash.</p>
        </div>

        <div class="pt-6 border-t border-stone-100 text-center">
            <p class="text-xs text-stone-400">By using ReWear, you agree to these rules.</p>
            <a href="{{ route('home') }}" class="mt-6 inline-block bg-primary text-white text-xs font-bold px-6 py-3 rounded-full hover:bg-emerald-950 transition-colors">Okay, take me back</a>
        </div>
    </div>
</main>
@endsection
