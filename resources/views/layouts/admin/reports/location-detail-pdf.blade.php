<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Detail Lokasi — {{ $location->name }}</title>
    <style>
        /* ── Reset & base ───────────────────────────────────────────── */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 10px;
               color: #1f2937; line-height: 1.55; background: #fff; }

        /* ── Helpers ────────────────────────────────────────────────── */
        .w100 { width: 100%; }
        .tbl  { width: 100%; border-collapse: collapse; }
        .tc   { text-align: center; }
        .tr   { text-align: right; }
        .bold { font-weight: bold; }
        .muted { color: #6b7280; font-style: italic; }
        .pb4  { padding-bottom: 4px; }
        .mt8  { margin-top: 8px; }
        .mt12 { margin-top: 12px; }
        .mt16 { margin-top: 16px; }
        .p8   { padding: 8px; }
        .p10  { padding: 10px; }
        .no-break { page-break-inside: avoid; }

        /* ── Header bar ─────────────────────────────────────────────── */
        .hdr { background: #009966; color: #fff; padding: 14px 18px; }
        .hdr-brand { font-size: 17px; font-weight: bold; letter-spacing: .5px; }
        .hdr-sub   { font-size: 9.5px; opacity: .92; margin-top: 2px; }
        .hdr-meta  { font-size: 9px; opacity: .82; margin-top: 3px; }

        /* ── Location identity block ────────────────────────────────── */
        .loc-block { background: #f0fdf4; border-left: 4px solid #009966;
                     padding: 11px 15px; margin-top: 12px; }
        .loc-name  { font-size: 15px; font-weight: bold; color: #1f2937; margin-bottom: 5px; }

        /* ── Badges ─────────────────────────────────────────────────── */
        .badge     { display: inline; padding: 2px 8px; border-radius: 3px;
                     font-size: 8.5px; font-weight: bold; margin-right: 4px; }
        .b-green   { background: #d1fae5; color: #065f46; }
        .b-blue    { background: #dbeafe; color: #1e40af; }
        .b-gray    { background: #f3f4f6; color: #374151; }
        .b-orange  { background: #ffedd5; color: #9a3412; }
        .b-yellow  { background: #fef9c3; color: #713f12; }
        .b-red     { background: #fee2e2; color: #991b1b; }
        .b-high    { background: #fee2e2; color: #991b1b; }
        .b-medium  { background: #fef3c7; color: #92400e; }
        .b-low     { background: #dbeafe; color: #1e40af; }
        .b-resolved    { background: #d1fae5; color: #065f46; }
        .b-progress    { background: #fef3c7; color: #92400e; }
        .b-pending     { background: #f3f4f6; color: #374151; }

        /* ── Section block ──────────────────────────────────────────── */
        .sec         { margin-top: 14px; page-break-inside: avoid; }
        .sec-title   { background: #2d5c54; color: #fff; padding: 6px 12px;
                        font-size: 10.5px; font-weight: bold; letter-spacing: .3px; }
        .sec-body    { border: 1px solid #e5e7eb; border-top: none; }

        /* ── Info table (label | value) ─────────────────────────────── */
        .info-tbl td          { padding: 6px 10px; border-bottom: 1px solid #f3f4f6;
                                 font-size: 10px; vertical-align: top; }
        .info-tbl tr:last-child td { border-bottom: none; }
        .info-tbl .lbl        { font-weight: bold; color: #374151; width: 34%;
                                  background: #f9fafb; }
        .info-tbl tr:nth-child(even) .lbl { background: #f3f4f6; }
        .info-tbl tr:nth-child(even) td   { background: #fafafa; }

        /* ── Data table (multi-col) ─────────────────────────────────── */
        .dt th { background: #2d5c54; color: #fff; padding: 6px 8px;
                  font-size: 9.5px; text-align: left; border: 1px solid #1a4a42; }
        .dt td { padding: 5px 8px; border: 1px solid #e5e7eb; font-size: 9.5px;
                  vertical-align: top; }
        .dt tr:nth-child(even) td { background: #f9fafb; }

        /* ── Two-column species ─────────────────────────────────────── */
        .spc-tbl th { background: #2d5c54; color: #fff; padding: 6px 8px;
                       font-size: 9.5px; border: 1px solid #1a4a42; }
        .spc-tbl td { padding: 5px 8px; border: 1px solid #e5e7eb; font-size: 9.5px; }
        .spc-tbl tr:nth-child(even) td { background: #f9fafb; }
        .spc-div { border-left: 3px solid #009966 !important; }

        /* ── Damage table ───────────────────────────────────────────── */
        .dmg-tbl th { background: #7f1d1d; color: #fff; padding: 6px 8px;
                       font-size: 9.5px; text-align: left; border: 1px solid #991b1b; }
        .dmg-tbl td { padding: 5px 8px; border: 1px solid #fecaca; font-size: 9.5px;
                       vertical-align: top; }
        .dmg-tbl tr:nth-child(even) td { background: #fff7f7; }

        /* ── Action list inside damage cell ─────────────────────────── */
        .act-list { margin: 0; padding-left: 13px; }
        .act-list li { font-size: 9px; margin-bottom: 2px; color: #374151; }

        /* ── Statistics strip ───────────────────────────────────────── */
        .stat-strip td { padding: 8px 14px; text-align: center; border: 1px solid #e5e7eb;
                          background: #f9fafb; }
        .stat-val  { font-size: 16px; font-weight: bold; color: #009966; display: block; }
        .stat-lbl  { font-size: 8.5px; color: #6b7280; margin-top: 1px; display: block; }

        /* ── Footer ─────────────────────────────────────────────────── */
        .ftr { margin-top: 18px; padding-top: 9px; border-top: 2px solid #009966; }
        .ftr td { font-size: 8px; color: #6b7280; vertical-align: middle; }

        /* ── Page break ─────────────────────────────────────────────── */
        .pgbrk { page-break-after: always; }
    </style>
</head>
<body>

{{-- ══════════════════════════════════════════
     HEADER
══════════════════════════════════════════ --}}
<div class="hdr">
    <table class="w100">
        <tr>
            <td>
                <div class="hdr-brand">SIKOMANG</div>
                <div class="hdr-sub">Sistem Informasi dan Komunikasi Mangrove DKI Jakarta</div>
                <div class="hdr-sub">Dinas Lingkungan Hidup Provinsi DKI Jakarta</div>
            </td>
            <td style="text-align:right;vertical-align:top;">
                <div style="font-size:11px;font-weight:bold;">LAPORAN DETAIL LOKASI</div>
                <div class="hdr-meta">Tanggal: {{ $generated_at }}</div>
                <div class="hdr-meta">Digenerate oleh: {{ $generated_by }}</div>
            </td>
        </tr>
    </table>
</div>

{{-- ══════════════════════════════════════════
     LOCATION IDENTITY
══════════════════════════════════════════ --}}
<div class="loc-block">
    <div class="loc-name">{{ $location->name }}</div>
    <div>
        @php
            $typeLabels = [
                'dilindungi'              => 'Dilindungi',
                'pengkayaan'              => 'Pengkayaan',
                'rehabilitasi'            => 'Rehabilitasi',
                'pengkayaan_rehabilitasi' => 'Pengkayaan / Rehabilitasi',
            ];
            $typeKey = strtolower($location->type);
            $typeLbl = $typeLabels[$typeKey] ?? ucfirst($location->type);
        @endphp
        <span class="badge b-blue">{{ $typeLbl }}</span>
        <span class="badge b-green">{{ ucfirst($location->density) }}</span>
        <span class="badge {{ $location->is_active ? 'b-green' : 'b-gray' }}">
            {{ $location->is_active ? 'Aktif' : 'Tidak Aktif' }}
        </span>
        @if($location->region)
            <span class="badge b-gray">{{ $location->region }}</span>
        @endif
    </div>
</div>

{{-- ══════════════════════════════════════════
     STATISTICS STRIP
══════════════════════════════════════════ --}}
<table class="w100 stat-strip mt8">
    <tr>
        <td style="width:25%">
            <span class="stat-val">{{ $location->area ? number_format($location->area, 2) . ' ha' : '—' }}</span>
            <span class="stat-lbl">Luas Area</span>
        </td>
        <td style="width:25%">
            <span class="stat-val">{{ $location->health_percentage ? $location->health_percentage . '%' : '—' }}</span>
            <span class="stat-lbl">Tingkat Kesehatan</span>
        </td>
        <td style="width:25%">
            @php
                $d   = $location->details;
                $nVeg = ($d && is_array($d->vegetasi)) ? count(array_filter($d->vegetasi)) : 0;
                $nFau = ($d && is_array($d->fauna))    ? count(array_filter($d->fauna))    : 0;
            @endphp
            <span class="stat-val">{{ $nVeg + $nFau }}</span>
            <span class="stat-lbl">Total Spesies</span>
        </td>
        <td style="width:25%">
            <span class="stat-val">{{ $location->damages->count() }}</span>
            <span class="stat-lbl">Laporan Kerusakan</span>
        </td>
    </tr>
</table>

{{-- ══════════════════════════════════════════
     A. INFORMASI DASAR
══════════════════════════════════════════ --}}
<div class="sec">
    <div class="sec-title">A.&nbsp; INFORMASI DASAR</div>
    <div class="sec-body">
        <table class="tbl info-tbl">
            <tr><td class="lbl">Nama Lokasi</td>     <td>{{ $location->name }}</td></tr>
            <tr><td class="lbl">Koordinat</td>         <td>{{ $location->latitude }}, {{ $location->longitude }}</td></tr>
            <tr><td class="lbl">Luas Area</td>         <td>{{ $location->area ? $location->area . ' Hektar (ha)' : 'Belum diidentifikasi' }}</td></tr>
            <tr><td class="lbl">Kerapatan Mangrove</td><td>{{ ucfirst($location->density) }}</td></tr>
            <tr><td class="lbl">Tipe Kawasan</td>      <td>{{ $typeLbl }}</td></tr>
            <tr>
                <td class="lbl">Tingkat Kesehatan</td>
                <td>
                    {{ $location->health_percentage ? $location->health_percentage . '%' : '—' }}
                    @if($location->health_score)
                        &nbsp;<span class="muted">({{ $location->health_score }})</span>
                    @endif
                </td>
            </tr>
            <tr><td class="lbl">Pengelola</td>         <td>{{ $location->manager          ?? '—' }}</td></tr>
            <tr><td class="lbl">Tahun Penetapan</td>   <td>{{ $location->year_established ?? '—' }}</td></tr>
            <tr><td class="lbl">Wilayah / Kecamatan</td><td>{{ $location->region          ?? '—' }}</td></tr>
            <tr><td class="lbl">Alamat</td>            <td>{{ $location->location_address  ?? '—' }}</td></tr>
            <tr><td class="lbl">Serapan Karbon</td>    <td>{{ $location->carbon_data       ?? 'Data tidak tersedia' }}</td></tr>
            <tr><td class="lbl">Spesies Utama</td>     <td>{{ $location->species           ?? 'Belum diidentifikasi' }}</td></tr>
        </table>
    </div>
</div>

@if($location->description)
{{-- ══════════════════════════════════════════
     B. DESKRIPSI
══════════════════════════════════════════ --}}
<div class="sec">
    <div class="sec-title">B.&nbsp; DESKRIPSI LOKASI</div>
    <div class="sec-body p10" style="font-size:10px;line-height:1.7;color:#374151;">
        {{ $location->description }}
    </div>
</div>
@endif

{{-- ══════════════════════════════════════════
     C. KEANEKARAGAMAN HAYATI
══════════════════════════════════════════ --}}
@php
    $det  = $location->details;
    $veg  = array_values(array_filter(($det && is_array($det->vegetasi)) ? $det->vegetasi : []));
    $fau  = array_values(array_filter(($det && is_array($det->fauna))    ? $det->fauna    : []));
    $maxSp = max(count($veg), count($fau));
@endphp

@if($maxSp > 0)
<div class="sec no-break">
    <div class="sec-title">C.&nbsp; KEANEKARAGAMAN HAYATI</div>
    <div class="sec-body">
        <table class="tbl spc-tbl">
            <tr>
                <th style="width:5%;text-align:center">No</th>
                <th style="width:44%">Vegetasi / Flora &nbsp;<span style="font-weight:normal;font-size:9px;">({{ count($veg) }} spesies)</span></th>
                <th class="spc-div" style="width:5%;text-align:center">No</th>
                <th style="width:46%">Fauna &nbsp;<span style="font-weight:normal;font-size:9px;">({{ count($fau) }} spesies)</span></th>
            </tr>
            @for($i = 0; $i < $maxSp; $i++)
            <tr>
                <td style="text-align:center">{{ isset($veg[$i]) ? $i+1 : '' }}</td>
                <td>{{ $veg[$i] ?? '' }}</td>
                <td class="spc-div" style="text-align:center">{{ isset($fau[$i]) ? $i+1 : '' }}</td>
                <td>{{ $fau[$i] ?? '' }}</td>
            </tr>
            @endfor
        </table>
    </div>
</div>
@endif

{{-- ══════════════════════════════════════════
     D. AKTIVITAS
══════════════════════════════════════════ --}}
@php
    $acts     = ($det && is_array($det->activities)) ? $det->activities : [];
    $actDesc  = $acts['description'] ?? null;
    $actItems = array_values(array_filter(
        (array)($acts['items'] ?? (isset($acts[0]) ? $acts : [])),
        fn($i) => is_string($i) && trim($i) !== ''
    ));
@endphp

@if($actDesc || count($actItems) > 0)
<div class="sec no-break">
    <div class="sec-title">D.&nbsp; AKTIVITAS SEKITAR</div>
    <div class="sec-body p10">
        @if($actDesc)
            <p style="margin-bottom:7px;color:#4b5563;">{{ $actDesc }}</p>
        @endif
        @if(count($actItems) > 0)
            <table class="tbl dt">
                @foreach($actItems as $idx => $item)
                <tr>
                    <td style="width:30px;text-align:center">{{ $idx + 1 }}</td>
                    <td>{{ $item }}</td>
                </tr>
                @endforeach
            </table>
        @endif
    </div>
</div>
@endif

{{-- ══════════════════════════════════════════
     E. PROGRAM & PEMANFAATAN
══════════════════════════════════════════ --}}
@php
    $util = array_values(array_filter(($det && is_array($det->forest_utilization)) ? $det->forest_utilization : []));
    $prog = array_values(array_filter(($det && is_array($det->programs))           ? $det->programs           : []));
    $stkh = array_values(array_filter(($det && is_array($det->stakeholders))       ? $det->stakeholders       : []));
    $hasProg = count($util) > 0 || count($prog) > 0 || count($stkh) > 0;
    $maxProg = max(count($util), count($prog), count($stkh), 0);
@endphp

@if($hasProg)
<div class="sec no-break">
    <div class="sec-title">E.&nbsp; PROGRAM &amp; PEMANFAATAN</div>
    <div class="sec-body">
        <table class="tbl">
            <tr>
                <th style="background:#2d5c54;color:#fff;padding:6px 8px;font-size:9.5px;border:1px solid #1a4a42;width:33%">
                    Pemanfaatan Hutan <span style="font-weight:normal;font-size:8.5px;">({{ count($util) }})</span>
                </th>
                <th style="background:#2d5c54;color:#fff;padding:6px 8px;font-size:9.5px;border:1px solid #1a4a42;width:34%">
                    Program Dilaksanakan <span style="font-weight:normal;font-size:8.5px;">({{ count($prog) }})</span>
                </th>
                <th style="background:#2d5c54;color:#fff;padding:6px 8px;font-size:9.5px;border:1px solid #1a4a42;width:33%">
                    Pihak Terkait <span style="font-weight:normal;font-size:8.5px;">({{ count($stkh) }})</span>
                </th>
            </tr>
            @for($i = 0; $i < $maxProg; $i++)
            <tr style="{{ $i % 2 === 0 ? 'background:#f9fafb' : '' }}">
                <td style="padding:5px 8px;border:1px solid #e5e7eb;font-size:9.5px;">{{ $util[$i] ?? '' }}</td>
                <td style="padding:5px 8px;border:1px solid #e5e7eb;font-size:9.5px;">{{ $prog[$i] ?? '' }}</td>
                <td style="padding:5px 8px;border:1px solid #e5e7eb;font-size:9.5px;">{{ $stkh[$i] ?? '' }}</td>
            </tr>
            @endfor
        </table>
    </div>
</div>
@endif

{{-- ══════════════════════════════════════════
     F. LAPORAN KERUSAKAN
══════════════════════════════════════════ --}}
@if($location->damages->count() > 0)
<div class="sec">
    <div class="sec-title" style="background:#7f1d1d;">
        F.&nbsp; LAPORAN KERUSAKAN &nbsp;<span style="font-weight:normal;font-size:9px;">({{ $location->damages->count() }} laporan)</span>
    </div>
    <div class="sec-body">
        <table class="tbl dmg-tbl">
            <tr>
                <th style="width:26px">No</th>
                <th style="width:22%">Judul Kerusakan</th>
                <th style="width:28%">Deskripsi</th>
                <th style="width:9%">Prioritas</th>
                <th style="width:11%">Status</th>
                <th>Aksi Penanganan</th>
            </tr>
            @foreach($location->damages as $i => $dmg)
            @php
                $pBadge = ['high' => 'b-high', 'medium' => 'b-medium', 'low' => 'b-low'][$dmg->priority] ?? 'b-gray';
                $sBadge = ['resolved' => 'b-resolved', 'in_progress' => 'b-progress', 'pending' => 'b-pending'][$dmg->status] ?? 'b-gray';
            @endphp
            <tr>
                <td style="text-align:center">{{ $i + 1 }}</td>
                <td class="bold">{{ $dmg->title }}</td>
                <td>{{ $dmg->description }}</td>
                <td style="text-align:center"><span class="badge {{ $pBadge }}">{{ ucfirst($dmg->priority) }}</span></td>
                <td style="text-align:center"><span class="badge {{ $sBadge }}">{{ ucfirst(str_replace('_', ' ', $dmg->status)) }}</span></td>
                <td>
                    @if($dmg->actions->count() > 0)
                        <ul class="act-list">
                            @foreach($dmg->actions as $act)
                                <li>{{ $act->action_description }}</li>
                            @endforeach
                        </ul>
                    @else
                        <span class="muted">Belum ada aksi</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@else
<div class="sec no-break">
    <div class="sec-title" style="background:#7f1d1d;">F.&nbsp; LAPORAN KERUSAKAN</div>
    <div class="sec-body p10 muted tc">Tidak ada laporan kerusakan tercatat.</div>
</div>
@endif

{{-- ══════════════════════════════════════════
     FOOTER
══════════════════════════════════════════ --}}
<div class="ftr">
    <table class="w100">
        <tr>
            <td>
                Digenerate oleh: <strong>{{ $generated_by }}</strong> &nbsp;|&nbsp;
                Pada: <strong>{{ $generated_at }}</strong> WIB
            </td>
            <td style="text-align:right;">
                &copy; {{ date('Y') }} Dinas Lingkungan Hidup Provinsi DKI Jakarta &nbsp;&mdash;&nbsp;
                SIKOMANG
            </td>
        </tr>
    </table>
</div>

</body>
</html>
