    // Global function to load activities feed (paginated via /get-activities)
    window.ActivityFeedState = window.ActivityFeedState || { page: 1, hasMore: false, loading: false, pendingReset: false, fetched: false };

    window.loadActivities = function(options) {
        var opts = options || {};
        var reset = opts.reset !== false;
        var append = opts.append === true;

        if (window.ActivityFeedState.loading) {
            if (reset && !append) {
                window.ActivityFeedState.pendingReset = true;
            }
            return;
        }

        if (reset) {
            window.ActivityFeedState.pendingReset = false;
            window.ActivityFeedState.page = 1;
            window.ActivityFeedState.hasMore = false;
            $('.activity-feed').scrollTop(0);
        } else if (append) {
            window.ActivityFeedState.page = (window.ActivityFeedState.page || 1) + 1;
        }

        window.ActivityFeedState.loading = true;
        $('#activity-feed-loading').show();
        $('#activity-feed-load-more').prop('disabled', true);

        var requestData = {
            id: window.ClientDetailConfig.clientId,
            page: window.ActivityFeedState.page,
            per_page: 40
        };

        var urlParams = new URLSearchParams(window.location.search);
        var staffFilter = urlParams.get('staff') || urlParams.get('user');
        var keywordFilter = urlParams.get('keyword');
        if (staffFilter) {
            requestData.staff = staffFilter;
        }
        if (keywordFilter) {
            requestData.keyword = keywordFilter;
        }

        $.ajax({
            url: window.ClientDetailConfig.urls.getActivities,
            type: 'GET',
            dataType: 'json',
            data: requestData,
            success: function(response) {
                if (response.status && response.data) {
                    // Escape template literal special characters to prevent syntax errors
                    function escapeTemplateLiteral(str) {
                        if (!str) return '';
                        return String(str)
                            .replace(/\\/g, '\\\\')
                            .replace(/`/g, '\\`')
                            .replace(/\$\{/g, '\\${');
                    }
                    
                    var html = '';
                    
                    $.each(response.data, function (k, v) {
                        var activityType = v.activity_type ?? '';
                        var noteSubtypeClass = '';
                        var subjectIcon;
                        var iconClass = '';
                        var subject = escapeTemplateLiteral(v.subject ?? '');
                        var subjectLower = subject.toLowerCase();
                        var rawMessage = v.message ?? '';
                        var isAppointmentActivity = String(rawMessage).indexOf('appointment-activity-detail') !== -1;

                        if (activityType === 'sms') {
                            subjectIcon = crmI('fas fa-sms');
                            iconClass = 'feed-icon-sms';
                        } else if (activityType === 'note') {
                            var noteIcon = 'fa-sticky-note';
                            if (subjectLower.indexOf('call') !== -1) { noteIcon = 'fa-phone'; noteSubtypeClass = ' activity-type-note-call'; }
                            else if (subjectLower.indexOf('email') !== -1) { noteIcon = 'fa-envelope'; noteSubtypeClass = ' activity-type-note-email'; }
                            else if (subjectLower.indexOf('in-person') !== -1) { noteIcon = 'fa-user-friends'; noteSubtypeClass = ' activity-type-note-in-person'; }
                            else if (subjectLower.indexOf('attention') !== -1) { noteIcon = 'fa-exclamation-triangle'; noteSubtypeClass = ' activity-type-note-attention'; }
                            else if (subjectLower.indexOf('others') !== -1) { noteIcon = 'fa-ellipsis-h'; noteSubtypeClass = ' activity-type-note-others'; }
                            subjectIcon = crmI('fas ' + noteIcon);
                            iconClass = 'feed-icon-note';
                        } else if (activityType === 'activity') {
                            if (isAppointmentActivity) {
                                subjectIcon = crmI('fas fa-calendar-check');
                                iconClass = 'feed-icon-appointment';
                            } else {
                                subjectIcon = crmI('fas fa-bolt');
                                iconClass = 'feed-icon-activity';
                            }
                        } else if (activityType === 'stage') {
                            subjectIcon = crmI('fas fa-route');
                            iconClass = 'feed-icon-stage';
                        } else if (activityType === 'financial') {
                            subjectIcon = crmI('fas fa-dollar-sign');
                            iconClass = 'feed-icon-financial';
                        } else if (activityType === 'email') {
                            subjectIcon = crmI('fas fa-envelope');
                            iconClass = 'feed-icon-email';
                        } else if (activityType === 'signature') {
                            subjectIcon = crmI('fas fa-file-signature');
                            iconClass = 'feed-icon-signature';
                        } else if (activityType === 'document') {
                            subjectIcon = crmI('fas fa-file-alt');
                            iconClass = '';
                        } else if (/uploaded email:/i.test(subjectLower)) {
                            subjectIcon = crmI('fas fa-envelope');
                            iconClass = 'feed-icon-email';
                        } else if (subjectLower.includes('invoice') || subjectLower.includes('receipt') || subjectLower.includes('ledger') || subjectLower.includes('payment') || subjectLower.includes('account')) {
                            subjectIcon = crmI('fas fa-dollar-sign');
                            iconClass = 'feed-icon-financial';
                        } else if (subjectLower.includes('document') && !/(receipt document|journal receipt document|client receipt document|office receipt document)/i.test(subjectLower)) {
                            subjectIcon = crmI('fas fa-file-alt');
                            iconClass = '';
                        } else if (subjectLower.includes('document')) {
                            subjectIcon = crmI('fas fa-file-alt');
                            iconClass = '';
                        } else {
                            subjectIcon = crmI('fas fa-sticky-note');
                            iconClass = '';
                        }

                        var description = escapeTemplateLiteral(rawMessage);
                        var taskGroup = escapeTemplateLiteral(v.task_group ?? '');
                        var followupDate = escapeTemplateLiteral(v.followup_date ?? '');
                        var date = escapeTemplateLiteral(v.date ?? '');
                        var fullName = escapeTemplateLiteral(v.name ?? '');
                        var activityTypeClass = activityType ? 'activity-type-' + activityType : '';
                        if (!activityTypeClass) {
                            if (/uploaded email:/i.test(subjectLower)) {
                                activityTypeClass = 'activity-type-email';
                            } else if (subjectLower.includes('invoice') || subjectLower.includes('receipt') || subjectLower.includes('ledger') || subjectLower.includes('payment') || subjectLower.includes('account')) {
                                activityTypeClass = 'activity-type-financial';
                            } else if (subjectLower.includes('document') && !/(receipt document|journal receipt document|client receipt document|office receipt document)/i.test(subjectLower)) {
                                activityTypeClass = 'activity-type-document';
                            }
                        }

                        var descriptionHtml = '';
                        if (v.message_truncated) {
                            descriptionHtml = '<p class="feed-item-message" data-activity-id="' + v.activity_id + '">' + description +
                                ' <button type="button" class="feed-item-show-more">Show more</button></p>';
                        } else if (description !== '') {
                            descriptionHtml = '<p>' + description + '</p>';
                        }
                        var taskGroupHtml = taskGroup !== '' ? '<p>' + taskGroup + '</p>' : '';
                        var followupDateHtml = followupDate !== '' ? '<p>' + followupDate + '</p>' : '';

                        var feedItemClass = activityType === 'stage' ? 'feed-item--stage' : 'feed-item--email';
                        var contentHtml;
                        if (activityType === 'stage') {
                            contentHtml = '<div class="feed-item-stage">' +
                                '<div class="feed-item-stage-header">' +
                                    '<span class="feed-item-staff">' + fullName + '</span>' +
                                    '<span class="feed-timestamp">' + date + '</span>' +
                                '</div>' +
                                '<div class="feed-item-stage-body">' + (v.message ? v.message : '') + '</div>' +
                            '</div>';
                        } else {
                            var subjectOnly = v.subject_without_staff_prefix === true;
                            var headline = subjectOnly ? subject : (fullName + ' ' + subject);
                            contentHtml = '<p><strong>' + headline + '</strong></p>' +
                                descriptionHtml +
                                taskGroupHtml +
                                followupDateHtml +
                                '<span class="feed-timestamp">' + date + '</span>';
                        }

                        var createdAtYmd = v.created_at_ymd || '';
                        var appointmentFeedClass = isAppointmentActivity ? ' feed-item--appointment' : '';
                        html += '<li class="feed-item ' + feedItemClass + ' activity ' + activityTypeClass + noteSubtypeClass + appointmentFeedClass + '" id="activity_' + v.activity_id + '" data-created-at="' + createdAtYmd + '">' +
                            '<span class="feed-icon ' + iconClass + '">' +
                                subjectIcon +
                            '</span>' +
                            '<div class="feed-content">' + contentHtml + '</div>' +
                        '</li>';
                    });

                    if (append) {
                        $('.feed-list .feed-item.activity').last().after(html);
                    } else {
                        $('.feed-list .feed-item.activity').remove();
                        var $emptyItem = $('.feed-list .feed-item--empty');
                        if ($emptyItem.length) {
                            $emptyItem.before(html);
                        } else if ($('.feed-list').length) {
                            $('.feed-list').prepend(html);
                        }
                    }

                    window.ActivityFeedState.fetched = true;
                    window.ActivityFeedState.hasMore = !!response.has_more;
                    $('#activity-feed-load-more-wrap').toggle(window.ActivityFeedState.hasMore);

                    if (typeof adjustActivityFeedHeight === 'function') {
                        adjustActivityFeedHeight();
                    }
                    if (window.ActivityFeed && typeof window.ActivityFeed.reapplyCurrentFilter === 'function') {
                        window.ActivityFeed.reapplyCurrentFilter();
                    }
                    if (window.ActivityFeed && typeof window.ActivityFeed.enhanceAppointmentActivityRows === 'function') {
                        window.ActivityFeed.enhanceAppointmentActivityRows();
                    }
                } else {
                    console.error('Failed to load activities:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading activities:', error);
            },
            complete: function() {
                window.ActivityFeedState.loading = false;
                $('#activity-feed-loading').hide();
                $('#activity-feed-load-more').prop('disabled', false);
                if (window.ActivityFeed && typeof window.ActivityFeed.afterActivitiesLoaded === 'function') {
                    window.ActivityFeed.afterActivitiesLoaded();
                }
                if (window.ClientDetailConfig && window.ClientDetailConfig.activeTab === 'activityfeed' && typeof window.ensureClientDetailActions === 'function') {
                    window.ensureClientDetailActions();
                }
                if (window.ActivityFeedState.pendingReset) {
                    window.ActivityFeedState.pendingReset = false;
                    window.loadActivities({ reset: true });
                }
            }
        });
    };

    // First fetch is from sidebar-tabs when the feed is shown (Personal / Company / Activity).
