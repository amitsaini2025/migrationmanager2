@props(['rows' => []])

<section class="my-day-card">
    <h3>Time by matter</h3>
    <p class="my-day-lead">Overlay minutes. Click a row to filter the board.</p>
    <div class="table-responsive">
        <table class="my-day-matter-table">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th class="text-end">Blocks</th>
                    <th class="text-end">Minutes</th>
                </tr>
            </thead>
            <tbody id="myDayMatterRows">
                @forelse($rows as $row)
                    <tr class="my-day-matter-row" data-filter-key="{{ $row['key'] ?? '' }}" data-matter-id="{{ $row['client_matter_id'] ?? '' }}">
                        <td class="my-day-refcell">{{ $row['matter_no'] ?? '—' }}</td>
                        <td class="text-end">{{ $row['blocks'] ?? 0 }}</td>
                        <td class="text-end">{{ $row['minutes'] ?? 0 }}m</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="my-day-empty">No confirmed overlay time yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
