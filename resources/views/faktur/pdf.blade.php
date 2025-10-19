@php
    $fmt = fn($n) => 'Rp ' . number_format($n ?? 0, 0, ',', '.');
    $tgl = \Carbon\Carbon::parse($header->tgl)->format('d/m/Y');
    $w = (int) ($widthMm ?? 80);
    $innerWidth = $w - 6;
@endphp
@php
    $jenisLabel =
        [
            1 => 'Penjualan',
            2 => 'Inventaris',
            3 => 'Kas & Bank',
        ][$header->jenis_transaksi] ?? '-';
@endphp


<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Struk {{ $header->no_transaksi }}</title>
    <style>
        @page {
            margin: 6mm 3mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10.5px;
            color: #111;
            margin: 0;
            padding: 0;
        }

        .wrap {
            width: {{ $innerWidth }}mm;
            margin: 0 auto;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .muted {
            color: #555;
        }

        .header {
            margin-bottom: 6px;
        }

        .brand {
            font-weight: 700;
            font-size: 12px;
        }

        .addr {
            font-size: 10px;
            line-height: 1.2;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta td {
            padding: 2px 0;
            vertical-align: top;
        }

        .meta .k {
            width: 28mm;
            color: #333;
        }

        /* === Garis solid (bukan titik) === */
        .rule {
            border-top: 1px solid #000;
            margin: 6px 0;
        }

        /* === Items table === */
        .items th,
        .items td {
            padding: 4px 0;
        }

        .items thead th {
            border-bottom: 1px solid #000;
            /* garis bawah header */
            font-weight: 700;
        }

        .items tbody td {
            border-bottom: 1px solid #ddd;
            /* garis antar baris */
        }

        .items tfoot td {
            border-top: 1px solid #000;
            /* garis atas total */
            padding-top: 5px;
            font-weight: 700;
        }

        /* Lebar kolom fix agar header & body sejajar */
        .c-barang {
            text-align: left;
        }

        .c-qty {
            text-align: center;
        }

        .c-harga {
            text-align: right;
        }

        .c-sub {
            text-align: right;
        }

        /* (Opsional) kecilkan font untuk 58mm */
        @if (($widthMm ?? 80) == 58)
            body {
                font-size: 10px;
            }

            .brand {
                font-size: 11px;
            }
        @endif
    </style>
</head>

<body>
    <div class="wrap">

        {{-- Header toko --}}
        <div class="header center">
            @if (!empty($logoBase64))
                <img src="data:image/{{ $logoMime }};base64,{{ $logoBase64 }}" style="height:28px"><br>
            @endif
            <div class="brand">STIM AKUNTANSI</div>
        </div>

        <div class="rule"></div>

        {{-- Meta --}}
        <table class="meta">
            <tr>
                <td class="k">No. Transaksi</td>
                <td>: {{ $header->no_transaksi }}</td>
            </tr>
            <tr>
                <td class="k">Tanggal</td>
                <td>: {{ $tgl }}</td>
            </tr>
            <tr>
                <td class="k">Jenis</td>
                <td>: {{ $jenisLabel }}</td>

            </tr>
            <tr>
                <td class="k">Nama Kontak</td>
                <td>: {{ $namaKontak }}</td>
            </tr>
        </table>

        <div class="rule"></div>

        {{-- Items --}}
        <table class="items">
            {{-- kunci: colgroup memastikan header & body SELALU sejajar --}}
            <colgroup>
                <col style="width: auto;"> {{-- Barang: sisa ruang --}}
                <col style="width: 10mm;"> {{-- Qty --}}
                <col style="width: 18mm;"> {{-- Harga --}}
                <col style="width: 20mm;"> {{-- Subtotal --}}
            </colgroup>
            <thead>
                <tr>
                    <th class="c-barang">Barang</th>
                    <th class="c-qty">Qty</th>
                    <th class="c-harga">Harga</th>
                    <th class="c-sub">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $it)
                    <tr>
                        <td class="c-barang">{{ $it->nama_barang }}</td>
                        <td class="c-qty">{{ number_format($it->qty, 0, ',', '.') }}</td>
                        <td class="c-harga">{{ $fmt($it->harga_mentah ?? 0) }}</td>
                        <td class="c-sub">{{ $fmt($it->subtotal ?? 0) }}</td>

                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="c-harga">SUBTOTAL</td>
                    <td class="c-sub">{{ $fmt($header->subtotal ?? $items->sum('subtotal')) }}</td>
                </tr>

                @if (!empty($header->diskon_persen) && $header->diskon_persen > 0)
                    <tr>
                        <td colspan="3" class="c-harga">Diskon ({{ $header->diskon_persen }}%)</td>
                        <td class="c-sub">
                            -{{ $fmt((($header->subtotal ?? $items->sum('subtotal')) * $header->diskon_persen) / 100) }}
                        </td>
                    </tr>
                @endif

                @if (!empty($header->pajak_nominal) && $header->pajak_nominal > 0)
                    <tr>
                        <td colspan="3" class="c-harga">Pajak (11%)</td>
                        <td class="c-sub">{{ $fmt($header->pajak_nominal) }}</td>
                    </tr>
                @endif

                @if (!empty($header->biaya_lain) && $header->biaya_lain > 0)
                    <tr>
                        <td colspan="3" class="c-harga">Biaya Lain</td>
                        <td class="c-sub">{{ $fmt($header->biaya_lain) }}</td>
                    </tr>
                @endif

                <tr>
                    <td colspan="3" class="c-harga">TOTAL</td>
                    <td class="c-sub">
                        {{ $fmt(
                            ($header->subtotal ?? $items->sum('subtotal')) -
                                (($header->subtotal ?? $items->sum('subtotal')) * ($header->diskon_persen ?? 0)) / 100 +
                                ($header->pajak_nominal ?? 0) +
                                ($header->biaya_lain ?? 0),
                        ) }}
                    </td>
                </tr>
            </tfoot>


        </table>

        <div class="rule"></div>



    </div>
</body>

</html>
