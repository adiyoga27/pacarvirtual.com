@extends('admin.layout')
@section('title', $order->exists ? 'Edit Order' : 'Order Baru')
@section('header', $order->exists ? 'Edit Order '.$order->displayNo() : 'Order Baru')
@section('subheader', 'Jasa bisa lebih dari 1 — nomor invoice terisi otomatis saat disimpan')

@push('head')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
    .select2-container--default .select2-selection--single { height: 46px; border-radius: 1rem; border: 1px solid #e2e8f0; display: flex; align-items: center; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { padding-left: 1rem; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 44px; }
    .step-num { width: 1.75rem; height: 1.75rem; border-radius: .75rem; display: inline-flex; align-items: center; justify-content: center; font-weight: 900; font-size: .8rem; }
</style>
@endpush

@section('content')
<form method="POST" action="{{ $order->exists ? route('admin.orders.update', $order) : route('admin.orders.store') }}" class="max-w-4xl space-y-4" x-data="orderForm()">
    @csrf @if($order->exists) @method('PUT') @endif
    <input type="hidden" name="status" value="paid">

    {{-- Langkah 1 --}}
    <div class="bg-white rounded-3xl border p-6">
        <h3 class="font-bold mb-4"><span class="step-num bg-slate-900 text-white mr-2">1</span> Pencatatan</h3>
        <div class="grid md:grid-cols-2 gap-4">
            <div><label class="text-xs font-bold text-slate-500">ADMIN PENCATAT</label>
                <select name="admin_id" class="select2 w-full" data-placeholder="Pilih admin">
                    @foreach($admins as $a)<option value="{{ $a->id }}" {{ (string) old('admin_id', $order->created_by ?? auth()->id()) === (string) $a->id ? 'selected' : '' }}>{{ $a->name }} ({{ '@' . ($a->username ?? $a->email) }})</option>@endforeach
                </select></div>
            <div><label class="text-xs font-bold text-slate-500">TANGGAL ORDERAN</label><input type="date" name="order_date" value="{{ old('order_date', ($order->order_date ?? now())->format('Y-m-d')) }}" required class="mt-1 w-full px-4 py-2.5 rounded-2xl border"></div>
        </div>
        @if($order->exists)
        <p class="text-xs text-slate-400 mt-3">No. Invoice: <b class="font-mono text-slate-600">{{ $order->displayNo() }}</b> (dibuat otomatis, tidak bisa diubah)</p>
        @else
        <p class="text-xs text-slate-400 mt-3">No. Invoice: <b>otomatis terisi saat disimpan</b> (mis. INV/2026/09/0001)</p>
        @endif
    </div>

    {{-- Langkah 2 --}}
    <div class="bg-white rounded-3xl border p-6">
        <h3 class="font-bold mb-4"><span class="step-num bg-indigo-600 text-white mr-2">2</span> Jasa & Talent</h3>

        <div class="space-y-3" id="itemsWrap">
            <template x-for="(row, i) in items" :key="i">
                <div class="rounded-2xl border p-4 bg-slate-50/60">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-black text-slate-500" x-text="'JASA ' + (i + 1)"></span>
                        <button type="button" x-show="items.length > 1" @click="items.splice(i, 1)" class="text-[11px] font-bold px-2.5 py-1.5 rounded-xl bg-rose-100 text-rose-700 hover:bg-rose-200"><i class="bi bi-trash"></i> Hapus</button>
                    </div>
                    <div class="grid md:grid-cols-2 gap-3">
                        <div><label class="text-xs font-bold text-slate-500">PILIH JASA</label>
                            <select :name="'items[' + i + '][service_id]'" x-model="row.service_id" @change="onService(i)" required class="mt-1 w-full px-4 py-2.5 rounded-2xl border bg-white">
                                <option value="">— pilih jasa —</option>
                                @foreach($services as $s)<option value="{{ $s->id }}">{{ $s->name }} — {{ rupiah($s->default_price) }} • komisi {{ $s->komisiLabel() }}</option>@endforeach
                            </select></div>
                        <div><label class="text-xs font-bold text-slate-500">TALENT JASA INI</label>
                            <select :name="'items[' + i + '][talent_id]'" x-model="row.talent_id" class="mt-1 w-full px-4 py-2.5 rounded-2xl border bg-white">
                                <option value="">— tanpa talent —</option>
                                @foreach($talents as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach
                            </select></div>
                        <div><label class="text-xs font-bold text-slate-500">QTY</label>
                            <input type="number" :name="'items[' + i + '][quantity]'" x-model.number="row.quantity" min="1" max="100" required class="mt-1 w-full px-4 py-2.5 rounded-2xl border bg-white"></div>
                        <div><label class="text-xs font-bold text-slate-500">HARGA SATUAN</label>
                            <input type="number" :name="'items[' + i + '][price]'" x-model.number="row.price" min="0" step="500" required class="mt-1 w-full px-4 py-2.5 rounded-2xl border bg-white"></div>
                        <div><label class="text-xs font-bold text-slate-500">DISKON (Rp)</label>
                            <input type="number" :name="'items[' + i + '][discount]'" x-model.number="row.discount" min="0" step="500" class="mt-1 w-full px-4 py-2.5 rounded-2xl border bg-white"></div>
                        <div><label class="text-xs font-bold text-slate-500">KOMISI (Rp)</label>
                            <input type="number" :name="'items[' + i + '][commission]'" x-model.number="row.commission" min="0" step="500" class="mt-1 w-full px-4 py-2.5 rounded-2xl border bg-white"></div>
                        <div class="md:col-span-2"><label class="text-xs font-bold text-slate-500">TANGGAL KOMISI JASA INI <span class="font-normal text-slate-400">(opsional, bisa beda tiap jasa)</span></label>
                            <input type="date" :name="'items[' + i + '][commission_date]'" x-model="row.commission_date" class="mt-1 w-full px-4 py-2.5 rounded-2xl border bg-white"></div>
                    </div>
                    <div class="text-right text-xs text-slate-500 mt-2">Subtotal: <b class="text-slate-800" x-text="rupiah(lineTotal(row))"></b></div>
                </div>
            </template>
        </div>
        <button type="button" @click="addRow()" class="mt-3 w-full py-3 rounded-2xl border-2 border-dashed border-indigo-300 text-indigo-700 text-sm font-bold hover:border-indigo-600 hover:bg-indigo-50/50"><i class="bi bi-plus-circle"></i> Tambah Jasa Lagi</button>

        <div class="grid md:grid-cols-2 gap-4 mt-4">
            <div><label class="text-xs font-bold text-slate-500">METODE PEMBAYARAN</label>
                <select name="payment_method" class="select2 w-full" data-placeholder="Pilih metode...">
                    @foreach($paymentMethods as $pm)<option {{ old('payment_method', $order->payment_method) === $pm ? 'selected' : '' }}>{{ $pm }}</option>@endforeach
                </select></div>
            <div><label class="text-xs font-bold text-slate-500">CLIENT (nama pelanggan)</label><input name="customer_name" value="{{ old('customer_name', $order->customer_name) }}" required placeholder="cth: Salsa" class="mt-1 w-full px-4 py-2.5 rounded-2xl border"></div>
            <div><label class="text-xs font-bold text-slate-500">NO. WHATSAPP CLIENT</label><input name="customer_whatsapp" value="{{ old('customer_whatsapp', $order->customer_whatsapp) }}" placeholder="628..." class="mt-1 w-full px-4 py-2.5 rounded-2xl border"></div>
        </div>
    </div>

    {{-- Langkah 3: ringkasan otomatis --}}
    <div class="bg-slate-900 text-white rounded-3xl p-6">
        <h3 class="font-bold mb-3"><span class="step-num bg-emerald-500 text-white mr-2">3</span> Ringkasan Otomatis</h3>
        <div class="space-y-1.5 text-sm">
            <div class="flex items-center justify-between"><span class="text-slate-300">Harga (<span x-text="items.length"></span> jasa)</span><b x-text="rupiah(grandTotal())"></b></div>
            <div class="flex items-center justify-between"><span class="text-slate-300">Total komisi talent</span><b class="text-indigo-300" x-text="rupiah(totalKomisi())"></b></div>
            <div class="flex items-center justify-between border-t border-white/10 pt-2"><span class="font-bold">Untung = Dana Cair</span><b class="text-emerald-400 text-xl" x-text="rupiah(untung())"></b></div>
        </div>
    </div>

    <div><label class="text-xs font-bold text-slate-500">CATATAN (opsional)</label><textarea name="notes" rows="2" class="mt-1 w-full px-4 py-2.5 rounded-2xl border bg-white" placeholder="cth: order via WA admin 1">{{ old('notes', $order->notes) }}</textarea></div>

    <div class="flex gap-2"><button class="flex-1 py-3.5 rounded-2xl bg-rose-500 text-white font-bold hover:bg-rose-600">Simpan Order (invoice otomatis)</button><a href="{{ route('admin.orders.index') }}" class="px-6 py-3.5 rounded-2xl bg-white border font-bold">Batal</a></div>
</form>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
var SERVICE_CATALOG = @json($servicesJson);
function rupiah(n) { return 'Rp ' + Number(n || 0).toLocaleString('id-ID'); }
function orderForm() {
    return {
        items: @json(old('items', $itemsDefault)),
        addRow() { this.items.push({ service_id: '', talent_id: '', quantity: 1, price: 0, discount: 0, commission: 0, commission_date: null }); },
        onService(i) {
            var row = this.items[i];
            var item = SERVICE_CATALOG[String(row.service_id)];
            if (!item) return;
            row.price = item.price;
            var perUnit = item.komisi_tipe === 'persen' ? Math.max(0, item.price * item.komisi / 100) : item.komisi;
            row.commission = Math.round(perUnit * (Number(row.quantity) || 1));
        },
        lineTotal(row) { return Math.max(0, (Number(row.quantity) || 0) * (Number(row.price) || 0) - (Number(row.discount) || 0)); },
        grandTotal() { return this.items.reduce((s, r) => s + this.lineTotal(r), 0); },
        totalKomisi() { return this.items.reduce((s, r) => s + Math.max(0, Number(r.commission) || 0), 0); },
        untung() { return Math.max(0, this.grandTotal() - this.totalKomisi()); }
    };
}
$(function () { $('.select2').select2({ width: '100%' }); });
</script>
@endpush
