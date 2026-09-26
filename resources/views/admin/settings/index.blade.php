@extends('admin.layout')
@section('title', 'Tampilan & SEO')
@section('header', 'Pengaturan Dinamis')
@section('subheader', 'Ubah logo, background, icon, warna, SEO & script — langsung tampil di semua halaman')

@section('content')
<div class="flex flex-wrap gap-2 mb-4">
    @foreach($allowed as $g)
        <a href="{{ route('admin.settings.index', $g) }}" class="px-4 py-2 rounded-2xl text-sm font-bold {{ $group === $g ? 'bg-slate-900 text-white' : 'bg-white border hover:bg-slate-50' }}">{{ ucfirst($g) }}</a>
    @endforeach
</div>
<form method="POST" action="{{ route('admin.settings.update', $group) }}" enctype="multipart/form-data" class="bg-white rounded-3xl border p-6 space-y-5">
    @csrf @method('PUT')
    @foreach($settings as $s)
        <div class="grid md:grid-cols-3 gap-3 items-start border-b last:border-0 pb-5">
            <div><div class="font-bold text-sm">{{ $s->label }}</div><div class="text-xs text-slate-400 font-mono">{{ $s->key }} • {{ $s->type }}</div></div>
            <div class="md:col-span-2">
                @if($s->type === 'image')
                    <div class="flex items-center gap-4">
                        @if($s->value)<img src="{{ pv_asset($s->value) }}" class="h-16 rounded-2xl border bg-slate-50 object-contain" alt="">@endif
                        <div class="flex-1">
                            <input type="file" name="file_{{ $s->key }}" accept="image/*" class="w-full text-sm">
                            <input name="url_{{ $s->key }}" value="" placeholder="atau tempel URL gambar / kosongkan" class="mt-2 w-full px-4 py-2 rounded-2xl border text-sm">
                            <div class="text-[11px] text-slate-400 mt-1 truncate">Saat ini: {{ $s->value }}</div>
                        </div>
                    </div>
                @elseif($s->type === 'textarea')
                    <textarea name="{{ $s->key }}" rows="3" class="w-full px-4 py-2.5 rounded-2xl border font-mono text-sm">{{ old($s->key, $s->value) }}</textarea>
                @elseif($s->type === 'color')
                    <div class="flex items-center gap-3"><input type="color" name="{{ $s->key }}" value="{{ old($s->key, $s->value) }}" class="w-12 h-10 rounded-xl border"><input value="{{ old($s->key, $s->value) }}" oninput="this.previousElementSibling.value=this.value" class="px-4 py-2 rounded-2xl border font-mono text-sm w-48"></div>
                @else
                    <input name="{{ $s->key }}" value="{{ old($s->key, $s->value) }}" class="w-full px-4 py-2.5 rounded-2xl border">
                @endif
            </div>
        </div>
    @endforeach
    <button class="px-6 py-3 rounded-2xl bg-rose-500 text-white font-bold hover:bg-rose-600">Simpan Pengaturan {{ ucfirst($group) }}</button>
</form>
@endsection
