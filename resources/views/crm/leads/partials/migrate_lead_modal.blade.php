{{-- Migrate Lead invite modal (additive; does not affect createLeadForm submit) --}}
<div id="migrateLeadModal" class="migrate-lead-modal" style="display:none;" aria-hidden="true">
    <div class="migrate-lead-modal__backdrop" data-migrate-lead-close></div>
    <div class="migrate-lead-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="migrateLeadModalTitle">
        <div class="migrate-lead-modal__header">
            <h4 id="migrateLeadModalTitle">Migrate Lead</h4>
            <button type="button" class="migrate-lead-modal__close" data-migrate-lead-close aria-label="Close">&times;</button>
        </div>
        <div class="migrate-lead-modal__body">
            <p class="migrate-lead-modal__hint">Send a one-time enquiry form link by email or Australian mobile number.</p>

            <div class="migrate-lead-channel-tabs">
                <button type="button" class="migrate-lead-tab active" data-migrate-channel="email">Lead email</button>
                <button type="button" class="migrate-lead-tab" data-migrate-channel="sms">Lead contact number</button>
            </div>

            <div class="migrate-lead-field" data-migrate-panel="email">
                <label for="migrate_lead_email">Lead email</label>
                <input type="email" id="migrate_lead_email" maxlength="150" placeholder="name@example.com" autocomplete="off">
            </div>

            <div class="migrate-lead-field" data-migrate-panel="sms" style="display:none;">
                <label for="migrate_lead_phone">Australian mobile number</label>
                <input type="tel" id="migrate_lead_phone" maxlength="25" placeholder="04XX XXX XXX" autocomplete="off">
            </div>

            <div id="migrateLeadMessage" class="migrate-lead-message" style="display:none;" role="status"></div>
        </div>
        <div class="migrate-lead-modal__footer">
            <button type="button" class="btn btn-secondary" data-migrate-lead-close>Cancel</button>
            <button type="button" class="btn btn-primary" id="migrateLeadSubmitBtn">Send invite</button>
        </div>
    </div>
</div>

<style>
.migrate-lead-modal{position:fixed;inset:0;z-index:1050;display:flex;align-items:center;justify-content:center}
.migrate-lead-modal__backdrop{position:absolute;inset:0;background:rgba(15,30,50,.45)}
.migrate-lead-modal__dialog{position:relative;z-index:1;width:min(440px,92vw);background:#fff;border-radius:12px;box-shadow:0 18px 50px rgba(20,40,70,.25);overflow:hidden}
.migrate-lead-modal__header{display:flex;align-items:center;justify-content:space-between;padding:16px 18px;border-bottom:1px solid #e6eef8;background:#f7fbff}
.migrate-lead-modal__header h4{margin:0;font-size:16px;color:#0f4d93}
.migrate-lead-modal__close{border:0;background:transparent;font-size:24px;line-height:1;color:#65748a;cursor:pointer}
.migrate-lead-modal__body{padding:18px}
.migrate-lead-modal__hint{margin:0 0 14px;font-size:13px;color:#5d6f86}
.migrate-lead-channel-tabs{display:flex;gap:8px;margin-bottom:14px}
.migrate-lead-tab{flex:1;border:1px solid #c8d4e3;background:#fff;color:#314862;border-radius:8px;padding:9px 10px;font-size:13px;font-weight:600;cursor:pointer}
.migrate-lead-tab.active{background:#e8f2ff;border-color:#0f4d93;color:#0f4d93}
.migrate-lead-field label{display:block;font-size:13px;font-weight:600;margin-bottom:6px}
.migrate-lead-field input{width:100%;border:1px solid #c8d4e3;border-radius:8px;min-height:42px;padding:8px 12px;font-size:14px}
.migrate-lead-message{margin-top:12px;padding:10px 12px;border-radius:8px;font-size:13px}
.migrate-lead-message.is-error{background:#fff5f5;color:#8a1f2f;border:1px solid #f0c2c8}
.migrate-lead-message.is-success{background:#f0fff4;color:#1e6b3a;border:1px solid #b7e4c7}
.migrate-lead-modal__footer{display:flex;justify-content:flex-end;gap:8px;padding:14px 18px;border-top:1px solid #e6eef8;background:#fafbfd}
</style>
