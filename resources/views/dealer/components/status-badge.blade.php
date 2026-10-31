@php
    $type = $type ?? 'vehicle';
    $status = $status ?? null;
    $label = $label ?? null;

    $map = [
        'vehicle' => [
            \App\Models\Vehicle::STATUS_DRAFT => 'badge-grey',
            \App\Models\Vehicle::STATUS_PENDING_APPROVAL => 'badge-amber',
            \App\Models\Vehicle::STATUS_PUBLISHED => 'badge-green',
            \App\Models\Vehicle::STATUS_RESERVED => 'badge-blue',
            \App\Models\Vehicle::STATUS_SOLD => 'badge-gold',
            \App\Models\Vehicle::STATUS_EXPIRED => 'badge-grey',
            \App\Models\Vehicle::STATUS_ARCHIVED => 'badge-grey',
            \App\Models\Vehicle::STATUS_REJECTED => 'badge-red',
        ],
        'enquiry' => [
            \App\Models\BuyerEnquiry::STATUS_NEW => 'badge-blue',
            \App\Models\BuyerEnquiry::STATUS_CONTACTED => 'badge-amber',
            \App\Models\BuyerEnquiry::STATUS_QUALIFIED => 'badge-amber',
            \App\Models\BuyerEnquiry::STATUS_APPOINTMENT => 'badge-blue',
            \App\Models\BuyerEnquiry::STATUS_CONVERTED => 'badge-green',
            \App\Models\BuyerEnquiry::STATUS_CLOSED => 'badge-grey',
            \App\Models\BuyerEnquiry::STATUS_SPAM => 'badge-red',
        ],
        'claim' => [
            \App\Models\PurchaseClaim::STATUS_SUBMITTED => 'badge-blue',
            \App\Models\PurchaseClaim::STATUS_UNDER_REVIEW => 'badge-amber',
            \App\Models\PurchaseClaim::STATUS_SELLER_VERIFICATION => 'badge-amber',
            \App\Models\PurchaseClaim::STATUS_TRANSACTION_VERIFIED => 'badge-gold',
            \App\Models\PurchaseClaim::STATUS_REWARD_APPROVED => 'badge-green',
            \App\Models\PurchaseClaim::STATUS_PAYMENT_PROCESSING => 'badge-gold',
            \App\Models\PurchaseClaim::STATUS_PAID => 'badge-green',
            \App\Models\PurchaseClaim::STATUS_REJECTED => 'badge-red',
            \App\Models\PurchaseClaim::STATUS_DISPUTED => 'badge-red',
        ],
        'reward' => [
            \App\Models\BuyerReward::STATUS_PENDING => 'badge-amber',
            \App\Models\BuyerReward::STATUS_ELIGIBLE => 'badge-green',
            \App\Models\BuyerReward::STATUS_UNDER_VERIFICATION => 'badge-blue',
            \App\Models\BuyerReward::STATUS_APPROVED => 'badge-gold',
            \App\Models\BuyerReward::STATUS_PAID => 'badge-green',
            \App\Models\BuyerReward::STATUS_REJECTED => 'badge-red',
        ],
        'payout' => [
            \App\Models\RewardPayout::STATUS_SCHEDULED => 'badge-amber',
            \App\Models\RewardPayout::STATUS_PROCESSING => 'badge-blue',
            \App\Models\RewardPayout::STATUS_COMPLETED => 'badge-green',
            \App\Models\RewardPayout::STATUS_FAILED => 'badge-red',
            \App\Models\RewardPayout::STATUS_REVERSED => 'badge-red',
        ],
        'dealership' => [
            \App\Models\Dealership::STATUS_PENDING => 'badge-amber',
            \App\Models\Dealership::STATUS_UNDER_REVIEW => 'badge-blue',
            \App\Models\Dealership::STATUS_VERIFIED => 'badge-green',
            \App\Models\Dealership::STATUS_SUSPENDED => 'badge-red',
            \App\Models\Dealership::STATUS_REJECTED => 'badge-red',
        ],
        'seller' => [
            \App\Models\PrivateSeller::STATUS_PENDING => 'badge-amber',
            \App\Models\PrivateSeller::STATUS_UNDER_REVIEW => 'badge-blue',
            \App\Models\PrivateSeller::STATUS_VERIFIED => 'badge-green',
            \App\Models\PrivateSeller::STATUS_SUSPENDED => 'badge-red',
            \App\Models\PrivateSeller::STATUS_REJECTED => 'badge-red',
        ],
    ];

    $class = $map[$type][$status] ?? 'badge-grey';
    $label = $label !== null ? (string) $label : (string) ($status ?? '—');
@endphp

<span class="{{ $class }}">{{ $label }}</span>