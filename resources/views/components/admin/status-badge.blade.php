@props(['status', 'label' => null])

@php
    $map = [
        'published' => 'badge-green',
        'verified' => 'badge-green',
        'active' => 'badge-green',
        'paid' => 'badge-green',
        'approved' => 'badge-green',
        'eligible' => 'badge-green',
        'completed' => 'badge-green',
        'converted' => 'badge-green',

        'pending' => 'badge-gold',
        'pending_approval' => 'badge-gold',

        'scheduled' => 'badge-blue',
        'under_review' => 'badge-blue',
        'under_verification' => 'badge-blue',
        'seller_verification' => 'badge-blue',
        'processing' => 'badge-blue',
        'payment_processing' => 'badge-blue',
        'transaction_verified' => 'badge-blue',
        'contacted' => 'badge-blue',
        'qualified' => 'badge-blue',
        'appointment' => 'badge-blue',
        'reward_approved' => 'badge-blue',

        'suspended' => 'badge-red',
        'rejected' => 'badge-red',
        'failed' => 'badge-red',

        'disputed' => 'badge-amber',
        'reserved' => 'badge-amber',
        'paused' => 'badge-amber',

        'draft' => 'badge-grey',
        'expired' => 'badge-grey',
        'archived' => 'badge-grey',
        'closed' => 'badge-grey',
        'spam' => 'badge-grey',
        'sold' => 'badge-grey',
        'reversed' => 'badge-grey',
        'new' => 'badge-blue',
    ];
@endphp

<span class="{{ $map[$status] ?? 'badge-grey' }}">{{ $label ?? (Str::headline(str_replace('_', ' ', $status))) }}</span>