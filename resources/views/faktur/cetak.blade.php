@extends('templates.layout')

@section('title', 'Cetak Faktur')

@section('content')
    <div class="container-fluid p-4 bg-white">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0">Faktur / Nota</h4>
                <small>{{ (int) $header->jenis_transaksi === 1 ? 'Penjualan' : 'Inventaris' }}</small>
            </div>
            <div class="text-right">
                <div><strong>No. Transaksi:</strong> {{ $header->no_transaksi }}</div>
                <div><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($header->tgl)->format('d/m/Y') }}</div>
                <div><strong>Nama Kontak:</strong> {{ $namaKontak }}</div>
            </div>
        </div>

        <table class="table table-bordered table-sm">
            <thead class="thead-light">
                <tr>
                    <th style="width:40px;">#</th>
                    <th>Nama Barang</th>
                    <th style="width:90px;" class="text-right">Qty</th>
                    <th style="width:150px;" class="text-right">Harga Satuan</th>
                    <th style="width:150px;" class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $i => $it)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $it->nama_barang }}</td>
                        <td class="text-right">{{ number_format($it->qty, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($it->total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-right">TOTAL</th>
                    <th class="text-right">Rp {{ number_format($header->total, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>

        @if (empty($isPdf))
            <div class="text-right mt-3">
                <a href="{{ route('faktur.export.pdf', $header->no_transaksi) }}"
                    class="btn btn-outline-danger btn-sm">Export PDF</a>
                <button class="btn btn-primary btn-sm" onclick="window.print()">Cetak</button>
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        @media print {

            .main-footer,
            .main-header,
            .btn,
            nav,
            aside {
                display: none !important;
            }

            .content-wrapper,
            .content {
                margin: 0 !important;
                padding: 0 !important;
            }

            body {
                background: #fff !important;
            }
        }
    </style>
@endpush
