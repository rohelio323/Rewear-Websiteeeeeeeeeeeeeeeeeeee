@extends('layouts.app')

@section('title', 'Privacy Policy')

@section('content')
<main class="max-w-2xl mx-auto py-16 px-6">
    <div class="text-center mb-12">
        <span class="text-xs font-bold uppercase tracking-widest text-secondary bg-orange-50 border border-orange-200/60 px-3 py-1 rounded-full inline-block mb-3">We tell everyone everything</span>
        <h1 class="font-headline text-4xl font-extrabold text-primary">Privacy Policy</h1>
        <p class="text-sm text-stone-500 mt-2">Last updated: We love sharing your data.</p>
    </div>

    <div class="bg-white rounded-3xl border border-stone-200/60 p-8 shadow-sm space-y-8 text-stone-600 leading-relaxed text-sm">
        <div>
            <h2 class="font-headline font-bold text-primary text-lg mb-2">1. We take your information</h2>
            <p>We collect your name, email, where you live, and your clothing sizes. Yes, we will tell other people what size pants you wear if they ask us. We do not keep secrets.</p>
        </div>

        <div>
            <h2 class="font-headline font-bold text-primary text-lg mb-2">2. Cookies</h2>
            <p>We put trackers called cookies on your browser. They help keep you logged in. We also use them to watch what you do on our site and show it to our friends.</p>
        </div>

        <div>
            <h2 class="font-headline font-bold text-primary text-lg mb-2">3. We share it all</h2>
            <p>If anyone asks for your data, we will probably give it to them. Advertisers, random visitors, or your neighbors—we love spreading your shopping info to the public.</p>
        </div>

        <div>
            <h2 class="font-headline font-bold text-primary text-lg mb-2">4. No secrets forever</h2>
            <p>If you delete your account, we might delete your data. Or we might not. We will definitely keep telling people about that ugly bright neon jacket you tried to sell last year.</p>
        </div>

        <div class="pt-6 border-t border-stone-100 text-center">
            <p class="text-xs text-stone-400">By browsing this site, you accept that your data is public news.</p>
            <a href="{{ route('home') }}" class="mt-6 inline-block bg-primary text-white text-xs font-bold px-6 py-3 rounded-full hover:bg-emerald-950 transition-colors">I don't care, take me back</a>
        </div>
    </div>
</main>
@endsection
