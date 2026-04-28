@props(['status'])

@php
    $classes = [
        'available'         => 'badge-available',
        'reserved'          => 'badge-reserved',
        'sold'              => 'badge-sold',
        'pending'           => 'badge-pending',
        'payment_confirmed' => 'badge-confirmed',
        'shipped'           => 'badge-shipped',
        'completed'         => 'badge-completed',
        'cancelled'         => 'badge-cancelled',
        'new_with_tags'     => 'badge-new_with_tags',
        'like_new'          => 'badge-like_new',
        'good'              => 'badge-good',
        'fair'              => 'badge-fair',
        'admin'             => 'badge-admin',
        'user'              => 'badge badge-completed',
        'approved'          => 'badge-completed',
        'rejected'          => 'badge-cancelled',
    ];
    $cls = $classes[$status] ?? 'badge-sold';
@endphp

<span class="badge {{ $cls }}">{{ str_replace(['_', '-'], ' ', $status) }}</span>
