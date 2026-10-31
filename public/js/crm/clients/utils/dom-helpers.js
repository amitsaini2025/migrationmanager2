/**
 * DOM/layout helper utilities for client detail pages.
 * Extracted from detail-main.js - Phase 2 refactoring.
 * Requires: jQuery
 */
(function($) {
    'use strict';
    if (!$) return;

    /**
     * Grow .client-sidebar-nav to fill space under the sticky header down to the
     * viewport bottom so blank space does not appear when the menu scrolls.
     * Does not shrink/clip .sidebar-header (Reference / Verification stay visible).
     */
    function adjustClientSidebarNavHeight() {
        var $sidebar = $('#client-sidebar');
        var $nav = $sidebar.children('.client-sidebar-nav').first();
        var $header = $sidebar.children('.sidebar-header').first();

        if (!$sidebar.length || !$nav.length || !$header.length) {
            return;
        }

        // Stacked/mobile layout: clear dynamic desktop sizing only
        if (window.matchMedia('(max-width: 992px)').matches) {
            $sidebar.css({ height: '', maxHeight: '', overflow: '' });
            $nav.css({ height: '', maxHeight: '', minHeight: '' });
            return;
        }

        // Let header keep its natural height; do not force a viewport max on the sidebar
        // that would clip Reference / Verification.
        $sidebar.css({
            height: 'auto',
            maxHeight: 'none',
            overflow: 'visible'
        });

        var headerBottom = $header[0].getBoundingClientRect().bottom;
        var bottomGap = 16;
        var navHeight = Math.floor(window.innerHeight - headerBottom - bottomGap);

        if (navHeight < 120) {
            navHeight = 120;
        }

        $nav.css({
            height: navHeight + 'px',
            maxHeight: navHeight + 'px',
            minHeight: 0,
            overflowX: 'hidden',
            overflowY: 'auto'
        });
    }

    var clientSidebarNavHeightBound = false;

    function bindClientSidebarNavHeightAdjust() {
        if (clientSidebarNavHeightBound || !$) {
            return;
        }
        clientSidebarNavHeightBound = true;

        var scheduled = null;
        function scheduleAdjust() {
            if (scheduled !== null) {
                return;
            }
            scheduled = window.requestAnimationFrame(function () {
                scheduled = null;
                adjustClientSidebarNavHeight();
            });
        }

        $(window).on('scroll.clientSidebarNav resize.clientSidebarNav', scheduleAdjust);

        var $header = $('#client-sidebar').children('.sidebar-header').first();
        if ($header.length && typeof ResizeObserver !== 'undefined') {
            var headerObserver = new ResizeObserver(scheduleAdjust);
            headerObserver.observe($header[0]);
        }

        adjustClientSidebarNavHeight();
        window.setTimeout(adjustClientSidebarNavHeight, 150);
        window.setTimeout(adjustClientSidebarNavHeight, 600);
    }

    /**
     * Adjust activity feed height based on viewport and content.
     */
    function adjustActivityFeedHeight() {
        if (!$('.activity-feed').length || !$('.main-content').length || !$('.crm-container').length) {
            bindClientSidebarNavHeightAdjust();
            return;
        }

        var windowHeight = $(window).height();
        var maxAvailableHeight = windowHeight - 120;

        $('.crm-container').css('align-items', 'flex-start');

        var mainVisible = $('.main-content').is(':visible');
        if (mainVisible) {
            $('.main-content').css('max-height', 'none');
            $('.main-content').css('overflow-y', 'visible');
            $('.main-content').css('height', 'auto');
        }

        var mainContentHeight = mainVisible ? $('.main-content').outerHeight() : 0;
        var activityFeedContentHeight = $('.activity-feed').prop('scrollHeight');
        var hasSubstantialContent = activityFeedContentHeight > 100;

        var targetHeight;
        if (!mainVisible) {
            targetHeight = maxAvailableHeight;
        } else if (hasSubstantialContent) {
            targetHeight = Math.max(mainContentHeight, maxAvailableHeight);
        } else {
            targetHeight = Math.min(mainContentHeight, maxAvailableHeight);
        }

        $('.activity-feed').css('max-height', targetHeight + 'px');
        $('.activity-feed').css('height', targetHeight + 'px');
        $('.activity-feed').css('overflow-y', 'auto');

        bindClientSidebarNavHeightAdjust();
        adjustClientSidebarNavHeight();
    }

    /**
     * Adjust file preview container heights based on viewport.
     */
    function adjustPreviewContainers() {
        $('.preview-pane.file-preview-container').each(function() {
            var windowHeight = $(window).height();
            var containerTop = $(this).offset().top;
            var desiredHeight = windowHeight - containerTop - 50;

            if (desiredHeight >= 600) {
                $(this).css('height', desiredHeight + 'px');
            } else {
                $(this).css('height', '600px');
            }
        });
    }

    /**
     * Trigger file download via temporary anchor element.
     * @param {string} url - Download URL
     * @param {string} fileName - Suggested filename
     */
    function downloadFile(url, fileName) {
        var link = document.createElement('a');
        link.href = url;
        link.download = fileName;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    window.adjustActivityFeedHeight = adjustActivityFeedHeight;
    window.adjustClientSidebarNavHeight = adjustClientSidebarNavHeight;
    window.adjustPreviewContainers = adjustPreviewContainers;
    window.downloadFile = downloadFile;

    $(function () {
        bindClientSidebarNavHeightAdjust();
    });

})(typeof jQuery !== 'undefined' ? jQuery : null);
