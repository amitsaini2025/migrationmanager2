<td onclick="event.stopPropagation();" class="checklist-status-cell">
    @if(!empty($row->matter_internal_id) && (int) ($row->matter_status ?? 1) === 1)
        @php
            $currentStatus = $row->tr_checklist_status ?? 'active';
            $statusLabels = ['active' => 'Active', 'convert_to_client' => 'Convert to client', 'discontinue' => 'Discontinue', 'hold' => 'Hold'];
        @endphp
        <select class="form-control form-control-sm checklist-status-select" data-matter-id="{{ $matterId }}" data-visa-type="{{ $visaType }}" title="Status">
            @foreach($statusLabels as $val => $label)
                <option value="{{ $val }}" {{ $currentStatus === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    @elseif($isLead)
        <span class="badge badge-info" title="{{ __('Lead row without a client matter; status is fixed until a matter exists.') }}">Lead</span>
    @elseif(!empty($row->matter_internal_id) && (int) ($row->matter_status ?? 1) === 0)
        <span class="badge badge-secondary">Discontinued</span>
    @else
        <span class="text-muted">—</span>
    @endif
</td>
