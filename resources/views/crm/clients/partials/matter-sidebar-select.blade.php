{{-- Matter dropdown and status pill. Discontinued files stay in the list. --}}
<div class="sidebar-matter-selection">
    @if($fetchedData->type)
        @if(!empty($showMatterDropdown) && !empty($matter_list_arr))
            <select name="matter_id" id="sel_matter_id_client_detail" class="form-control mm-select visa-dropdown" data-valid="required">
                <option value="">Select Matters</option>
                @foreach($matter_list_arr as $matterlist)
                    @php
                        $matterName = 'General Matter';
                        if ($matterlist->sel_matter_id != 1 && !empty($matterlist->title)) {
                            $matterName = $matterlist->title;
                        }
                        $matterOptionLabel = $matterName.'('.($matterlist->client_unique_matter_no ?? '').')'
                            .\App\Support\ClientMatterSidebarStatus::optionSuffix((int) ($matterlist->matter_status ?? 1));
                    @endphp
                    <option value="{{ $matterlist->id }}" {{ $matterlist->id == ($latestClientMatterId ?? null) ? 'selected' : '' }} data-clientuniquematterno="{{ @$matterlist->client_unique_matter_no }}" data-sel-matter-id="{{ @$matterlist->sel_matter_id }}">{{ $matterOptionLabel }}</option>
                @endforeach
            </select>
        @endif
    @endif
</div>

<div class="matter-status-badge">{{ $matter_status_badge ?? 'N/A' }}</div>
