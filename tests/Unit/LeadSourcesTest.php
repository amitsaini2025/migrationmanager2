<?php

namespace Tests\Unit;

use App\Support\LeadSources;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LeadSourcesTest extends TestCase
{
    #[Test]
    public function options_returns_configured_sources(): void
    {
        $this->assertContains('Meta Ads', LeadSources::options());
        $this->assertContains('Other', LeadSources::options());
    }

    #[Test]
    public function legacy_unset_values_are_treated_as_not_selected(): void
    {
        $this->assertTrue(LeadSources::isLegacyUnset('Others'));
        $this->assertTrue(LeadSources::isLegacyUnset('SubAgent'));
        $this->assertNull(LeadSources::displayValue('Others'));
        $this->assertNull(LeadSources::selectedValueForEdit('Others'));
    }

    #[Test]
    public function configured_option_displays_and_preselects_on_edit(): void
    {
        $this->assertSame('WhatsApp', LeadSources::displayValue('WhatsApp'));
        $this->assertSame('WhatsApp', LeadSources::selectedValueForEdit('WhatsApp'));
    }

    #[Test]
    public function external_sources_display_but_do_not_preselect_on_edit(): void
    {
        $this->assertSame('Public Appointment', LeadSources::displayValue('Public Appointment'));
        $this->assertNull(LeadSources::selectedValueForEdit('Public Appointment'));
    }

    #[Test]
    public function resolve_on_save_preserves_external_source_when_left_blank(): void
    {
        $this->assertSame(
            'Public Appointment',
            LeadSources::resolveOnSave('', 'Public Appointment')
        );
    }

    #[Test]
    public function resolve_on_save_clears_legacy_unset_when_left_blank(): void
    {
        $this->assertNull(LeadSources::resolveOnSave('', 'Others'));
    }

    #[Test]
    public function allowed_values_includes_external_source_for_updates(): void
    {
        $allowed = LeadSources::allowedValues('Bansal Website');

        $this->assertContains('Bansal Website', $allowed);
        $this->assertContains('Referral', $allowed);
    }
}
