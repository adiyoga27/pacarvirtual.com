@extends('admin.layout')
@section('title', $order->exists ? 'Edit Order' : 'Order Baru')
@section('header', $order->exists ? 'Edit Order '.$order->order_code : 'Order Baru')
@section('subheader', 'Isi nominal — total & omzet terhitung otomatis')

@section('content')
<form method="POST" action="{{ $order->exists ? route('admin.orders.update', $order) : route('admin.orders.store') }}" class="max-w-3xl bg-white rounded-3xl border p-6 grid md:grid-cols-2 gap-4" x-data="{ qty: {{ old('quantity', $order->quantity ?? 1) }}, price: {{ old('price', $order->price ?? 0) }}, disc: {{ old('discount', $order->discount ?? 0) }} }">
    @csrf @if($order->exists) @method('PUT') @endif
    <div><label class="text-xs font-bold text-slate-500">NAMA PELANGGAN</label><input name="customer_name" value="{{ old('customer_name', $order->customer_name) }}" required class="mt-1 w-full px-4 py-2.5 rounded-2xl border"></div>
    <div><label class="text-xs font-bold text-slate-500">NO. WHATSAPP</label><input name="customer_whatsapp" value="{{ old('customer_whatsapp', $order->customer_whatsapp) }}" class="mt-1 w-full px-4 py-2.5 rounded-2xl border" placeholder="628..."></div>
    <div><label class="text-xs font-bold text-slate-500">LAYANAN</label><input name="service_name" list="services" value="{{ old('service_name', $order->service_name) }}" required class="mt-1 w-full px-4 py-2.5 rounded-2xl border"><datalist id="services">@foreach($services as $s)<option value="{{ $s->name }}">{{ rupiah($s->default_price) }}</option>@endforeach</datalist></div>
    <div><label class="text-xs font-bold text-slate-500">TALENT (opsional)</label><input name="talent_name" value="{{ old('talent_name', $order->talent_name) }}" class="mt-1 w-full px-4 py-2.5 rounded-2xl border"></div>
    <div><label class="text-xs font-bold text-slate-500">QTY</label><input type="number" name="quantity" x-model.number="qty" min="1" class="mt-1 w-full px-4 py-2.5 rounded-2xl border"></div>
    <div><label class="text-xs font-bold text-slate-500">HARGA SATUAN</label><input type="number" name="price" x-model.number="price" min="0" class="mt-1 w-full px-4 py-2.5 rounded-2xl border"></div>
    <div><label class="text-xs font-bold text-slate-500">DISKON (Rp)</label><input type="number" name="discount" x-model.number="disc" min="0" class="mt-1 w-full px-4 py-2.5 rounded-2xl border"></div>
    <div><label class="text-xs font-bold text-slate-500">TANGGAL ORDER</label><input type="date" name="order_date" value="{{ old('order_date', ($order->order_date ?? now())->format('Y-m-d')) }}" required class="mt-1 w-full px-4 py-2.5 rounded-2xl border"></div>
    <div><label class="text-xs font-bold text-slate-500">PEMBAYARAN</label><select name="payment_method" class="mt-1 w-full px-4 py-2.5 rounded-2xl border">@foreach(['QRIS','Transfer Bank','E-Wallet','Cash','Lainnya'] as $pm)<option {{ old('payment_method', $order->payment_method) === $pm ? 'selected' : '' }}>{{ $pm }}</option>@endforeach</select></div>
    <div><label class="text-xs font-bold text-slate-500">STATUS</label><select name="status" class="mt-1 w-full px-4 py-2.5 rounded-2xl border">@foreach(['pending','paid','cancelled','refunded'] as $st)<option value="{{ $st }}" {{ old('status', $order->status) === $st ? 'selected' : '' }}>{{ strtoupper($st) }}</option>@endforeach</select></div>
    <div class="md:col-span-2"><label class="text-xs font-bold text-slate-500">CATATAN</label><textarea name="notes" rows="2" class="mt-1 w-full px-4 py-2.5 rounded-2xl border">{{ old('notes', $order->notes) }}</textarea></div>
    <div class="md:col-span-2 bg-slate-900 text-white rounded-2xl p-4 flex items-center justify-between">
        <span class="text-sm text-slate-300">Total otomatis:</span>
        <span class="text-2xl font-black" x-text="'Rp ' + Math.max(0, qty*price-disc).toLocaleString('id-ID')"></span>
    </div>
    <div class="md:col-span-2 flex gap-2"><button class="px-6 py-3 rounded-2xl bg-rose-500 text-white font-bold">Simpan Order</button><a href="{{ route('admin.orders.index') }}" class="px-6 py-3 rounded-2xl bg-slate-100 font-bold">Batal</a></div>
</form>
@endsection
