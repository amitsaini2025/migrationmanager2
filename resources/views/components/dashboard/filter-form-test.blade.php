@props(['filters', 'workflowStages'])

<div class="filter-controls filter-controls-enhanced">
    <form id="filterForm" method="GET" action="{{ route('dashboard.test') }}">
        <div class="search-box search-box-enhanced">
            <input type="text" 
                   name="client_name" 
                   id="clientSearchInput"
                   placeholder="Search Client Name or ID..." 
                   value="{{ $filters['client_name'] ?? '' }}"
                   autocomplete="off"
                   aria-label="Search clients">
            <i class="fas fa-search"></i>
            <div class="search-loading" id="searchLoading" style="display: none;">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
        </div>

        <select name="client_stage" class="stage-select stage-select-enhanced" aria-label="Filter by stage">
            <option value="">All Stages</option>
            @foreach($workflowStages as $stage)
                <option value="{{ $stage->id }}" 
                        {{ (isset($filters['client_stage']) && $filters['client_stage'] == $stage->id) ? 'selected' : '' }}>
                    {{ $stage->name }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="filter-button filter-button-enhanced">
            <i class="fas fa-filter"></i> Filter
        </button>

        @if(isset($filters['client_name']) || isset($filters['client_stage']))
            <a href="{{ route('dashboard.test') }}" class="clear-filters clear-filters-enhanced">
                <i class="fas fa-times-circle"></i> Clear
            </a>
        @endif
    </form>
</div>

<style>
.filter-controls-enhanced {
    background: white;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    margin-bottom: 20px;
}

.filter-controls-enhanced form {
    display: grid;
    grid-template-columns: 1fr auto auto auto;
    gap: 12px;
    align-items: center;
}

.search-box-enhanced {
    position: relative;
}

.search-box-enhanced input {
    width: 100%;
    padding: 10px 40px 10px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 0.95em;
    transition: all 0.3s ease;
}

.search-box-enhanced input:focus {
    border-color: #005792;
    box-shadow: 0 0 0 3px rgba(0, 87, 146, 0.1);
    outline: none;
}

.search-box-enhanced .fa-search {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
}

.search-loading {
    position: absolute;
    right: 40px;
    top: 50%;
    transform: translateY(-50%);
    color: #005792;
}

.stage-select-enhanced {
    padding: 10px 40px 10px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 0.95em;
    background-color: white;
    min-width: 200px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.stage-select-enhanced:focus {
    border-color: #005792;
    box-shadow: 0 0 0 3px rgba(0, 87, 146, 0.1);
    outline: none;
}

.filter-button-enhanced {
    padding: 10px 20px;
    background: linear-gradient(135deg, #005792 0%, #003d66 100%);
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 0.95em;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 87, 146, 0.3);
}

.filter-button-enhanced:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 87, 146, 0.4);
}

.filter-button-enhanced:active {
    transform: translateY(0);
}

.clear-filters-enhanced {
    padding: 10px 20px;
    background: white;
    color: #dc3545;
    border: 2px solid #dc3545;
    border-radius: 8px;
    cursor: pointer;
    font-size: 0.95em;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.clear-filters-enhanced:hover {
    background: #dc3545;
    color: white;
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .filter-controls-enhanced form {
        grid-template-columns: 1fr;
    }
    
    .stage-select-enhanced {
        width: 100%;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Debounced search
    let searchTimeout;
    const searchInput = document.getElementById('clientSearchInput');
    const searchLoading = document.getElementById('searchLoading');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchLoading.style.display = 'block';
            
            searchTimeout = setTimeout(() => {
                searchLoading.style.display = 'none';
                // Auto-submit could be enabled here if desired
                // document.getElementById('filterForm').submit();
            }, 800);
        });
    }
});
</script>

