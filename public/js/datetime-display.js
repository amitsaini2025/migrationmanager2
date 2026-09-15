/**
 * Human-readable local datetime for CRM UI
 * (ISO / Laravel JSON / unix ms / legacy "d/m/Y h:i A" → "24 Mar 2026, 8:32 am").
 * Invalid or unparseable values return '' (never echoes arbitrary strings into HTML).
 */
(function (global) {
    'use strict';

    var MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    function partsFromDate(d) {
        var day = d.getDate();
        var mon = MONTHS[d.getMonth()];
        var y = d.getFullYear();
        var h24 = d.getHours();
        var m = d.getMinutes();
        var ap = h24 >= 12 ? 'pm' : 'am';
        var h12 = h24 % 12;
        if (h12 === 0) h12 = 12;
        var mm = m < 10 ? '0' + m : String(m);
        return day + ' ' + mon + ' ' + y + ', ' + h12 + ':' + mm + ' ' + ap;
    }

    /**
     * Parse ISO / Laravel JSON, unix ms, or CRM legacy "d/m/Y h:i A".
     * Native Date() treats slash dates as m/d/Y, which breaks AU d/m/Y payloads.
     */
    function parseDisplayDateInput(value) {
        if (value == null || value === '') return null;

        if (typeof value === 'number' && isFinite(value)) {
            var fromNumber = new Date(value);
            return isNaN(fromNumber.getTime()) ? null : fromNumber;
        }

        var s = typeof value === 'string' ? value.trim() : String(value);
        if (!s) return null;

        var legacy = s.match(
            /^(\d{1,2})\/(\d{1,2})\/(\d{4})(?:\s+(\d{1,2}):(\d{2})(?::(\d{2}))?\s*(am|pm)?)?$/i
        );
        if (legacy) {
            var day = parseInt(legacy[1], 10);
            var month = parseInt(legacy[2], 10) - 1;
            var year = parseInt(legacy[3], 10);
            var hour = legacy[4] != null ? parseInt(legacy[4], 10) : 0;
            var minute = legacy[5] != null ? parseInt(legacy[5], 10) : 0;
            var second = legacy[6] != null ? parseInt(legacy[6], 10) : 0;
            var ap = legacy[7] ? legacy[7].toLowerCase() : null;
            if (ap === 'pm' && hour < 12) {
                hour += 12;
            }
            if (ap === 'am' && hour === 12) {
                hour = 0;
            }
            var fromLegacy = new Date(year, month, day, hour, minute, second);
            // Reject JS date rollover (e.g. 31/02/2026 → March).
            if (
                isNaN(fromLegacy.getTime()) ||
                fromLegacy.getFullYear() !== year ||
                fromLegacy.getMonth() !== month ||
                fromLegacy.getDate() !== day
            ) {
                return null;
            }
            return fromLegacy;
        }

        var fromNative = new Date(s);
        return isNaN(fromNative.getTime()) ? null : fromNative;
    }

    function formatDisplayDateTime(iso) {
        var d = parseDisplayDateInput(iso);
        if (!d) return '';

        return partsFromDate(d);
    }

    global.formatDisplayDateTime = formatDisplayDateTime;
    global.formatGrantWhen = formatDisplayDateTime;
}(typeof window !== 'undefined' ? window : this));
