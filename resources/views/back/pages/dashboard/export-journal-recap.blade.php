<table>
    <thead>
        <tr>
            <th colspan="12" style="font-size: 14pt; font-weight: bold; text-align: center; vertical-align: middle;">
                REKAPITULASI ARTIKEL &amp; PEMBAYARAN JURNAL
            </th>
        </tr>
        <tr>
            <th colspan="12" style="font-size: 10pt; font-weight: bold; text-align: left;">
                Target / Cakupan: {{ $journal['name'] ?? ($journal['title'] ?? '-') }}
            </th>
        </tr>
        <tr>
            <th colspan="12" style="font-size: 10pt; text-align: left;">
                Filter Diterapkan:
                Tahun: {{ $summary['selected_year'] ?? 'Semua Tahun' }} |
                Bulan: {{ $summary['selected_month_name'] ?? 'Semua Bulan' }} |
                Edisi: {{ $journal['selected_issue']['label'] ?? 'Semua Edisi' }}
            </th>
        </tr>
        <tr>
            <th colspan="12" style="font-size: 9pt; color: #555555; text-align: left;">
                Waktu Unduh: {{ date('d-m-Y H:i:s') }}
            </th>
        </tr>
        <tr>
            <th colspan="12"></th>
        </tr>
        <tr>
            <th colspan="12"></th>
        </tr>
        <tr style="background-color: #E4E6EF; font-weight: bold; text-align: center;">
            <th style="width: 60px; text-align: center; font-weight: bold;">No</th>
            <th style="width: 250px; text-align: left; font-weight: bold;">
                @if($is_scope) Jurnal / Edisi @else Edisi / Issue @endif
            </th>
            <th style="width: 80px; text-align: center; font-weight: bold;">Tahun</th>
            <th style="width: 120px; text-align: right; font-weight: bold;">Author Fee (Rp)</th>
            <th style="width: 100px; text-align: center; font-weight: bold;">Total Artikel</th>
            <th style="width: 90px; text-align: center; font-weight: bold;">Publish</th>
            <th style="width: 100px; text-align: center; font-weight: bold;">Belum Publish</th>
            <th style="width: 80px; text-align: center; font-weight: bold;">Lunas</th>
            <th style="width: 90px; text-align: center; font-weight: bold;">Belum Lunas</th>
            <th style="width: 90px; text-align: center; font-weight: bold;">Belum Bayar</th>
            <th style="width: 80px; text-align: center; font-weight: bold;">Free</th>
            <th style="width: 140px; text-align: right; font-weight: bold;">Uang Masuk (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @if($is_scope)
            @forelse($journals_table as $jIndex => $item)
                {{-- Master Row (Jurnal) --}}
                <tr style="background-color: #E8FFF3; font-weight: bold;">
                    <td style="text-align: center; font-weight: bold;">{{ $jIndex + 1 }}</td>
                    <td style="font-weight: bold; text-align: left;">
                        {{ $item['journal_name'] }} ({{ $item['total_issues'] }} Edisi)
                    </td>
                    <td style="text-align: center; color: #888888;">-</td>
                    <td style="text-align: right; font-weight: bold;">{{ number_format($item['journal_author_fee'], 0, ',', '.') }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $item['total_articles'] }}</td>
                    <td style="text-align: center; font-weight: bold; color: #50CD89;">{{ $item['published_count'] }}</td>
                    <td style="text-align: center; font-weight: bold; color: #FFA800;">{{ $item['unpublished_count'] }}</td>
                    <td style="text-align: center; font-weight: bold; color: #50CD89;">{{ $item['lunas_count'] }}</td>
                    <td style="text-align: center; font-weight: bold; color: #FFA800;">{{ $item['belum_lunas_count'] }}</td>
                    <td style="text-align: center; font-weight: bold; color: #F1416C;">{{ $item['belum_bayar_count'] }}</td>
                    <td style="text-align: center; font-weight: bold; color: #009EF7;">{{ $item['free_count'] }}</td>
                    <td style="text-align: right; font-weight: bold; color: #50CD89;">{{ number_format($item['total_income'], 0, ',', '.') }}</td>
                </tr>

                {{-- Child Rows (Edisi per Jurnal) --}}
                @foreach($item['issues'] as $issIndex => $iss)
                    <tr>
                        <td style="text-align: center; color: #555555;">{{ $jIndex + 1 }}.{{ $issIndex + 1 }}</td>
                        <td style="text-align: left; padding-left: 15px;">
                            &nbsp;&nbsp;&nbsp;↳ {{ $iss['issue_label'] }}@if(!empty($iss['title']) && $iss['title'] !== '-') - {{ $iss['title'] }}@endif
                        </td>
                        <td style="text-align: center;">{{ $iss['year'] ?? '-' }}</td>
                        <td style="text-align: right;">{{ number_format($iss['author_fee'], 0, ',', '.') }}</td>
                        <td style="text-align: center;">{{ $iss['total_articles'] }}</td>
                        <td style="text-align: center;">{{ $iss['published_count'] }}</td>
                        <td style="text-align: center;">{{ $iss['unpublished_count'] }}</td>
                        <td style="text-align: center;">{{ $iss['lunas_count'] }}</td>
                        <td style="text-align: center;">{{ $iss['belum_lunas_count'] }}</td>
                        <td style="text-align: center;">{{ $iss['belum_bayar_count'] }}</td>
                        <td style="text-align: center;">{{ $iss['free_count'] }}</td>
                        <td style="text-align: right;">{{ number_format($iss['total_income'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="12" style="text-align: center; color: #888888;">Belum ada data jurnal atau edisi untuk cakupan ini.</td>
                </tr>
            @endforelse
        @else
            @forelse($issues_table as $index => $iss)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td style="text-align: left;">
                        {{ $iss['issue_label'] }}@if(!empty($iss['title']) && $iss['title'] !== '-') - {{ $iss['title'] }}@endif
                    </td>
                    <td style="text-align: center;">{{ $iss['year'] ?? '-' }}</td>
                    <td style="text-align: right;">{{ number_format($iss['author_fee'], 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ $iss['total_articles'] }}</td>
                    <td style="text-align: center;">{{ $iss['published_count'] }}</td>
                    <td style="text-align: center;">{{ $iss['unpublished_count'] }}</td>
                    <td style="text-align: center;">{{ $iss['lunas_count'] }}</td>
                    <td style="text-align: center;">{{ $iss['belum_lunas_count'] }}</td>
                    <td style="text-align: center;">{{ $iss['belum_bayar_count'] }}</td>
                    <td style="text-align: center;">{{ $iss['free_count'] }}</td>
                    <td style="text-align: right;">{{ number_format($iss['total_income'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" style="text-align: center; color: #888888;">Belum ada data edisi untuk jurnal ini.</td>
                </tr>
            @endforelse
        @endif
    </tbody>
    <tfoot>
        <tr style="background-color: #FFF8DD; font-weight: bold;">
            <td colspan="4" style="text-align: right; font-weight: bold;">TOTAL KESELURUHAN:</td>
            <td style="text-align: center; font-weight: bold;">{{ $summary['total_submissions'] ?? 0 }}</td>
            <td style="text-align: center; font-weight: bold; color: #50CD89;">{{ $summary['total_published'] ?? 0 }}</td>
            <td style="text-align: center; font-weight: bold; color: #FFA800;">{{ $summary['total_unpublished'] ?? 0 }}</td>
            <td style="text-align: center; font-weight: bold; color: #50CD89;">{{ $summary['lunas']['count'] ?? 0 }}</td>
            <td style="text-align: center; font-weight: bold; color: #FFA800;">{{ $summary['belum_lunas']['count'] ?? 0 }}</td>
            <td style="text-align: center; font-weight: bold; color: #F1416C;">{{ $summary['belum_bayar']['count'] ?? 0 }}</td>
            <td style="text-align: center; font-weight: bold; color: #009EF7;">{{ $summary['free']['count'] ?? 0 }}</td>
            <td style="text-align: right; font-weight: bold; color: #50CD89;">
                {{ number_format($summary['total_paid_received'] ?? 0, 0, ',', '.') }}
            </td>
        </tr>
    </tfoot>
</table>
