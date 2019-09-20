<?php

class LodgixAvailability {

    function __construct($config) {
        $this->ownerId = $config->get('p_lodgix_owner_id');
        $this->displayHelp = ($config->get('p_lodgix_display_multi_instructions') == 1);

        $properties = (new LodgixServiceProperties($config))->getAll();
        if ($properties) {
            $this->nProperties = count($properties);
            $this->property = $properties[0];
        } else {
            $this->nProperties = 0;
            $this->property = null;
        }

        $this->website = 'https://www.lodgix.com';
    }

    function page() {
        $html = '';
        if ($this->nProperties > 0) {
            if ($this->nProperties > 1) {
                $calendar = $this->multiUnitCalendar();
            } else {
                $calendar = $this->singleUnitCalendar();
            }
            $html .= <<<EOT
<div class="ldgxCalendar ldgxCalendarAvailability">
    $calendar
</div>
EOT;
        }
        return $html;
    }

    protected function multiUnitCalendar() {
        $arr = explode('-', $this->ownerId);
        if (!$arr) {
            return '';
        }
        $ownerId = $arr[0];
        if (count($arr) > 1) {
            $wpsite = $arr[1];
        } else {
            $wpsite = '0';
        }
        $language = str_replace('_', '-', get_locale());
        $html = <<<EOT
<script src="https://www.lodgix.com/static/booking-calendar/multi/iframe/lodgix.min.js"></script>
<script>new LodgixCalendarMulti(new LodgixOrigin('https://www.lodgix.com','/booking-calendar/websites/$ownerId/wpsites/$wpsite/categories/0/'),'$language')</script>
EOT;
        return $html;
    }

    protected function singleUnitCalendar() {
        $html = '';
        if ($this->property) {
            $ownerId = $this->property->owner_id;
            $propertyId = $this->property->id;
            $html .= <<<EOT
<script src="https://www.lodgix.com/static/booking-calendar/single/iframe/lodgix.min.js"></script>
<script>new LodgixCalendarSingle(new LodgixOrigin('https://www.lodgix.com','/booking-calendar/websites/$ownerId/properties/$propertyId/'))</script>
EOT;
        }
        return $html;
    }

}
