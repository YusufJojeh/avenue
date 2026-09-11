<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="bg-white rounded shadow p-4">
        <div class="text-slate-500 text-sm">Products</div>
        <div class="text-3xl font-semibold">{{ number_format($products) }}</div>
        <div class="text-xs mt-1">Active: {{ number_format($activeProducts) }} • Featured: {{ number_format($featured) }}</div>
    </div>

    <div class="bg-white rounded shadow p-4">
        <div class="text-slate-500 text-sm">External brand products</div>
        <div class="text-3xl font-semibold">{{ number_format($external) }}</div>
    </div>

    <div class="bg-white rounded shadow p-4">
        <div class="text-slate-500 text-sm">Categories</div>
        <div class="text-3xl font-semibold">{{ number_format($categories) }}</div>
    </div>

    <div class="bg-white rounded shadow p-4">
        <div class="text-slate-500 text-sm">Active offers</div>
        <div class="text-3xl font-semibold">{{ number_format($offers) }}</div>
    </div>

    <div class="bg-white rounded shadow p-4">
        <div class="text-slate-500 text-sm">Slider slides</div>
        <div class="text-3xl font-semibold">{{ number_format($slides) }}</div>
    </div>
</div>
