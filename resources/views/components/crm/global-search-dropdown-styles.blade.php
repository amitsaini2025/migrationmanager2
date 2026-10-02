        /* Global search dropdown results (topbar + modal client search) */
        .topbar-search .ts-dropdown .mm-result-repository,
        .custom_modal .ts-dropdown .mm-result-repository {
            display: block !important;
            width: 100% !important;
            padding: 2px 0 !important;
        }
        .topbar-search .ts-dropdown .mm-result-repository .ag-flex-column,
        .custom_modal .ts-dropdown .mm-result-repository .ag-flex-column {
            display: flex !important;
            flex-direction: column !important;
            min-width: 0 !important;
        }
        .topbar-search .ts-dropdown .mm-result-repository-stats,
        .custom_modal .ts-dropdown .mm-result-repository-stats {
            flex-shrink: 0 !important;
        }
        .topbar-search .ts-dropdown .mm-result-repository__title,
        .custom_modal .ts-dropdown .mm-result-repository__title {
            font-weight: 600 !important;
            color: #343a40 !important;
        }
        .topbar-search .ts-dropdown .mm-result-repository__description,
        .custom_modal .ts-dropdown .mm-result-repository__description {
            color: #6c757d !important;
            font-size: 12px !important;
        }
        .topbar-search .ts-dropdown .mm-result-repository-stats,
        .custom_modal .ts-dropdown .mm-result-repository-stats {
            display: block !important;
            margin-top: 4px !important;
        }
        .topbar-search .ts-dropdown .ui.label.mm-result-repository__statistics,
        .custom_modal .ts-dropdown .ui.label.mm-result-repository__statistics {
            display: inline-block !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            line-height: 1.2 !important;
            padding: 4px 10px !important;
            border-radius: 4px !important;
            text-transform: lowercase !important;
            margin: 0 !important;
            border: none !important;
        }
        .topbar-search .ts-dropdown .ui.label.yellow.mm-result-repository__statistics,
        .custom_modal .ts-dropdown .ui.label.yellow.mm-result-repository__statistics {
            background-color: #6777ef !important;
            color: #fff !important;
        }
        .topbar-search .ts-dropdown .ui.label.mm-result-repository__statistics:not(.yellow),
        .custom_modal .ts-dropdown .ui.label.mm-result-repository__statistics:not(.yellow) {
            background-color: #e9ecef !important;
            color: #495057 !important;
        }
        .topbar-search .ts-dropdown .mm-result-repository-stats:empty,
        .custom_modal .ts-dropdown .mm-result-repository-stats:empty {
            display: none !important;
            margin-top: 0 !important;
        }
