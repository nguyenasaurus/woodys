<?php

class LodgixPropertyDetail
{

    function __construct(
        $property,
        $dateFormat,
        $displayRatesDaily = true,
        $displayRatesWeekly = true,
        $displayRatesMonthly = true,
        $displayMinStay = true,
        $displaySimpleRates = true,
        $displayMergedRates = true,
        $displayMergedRatesNoDefault = true,
        $permalink = '',
        $emailUrl = '',
        $iconSet = LodgixConst::ICON_SET_OLD,
        $alwaysDisplayBookNow = true,
        $displayBeds = true,
        $displayCityRegistration = true,
        $imageSize = LodgixConst::IMAGE_640x480,
        $mapZoom = 13,
        $language = 'en',
        $displayHiddenTabs = true
    )
    {
        $this->property = $property;
        $this->dateFormat = $dateFormat;
        $this->displaySimpleRates = $displaySimpleRates;
        $this->displayMergedRates = $displayMergedRates;
        $this->displayMergedRatesNoDefault = $displayMergedRatesNoDefault;
        $this->language = $language;

        $lsp = new LodgixServiceProperty($property, $language);

        $this->minStay = $lsp->minStay();
        $this->displayMinStay = $this->minStay[0] > 1 ? $displayMinStay : false;
        $this->displayRatesDaily = $this->minStay[0] < 7 ? $displayRatesDaily : false;
        $this->displayRatesWeekly = $this->minStay[0] < 30 ? $displayRatesWeekly : false;
        $this->displayRatesMonthly = $displayRatesMonthly;

        $this->ratesDaily = $lsp->ratesDaily();
        $this->ratesWeekly = $lsp->ratesWeekly();
        $this->ratesMonthly = $lsp->ratesMonthly();
        $this->ratesMerged = $displayMergedRates || $displayMergedRatesNoDefault ? $lsp->ratesMerged() : null;
        $this->taxes = $lsp->taxes();
        $this->fees = $lsp->fees();
        $this->deposits = $lsp->deposits();
        $this->policies = $lsp->policies();
        $this->amenityCategories = $lsp->amenityCategories();
        $this->reviews = $lsp->reviews();
        $this->photos = $lsp->photos();
        $this->category = $lsp->mainCategory();

        if (empty($permalink)) {
            $void = 'javascript:void(0)';
            $this->permalink = $void;
            $this->bookingUrl = $void;
            $this->emailUrl = $void;
        } else {
            $this->permalink = $permalink;
            $this->bookingUrl = $permalink . '#booking';
            $this->emailUrl = $emailUrl;
        }

        $this->iconSet = $iconSet;
        $this->alwaysDisplayBookNow = $alwaysDisplayBookNow;
        $this->displayBeds = $displayBeds;
        $this->displayCityRegistration = $displayCityRegistration;
        $this->imageSize = $imageSize;
        $this->mapZoom = $mapZoom;
        $this->displayHiddenTabs = $displayHiddenTabs;
    }

    function single(
        $showDetails,
        $showAmenities,
        $showReviews,
        $showCalendar,
        $showPolicies,
        $showLocation
    )
    {
        $header = $this->header();
        if ($showDetails) {
            $slider = $this->slider();
            $buttonCheckAvailability = $this->buttonCheckAvailability();
            $description = $this->description();
            $details = $this->details();
        } else {
            $slider = '';
            $buttonCheckAvailability = '';
            $description = '';
            $details = '';
        }
        $amenities = $showAmenities ? $this->amenities() : '';
        $reviews = $showReviews ? $this->reviews() : '';
        $calendar = $showCalendar ? $this->calendar() : '';
        if ($showPolicies) {
            $rates = $this->rates();
            $taxesFeesDepositsPolicies = $this->taxesFeesDepositsPolicies();
        } else {
            $rates = '';
            $taxesFeesDepositsPolicies = '';
        }
        $map = $showLocation ? $this->map() : '';
        $link = $this->lodgixLink();
        $html = <<<EOT
<div class="ldgxPropertySingle ldgxPropertyWrapper ldgxPropertySingleWrapper">
    $header
    $slider
    $buttonCheckAvailability
    $description
    $details
    $amenities
    $reviews
    $rates
    $calendar
    $taxesFeesDepositsPolicies
    $map
    <div class="ldgxPowered">$link by Lodgix.com</div>
</div>
EOT;
        return $html;
    }

    function tabs(
        $tabTitleDetails,
        $tabTitleCalendar,
        $tabTitleLocation,
        $tabTitleAmenities,
        $tabTitlePolicies,
        $tabTitleReviews
    )
    {
        $header = $this->header(true);
        $tabs = $this->tabsTemplate(
            $tabTitleDetails,
            $tabTitleCalendar,
            $tabTitleLocation,
            $tabTitleAmenities,
            $tabTitlePolicies,
            $tabTitleReviews,
            $this->sliderDescriptionDetails(false),
            $this->calendar(),
            $this->map(),
            $this->amenities(),
            $this->ratesTaxesFeesDepositsPolicies(),
            $this->reviews()
        );
        $html = <<<EOT
<div class="ldgxPropertyTabs ldgxPropertyWrapper ldgxPropertyTabbedWrapper">
    $header
    <div class="ldgxTabbedContentBox">
        <div id="lodgix_tabbed_content" class="ldgxTabbedContent">
            $tabs
        </div>
    </div>
</div>
<script>
    jQueryLodgix(document).ready(function(){
        jQueryLodgix('.ldgxTabbedContent').tabs({
            create: function(event, ui) {
                LodgixSlider.init('.ldgxSlider');
            },
            show: function(event, ui) {
                // if (ui.index == 1 && typeof lodgixUnitCalendarInstance != "undefined") {
                //     lodgixUnitCalendarInstance.resize();
                // }
            }
        });
        if(document.location.hash == "#booking") {
            window.scrollTo(0,0);
            jQueryLodgix(".ldgxTabbedContent").tabs("select", 1);
        }
        if(document.location.hash == "#map") {
            jQueryLodgix(".ldgxTabbedContent").tabs("select", 2);
        }
    });
</script>
EOT;
        return $html;
    }

    function tabsTemplate(
        $tabTitleDetails,
        $tabTitleCalendar,
        $tabTitleLocation,
        $tabTitleAmenities,
        $tabTitlePolicies,
        $tabTitleReviews,
        $slider = '',
        $calendar = '',
        $map = '',
        $amenities = '',
        $ratesTaxesFeesDepositsPolicies = '',
        $reviews = ''
    )
    {
        $html = '<ul class="ldgxTabs">';
        $html .= $this->tabHeader($tabTitleDetails, 'lodgix_tabbed_details', 'ldgxTabDetails', '#lodgix_tabbed_content-1');
        $html .= $this->tabHeader($tabTitleCalendar, 'lodgix_tabbed_booking_calendar', 'ldgxTabCalendar', '#lodgix_tabbed_content-2');
        $html .= $this->tabHeader($tabTitleLocation, 'lodgix_tabbed_location', 'ldgxTabLocation', '#lodgix_tabbed_content-3');
        $html .= $this->tabHeader($tabTitleAmenities, 'lodgix_tabbed_amenities', 'ldgxTabAmenities', '#lodgix_tabbed_content-4');
        $html .= $this->tabHeader($tabTitlePolicies, 'lodgix_tabbed_policies', 'ldgxTabPolicies', '#lodgix_tabbed_content-5');
        $html .= $this->tabHeader($tabTitleReviews, 'lodgix_tabbed_reviews', 'ldgxTabReviews', '#lodgix_tabbed_content-6');
        $html .= '</ul>';
        $html .= $this->tabContent($tabTitleDetails, 'ldgxMobileTabDetails', '1', 'lodgix_tabbed_content-1', 'ldgxTabContentDetails', $slider);
        $html .= $this->tabContent($tabTitleCalendar, 'ldgxMobileTabCalendar', '2', 'lodgix_tabbed_content-2', 'ldgxTabContentCalendar', $calendar);
        $html .= $this->tabContent($tabTitleLocation, 'ldgxMobileTabLocation', '3', 'lodgix_tabbed_content-3', 'ldgxTabContentLocation', $map);
        $html .= $this->tabContent($tabTitleAmenities, 'ldgxMobileTabAmenities', '4', 'lodgix_tabbed_content-4', 'ldgxTabContentAmenities', $amenities);
        $html .= $this->tabContent($tabTitlePolicies, 'ldgxMobileTabPolicies', '5', 'lodgix_tabbed_content-5', 'ldgxTabContentPolicies', $ratesTaxesFeesDepositsPolicies);
        $html .= $this->tabContent($tabTitleReviews, 'ldgxMobileTabReviews', '6', 'lodgix_tabbed_content-6', 'ldgxTabContentReviews', $reviews);
        return $html;
    }

    protected function tabHeader($tabTitle, $id, $class, $href)
    {
        $html = '';
        if ($tabTitle || $this->displayHiddenTabs) {
            $label = LodgixTranslate::translate($tabTitle);
            $html .= <<<EOT
<li id="$id" class="ldgxTab $class">
    <a href="$href">$label</a>
</li>
EOT;
        }
        return $html;
    }

    protected function tabContent($tabTitle, $classMobile, $n, $id, $classContent, $content)
    {
        $html = '';
        if ($tabTitle || $this->displayHiddenTabs) {
            $label = LodgixTranslate::translate($tabTitle);
            $html .= <<<EOT
<div class="ldgxMobileTab $classMobile" onclick="jQueryLodgix('.ldgxTabbedContent').tabs('toggle','$n',this)">
    $label
</div>
<div id="$id" class="ldgxTabContent $classContent">
    $content
</div>
EOT;
        }
        return $html;
    }

    function header($isTabs = false)
    {
        $description = $this->property->description;
        $bedrooms = $this->property->bedrooms ? $this->property->bedrooms . ' ' . LodgixTranslate::translate('Bedrooms') : LodgixTranslate::translate('Studio');
        $bathrooms = $this->property->bathrooms . ' ' . LodgixTranslate::translate('Bathrooms');
        $type = LodgixTranslate::translate($this->property->proptype);
        $city = $this->property->city ? LodgixTranslate::translate('in') . ' ' . $this->property->city : '';
        $minDailyRate = $this->rateDailyMin();
        $minWeeklyRate = $this->rateWeeklyMin();
        $minMonthlyRate = $this->rateMonthlyMin();
        $minStay = $this->rateMinStay();
        $buttonBookNow = $this->buttonBookNow($isTabs);
        $mapIcon = $isTabs ? '' : $this->mapIcon();
        $videoIcon = $this->videoIcon();
        $virtualTourIcon = $this->virtualTourIcon();
        $emailIcon = $this->emailIcon();
        $isPets = $this->property->pets ? '' : 'style="display:none"';
        $noPets = $this->property->pets ? 'style="display:none"' : '';
        $noSmoking = $this->property->smoking ? 'style="display:none"' : '';
        $class = LodgixConst::$ICON_SET_CLASS[$this->iconSet];
        $html = <<<EOT
<div class="ldgxPropBadge">
    <div class="ldgxPropBadgeLine ldgxPropBadgeLine1">
        <div class="ldgxPropBadgeTitle">
            <span class="ldgxPropBadgeName">$description</span>
            <div class="ldgxPropBadgeRooms">
                <span class="ldgxPropBadgeRoomsBeedrooms">$bedrooms</span>
                <span class="ldgxPropBadgeRoomsSeparator"></span>
                <span class="ldgxPropBadgeRoomsBathrooms">$bathrooms</span>
                <span class="ldgxPropBadgeRoomsSeparator"></span>
                <span class="ldgxPropBadgeRoomsType">$type</span>
                <span class="ldgxPropBadgeRoomsCity">$city</span>
            </div>
        </div>
        <div class="ldgxPropBadgeRates">
            $minDailyRate
            $minWeeklyRate
            $minMonthlyRate
            $minStay
            $buttonBookNow
        </div>
        <div class="ldgxPropBadgeSeparator"></div>
    </div>
    <div class="ldgxPropBadgeLine ldgxPropBadgeLine2">
        <div class="ldgxPropBadgeIconsLeft">
            $mapIcon
            $videoIcon
            $virtualTourIcon
            $emailIcon
        </div>
        <div class="ldgxPropBadgeIconsRight">
            <span class="ldgxButton ldgxButtonPets ldgxButtonPets$class" $isPets title="Pet friendly"></span>
            <span class="ldgxButton ldgxButtonPetsNo ldgxButtonPetsNo$class" $noPets title="No pets"></span>
            <span class="ldgxButton ldgxButtonSmokeNo ldgxButtonSmokeNo$class" $noSmoking title="No smoking"></span>
        </div>
        <div class="ldgxPropBadgeSeparator"></div>
    </div>
</div>
EOT;
        return $html;
    }

    protected function rateDailyMin()
    {
        $html = '';
        if ($this->displayRatesDaily && $this->ratesDaily[0] != null) {
            $from = LodgixTranslate::translate('from');
            $per = LodgixTranslate::translate('per /nt');
            $rate = $this->ratesDaily[0];
            $html .= <<<EOT
<div class="ldgxPropBadgeRatesDaily">
    $from $rate $per
</div>
EOT;
        }
        return $html;
    }

    protected function rateWeeklyMin()
    {
        $html = '';
        if ($this->displayRatesWeekly && $this->ratesWeekly[0] != null) {
            $from = LodgixTranslate::translate('from');
            $per = LodgixTranslate::translate('per /wk');
            $rate = $this->ratesWeekly[0];
            $html .= <<<EOT
<div class="ldgxPropBadgeRatesWeekly">
    $from $rate $per
</div>
EOT;
        }
        return $html;
    }

    protected function rateMonthlyMin()
    {
        $html = '';
        if ($this->displayRatesMonthly && $this->ratesMonthly[0] != null) {
            $from = LodgixTranslate::translate('from');
            $per = LodgixTranslate::translate('per /mo');
            $rate = $this->ratesMonthly[0];
            $html .= <<<EOT
<div class="ldgxPropBadgeRatesMonthly">
    $from $rate $per
</div>
EOT;
        }
        return $html;
    }

    protected function rateMinStay()
    {
        $html = '';
        if ($this->displayMinStay) {
            $ntMin = LodgixTranslate::translate('nt min');
            $minStay = $this->minStay[0];
            $html .= <<<EOT
<div class="ldgxPropBadgeMinStay">
    $minStay $ntMin
</div>
EOT;
        }
        return $html;
    }

    protected function mapIcon()
    {
        $label = LodgixTranslate::translate('Display Google Map');
        $class = LodgixConst::$ICON_SET_CLASS[$this->iconSet];
        $html = <<<EOT
<a title="$label" href="#map" class="ldgxButton ldgxButtonMap ldgxButtonMap$class"></a>
EOT;
        return $html;
    }

    protected function videoIcon()
    {
        $html = '';
        $url = $this->property->video_url;
        if ($url) {
            $class = LodgixConst::$ICON_SET_CLASS[$this->iconSet];
            $html .= <<<EOT
<a style="margin-left:5px" data-lity href="$url" class="ldgxButton ldgxButtonVideo ldgxButtonVideo$class"></a>
EOT;
        }
        return $html;
    }

    protected function virtualTourIcon()
    {
        $html = '';
        $url = $this->property->virtual_tour_url;
        if ($url) {
            $class = LodgixConst::$ICON_SET_CLASS[$this->iconSet];
            $html .= <<<EOT
<a target="_blank" style="margin-left:5px" href="$url" class="ldgxButton ldgxButtonTour ldgxButtonTour$class"></a>
EOT;
        }
        return $html;
    }

    protected function emailIcon()
    {
        $html = '';
        $url = $this->emailUrl;
        if ($url) {
            $label = LodgixTranslate::translate('Contact Us');
            $url = str_replace('__PROPERTY__', $this->property->description, $url);
            $url = str_replace('__PROPERTYID__', $this->property->id, $url);
            $class = LodgixConst::$ICON_SET_CLASS[$this->iconSet];
            $html .= <<<EOT
<a title="$label" style="margin-left:5px" href="$url" class="ldgxButton ldgxButtonMail ldgxButtonMail$class"></a>
EOT;
        }
        return $html;
    }

    protected function buttonBookNow($isTabs = false)
    {
        $html = '';
        $label = LodgixTranslate::translate('Book Now');
        if (!empty($this->property->really_available) && $this->property->allow_booking) {
            $url = $this->property->booklink;
            $html .= <<<EOT
<div class="ldgxBookNow"><a href="$url">$label</a></div>
EOT;
        } else {
            $class = $this->alwaysDisplayBookNow ? '' : 'ldgxBookNowOfflineHidden';
            if ($isTabs) {
                $html .= <<<EOT
<div class="ldgxBookNow ldgxBookNowOffline $class"><a href="javascript:void(0)" onclick="jQueryLodgix('.ldgxTabbedContent').tabs('select','2')">$label</a></div>
EOT;
            } else {
                $html .= <<<EOT
<div class="ldgxBookNow ldgxBookNowOffline $class"><a href="#booking">$label</a></div>
EOT;
            }
        }
        return $html;
    }

    protected function sliderDescriptionDetails($isInit = true)
    {
        $html = '';
        $html .= $this->slider($isInit);
        $html .= $this->description();
        $html .= $this->details();
        return $html;
    }

    function slider($isInit = true)
    {
        $label = LodgixTranslate::translate('Property Details');
        $imageWidth = LodgixConst::$IMAGE_WIDTH[$this->imageSize];
        $style = $imageWidth ? 'style="max-width:' . $imageWidth . 'px"' : '';
        $html = <<<EOT
<div class="ldgxPropertyBlock ldgxPropertyBlockSlider">
    <h2>$label</h2>
    <div class="ldgxSliderWrapper" $style>
        <div class="ldgxSlider royalSlider rsDefaultInv">
EOT;
        foreach ($this->photos as $photo) {
            $caption = htmlspecialchars($photo->caption, ENT_QUOTES);
            $previewUrl = $imageWidth ? $photo->preview_url : $photo->url;
            $html .= <<<EOT
            <a class="rsImg" data-rsBigImg="$photo->url" data-rsTmb="$photo->thumb_url" href="$previewUrl">$caption</a>
EOT;
        }
        $html .= <<<EOT
        </div>
    </div>
</div>
EOT;
        if ($isInit) {
            $html .= <<<EOT
<script>LodgixSlider.initOnDocumentReady('.ldgxSlider')</script>
EOT;
        }
        return $html;
    }

    protected function buttonCheckAvailability()
    {
        if ($this->property->really_available && $this->property->allow_booking) {
            $label = LodgixTranslate::translate('Book Now');
            $url = $this->property->booklink;
            $pluginUrl = plugin_dir_url(plugin_basename(PLUGIN_PATH));
            $html = <<<EOT
<div class="ldgxPropertyBlock ldgxPropertyBlockCheckAvailability">
    <a title="$label" href="$url"><img src="$pluginUrl/images/booknow.png"></a>
</div>
EOT;
        } else {
            $label = LodgixTranslate::translate('Check Availability');
            $url = $this->bookingUrl;
            $html = <<<EOT
<div class="ldgxPropertyBlock ldgxPropertyBlockCheckAvailability">
    <a title="$label" href="$url" class="lodgix_check_availability_icon"></a>
</div>
EOT;
        }
        return $html;
    }

    function description()
    {
        $html = '';
        if ($this->property->description_long) {
            $label = LodgixTranslate::translate('Property Description');
            $value = nl2br($this->property->description_long);
            $html .= <<<EOT
<div class="ldgxPropertyBlock ldgxPropertyBlockDescription">
    <h2>$label</h2>
    <div class="ldgxPropertyInfoDesc">
        $value
    </div>
</div>
EOT;
        }
        return $html;
    }

    function details($inject1 = '', $inject2 = '')
    {
        $label = LodgixTranslate::translate('Property Details');
        $value = nl2br($this->property->details);
        $beds = $this->infoBeds();
        $cityRegistration = $this->infoCityRegistration();
        $html = <<<EOT
<div class="ldgxPropertyBlock ldgxPropertyBlockDetails">
    <h2>$label</h2>
    <div class="ldgxPropertyInfoDetails">
        $value
    </div>
    $inject1
    $beds
    $inject2
    $cityRegistration
</div>
EOT;
        return $html;
    }

    protected function infoBeds()
    {
        $html = '';
        if ($this->displayBeds && $this->property->beds_text) {
            $label = LodgixTranslate::translate('This property has');
            $value = $this->property->beds_text;
            $html .= <<<EOT
<div class="ldgxPropertyInfoBeds">
    $label $value.
</div>
EOT;
        }
        return $html;
    }

    protected function infoCityRegistration()
    {
        $html = '';
        if ($this->displayCityRegistration && $this->property->city_registration) {
            $label = LodgixTranslate::translate('City Registraton:');
            $value = $this->property->city_registration;
            $html .= <<<EOT
<div class="ldgxPropertyInfoCityRegistration">
    $label $value
</div>
EOT;
        }
        return $html;
    }

    function amenities()
    {
        $html = '';
        $amenityCategories = $this->amenityCategories;
        if (count($amenityCategories)) {
            $label = LodgixTranslate::translate('Amenities');
            $amenities = '';
            foreach ($amenityCategories as $amenityCategory) {
                $amenities .= $this->amenityCategory($amenityCategory);
            }
            $html .= <<<EOT
<div class="ldgxPropertyBlock ldgxPropertyBlockAmenities">
    <h2>$label</h2>
    $amenities
</div>
EOT;
        }
        return $html;
    }

    function amenityCategory($amenityCategory)
    {
        $html = '';
        $amenityNames = $amenityCategory['amenity_names'];
        if (count($amenityNames)) {
            $categoryName = LodgixTranslate::translateTerm($amenityCategory['category_name'], $this->language);
            $amenities = '';
            foreach ($amenityNames as $amenity) {
                $amenities .= '<li>' . LodgixTranslate::translateTerm($amenity, $this->language) . '</li>';
            }
            $html .= <<<EOT
<div class="ldgxAmenityCategory">$categoryName</div>
<ul class="amenities ldgxAmenities">$amenities</ul>
EOT;
        }
        return $html;
    }

    function reviews()
    {
        $html = '';
        if (count($this->reviews)) {
            $label = LodgixTranslate::translate('Guest Reviews');
            $reviews = $this->reviewList();
            $html .= <<<EOT
<div class="ldgxPropertyBlock ldgxPropertyBlockReviews">
    <h2>$label</h2>
    $reviews
</div>
EOT;
        }
        return $html;
    }

    protected function reviewList()
    {
        $html = '';
        if (count($this->reviews)) {
            $class = LodgixConst::$ICON_SET_CLASS[$this->iconSet];
            foreach ($this->reviews as $review) {
                $nStars = $review->stars;
                if ($nStars < 1 || $nStars > 5) {
                    $nStars = 5;
                }
                $title = $review->title;
                if (!$title) {
                    $title = $this->firstSentence($review->description);
                }
                $starsOn = str_repeat('<i class="ldgxStar"></i>', $nStars);
                $starsOff = str_repeat('<i class="ldgxStar ldgxStarGrey"></i>', 5 - $nStars);
                $date = strftime($this->dateFormat, strtotime($review->date));
                $text = nl2br($review->description);
                $html .= <<<EOT
<div class="ldgxReviewBlock">
    <div class="ldgxReviewDateBlock">
        <span class="ldgxReviewIcon ldgxButton ldgxButtonMedium ldgxButtonReview ldgxButtonReview$class"></span>
        <span class="ldgxReviewTitle">$title</span>
        <span class="ldgxReviewStars">$starsOn$starsOff</span>
        <span class="ldgxReviewBy">by</span> <span class="ldgxReviewName">$review->name</span>
        <span class="ldgxReviewDate">$date</span>
    </div>
    <div class="ldgxReviewTextBlock">$text</div>
</div>
EOT;
            }
        }
        return $html;
    }

    protected function firstSentence($text)
    {
        $sentence = preg_replace('/(.*?(?:[?!.](?=\s|$)|\n)).*/m', '\\1', $text);
        return trim($sentence, " \t\n\r\0\x0B\"“”");
    }

    function rates($title = 'Rates')
    {
        $html = '';
        $ratesSimple = $this->displaySimpleRates ? $this->simpleRates() : '';
        $ratesMerged = $this->displayMergedRates ? $this->mergedRates() : '';
        $ratesMergedNoDefault = $this->displayMergedRatesNoDefault ? $this->mergedRates(false) : '';
        if ($ratesSimple || $ratesMerged || $ratesMergedNoDefault) {
            $label = LodgixTranslate::translate($title);
            $help1 = LodgixTranslate::translate('Rate varies due to seasonality and holidays');
            $help2 = LodgixTranslate::translate('Please select your dates on our online booking calendar for an exact quote');
            $html .= <<<EOT
<div class="ldgxPropertyBlock ldgxPropertyBlockRates">
    <h2>$label</h2>
    $ratesSimple
    $ratesMerged
    $ratesMergedNoDefault
    <div class="ldgxPropertyRatesHelp">
        - $help1.<br>
        - $help2.
    </div>
</div>
EOT;
        }
        return $html;
    }

    function taxesFeesDepositsPolicies()
    {
        $html = '';
        if ($this->taxes || $this->fees || $this->deposits || $this->policies) {
            $label = LodgixTranslate::translate('Policies');
            $taxes = $this->taxes();
            $fees = $this->fees();
            $deposits = $this->deposits();
            $policies = $this->policies();
            $html .= <<<EOT
<div class="ldgxPropertyBlock ldgxPropertyBlockPolicies">
    <h2>$label</h2>
    $taxes
    $fees
    $deposits
    $policies
</div>
EOT;
        }
        return $html;
    }

    protected function ratesTaxesFeesDepositsPolicies()
    {
        $html = $this->rates('Policies');
        $html .= $this->taxesFeesDepositsPolicies();
        return $html;
    }

    protected function mergedRates($showDefaultRates = true)
    {
        $isDaily = false;
        $isWeekly = false;
        $isMonthly = false;
        foreach ($this->ratesMerged as $rate) {
            if ($showDefaultRates || !$rate->is_default) {
                if ($rate->min_stay < 7 && ($rate->nightly || $rate->weekend_nightly)) {
                    $isDaily = true;
                }
                if ($rate->min_stay < 30 && $rate->weekly) {
                    $isWeekly = true;
                }
                if ($rate->monthly) {
                    $isMonthly = true;
                }
            }
        }
        $label = LodgixTranslate::translate('Dates');
        $thWeekday = $isDaily ? $this->mergedRatesTH('Wkdy / Wknd') : '';
        $thWeekly = $isWeekly ? $this->mergedRatesTH('Weekly') : '';
        $thMonthly = $isMonthly ? $this->mergedRatesTH('Monthly') : '';
        $rows = '';
        $even = true;
        foreach ($this->ratesMerged as $rate) {
            if ($showDefaultRates || !$rate->is_default) {
                $rows .= $this->mergedRatesRow($rate, $even, $isDaily, $isWeekly, $isMonthly);
                $even = !$even;
            }
        }
        $addClass = $showDefaultRates ? '' : 'ldgxPropertyRatesMergedNoDefault';
        $html = <<<EOT
<div class="ldgxPropertyRates ldgxPropertyRatesMerged $addClass">
    <table class="merged_rates_table">
        <thead>
            <tr>
                <th class="lodgix_left lodgix_dates merged_rates_table_green">$label</th>
                $thWeekday
                $thWeekly
                $thMonthly
            </tr>
        </thead>
        <tbody>
            $rows
        </tbody>
    </table>
</div>
EOT;
        return $html;
    }

    protected function mergedRatesTH($label)
    {
        $label = LodgixTranslate::translate($label);
        $html = <<<EOT
<th class="lodgix_centered merged_rates_table_green">$label</th>
EOT;
        return $html;
    }

    protected function mergedRatesRow($rate, $even, $isDaily, $isWeekly, $isMonthly)
    {
        $currency = $this->property->currency_symbol;
        $label = LodgixTranslate::translate($rate->name);
        $class = $even ? 'even' : 'odd';
        $from = strftime($this->dateFormat, strtotime($rate->from_date));
        $to = strftime($this->dateFormat, strtotime($rate->to_date));
        $minStay = $rate->min_stay > 1 ? '<br>' . $rate->min_stay . ' ' . LodgixTranslate::translate('nt min') : '';
        if ($isDaily) {
            $valueWeekday = $rate->nightly ? $currency . $rate->nightly : '-';
            $valueWeekend = $rate->weekend_nightly ? $currency . $rate->weekend_nightly : $valueWeekday;
            $tdWeekday = $this->mergedRatesTD($valueWeekday . ' / ' . $valueWeekend);
        } else {
            $tdWeekday = '';
        }
        $tdWeekly = $isWeekly ? $this->mergedRatesTD($rate->weekly ? $currency . $rate->weekly : '-') : '';
        $tdMonthly = $isMonthly ? $this->mergedRatesTD($rate->monthly ? $currency . $rate->monthly : '-') : '';
        $html = <<<EOT
        <tr class="merged_rates_table-$class">
            <td class="lodgix_left lodgix_dates">
                <b>$label</b><br>
                <span style="white-space:nowrap;font:inherit">$from -</span>
                <span style="white-space:nowrap;font:inherit">$to</span>
                $minStay
            </td>
            $tdWeekday
            $tdWeekly
            $tdMonthly
        </tr>
EOT;
        return $html;
    }

    protected function mergedRatesTD($value)
    {
        $html = <<<EOT
<td class="lodgix_centered">$value</td>
EOT;
        return $html;
    }

    protected function simpleRates()
    {
        $html = '';
        if ($this->displayRatesDaily) {
            $daily = $this->simpleRate($this->ratesDaily, 'ldgxPropertySimpleRateDaily', 'Daily Rate', 'per night');
        } else {
            $daily = '';
        }
        if ($this->displayRatesWeekly) {
            $weekly = $this->simpleRate($this->ratesWeekly, 'ldgxPropertySimpleRateWeekly', 'Weekly Rate', 'per week');
        } else {
            $weekly = '';
        }
        if ($this->displayRatesMonthly) {
            $monthly = $this->simpleRate($this->ratesMonthly, 'ldgxPropertySimpleRateMonthly', 'Monthly Rate', 'per month');
        } else {
            $monthly = '';
        }
        if ($this->displayMinStay) {
            $minStay = '<div class="ldgxPropertySimpleRateMinStay">' . $this->minStay[0] . ' ' . LodgixTranslate::translate('nt min') . '</div>';
        } else {
            $minStay = '';
        }
        if ($daily || $weekly || $monthly || $minStay) {
            $html .= <<<EOT
<div class="ldgxPropertyRates ldgxPropertyRatesSimple">
    $daily
    $weekly
    $monthly
    $minStay
</div>
EOT;
        }
        return $html;
    }

    protected function simpleRate($rates, $class, $labelEn, $perEn)
    {
        $html = '';
        if ($rates) {
            $low = $rates[0];
            $high = $rates[1];
            $high = $low != $high ? "- $high" : '';
            $label = LodgixTranslate::translate($labelEn);
            $per = LodgixTranslate::translate($perEn);
            $html .= <<<EOT
<div class="$class">
    $label: $low $high $per
</div>
EOT;
        }
        return $html;
    }

    protected function taxes()
    {
        $html = '';
        if ($this->taxes) {
            $label = LodgixTranslate::translate('Taxes');
            $currency = $this->property->currency_symbol;
            $html .= <<<EOT
<div class="ldgxPropertyPolicies ldgxPropertyPoliciesTaxes">
    <div class="ldgxPropertyPoliciesTitle">$label</div>
    <div class="ldgxPropertyPoliciesText">
EOT;
            foreach ($this->taxes as $tax) {
                $label = LodgixTranslate::translate($tax->title);
                if ($tax->is_flat == 1) {
                    $value = $currency . number_format($tax->value, 2);
                    if ($tax->frequency == 'ONETIME') {
                        $frequency = '- ' . LodgixTranslate::translate('One Time');
                    } else {
                        $frequency = '- ' . LodgixTranslate::translate('Daily');
                    }
                } else {
                    $value = number_format($tax->value, 2) . '%';
                    $frequency = '';
                }
                $html .= <<<EOT
                    $label - $value $frequency<br>
EOT;
            }
            $html .= <<<EOT
    </div>
</div>
EOT;
        }
        return $html;
    }

    protected function fees()
    {
        $html = '';
        if ($this->fees) {
            $label = LodgixTranslate::translate('Fees');
            $currency = $this->property->currency_symbol;
            $html .= <<<EOT
<div class="ldgxPropertyPolicies ldgxPropertyPoliciesFees">
    <div class="ldgxPropertyPoliciesTitle">$label</div>
    <div class="ldgxPropertyPoliciesText">
EOT;
            foreach ($this->fees as $fee) {
                $label = LodgixTranslate::translate($fee->title);
                if ($fee->is_flat == 1) {
                    $value = $currency . number_format($fee->value, 2);
                } else {
                    $value = number_format($fee->value, 2) . '%';
                }
                $taxExempt = $fee->tax_exempt == 1 ? '- ' . LodgixTranslate::translate('Tax Exempt') : '';
                $html .= <<<EOT
                    $label - $value $taxExempt<br>
EOT;
            }
            $html .= <<<EOT
    </div>
</div>
EOT;
        }
        return $html;
    }

    protected function deposits()
    {
        $html = '';
        if ($this->deposits) {
            $label = LodgixTranslate::translate('Deposits');
            $currency = $this->property->currency_symbol;
            $html .= <<<EOT
<div class="ldgxPropertyPolicies ldgxPropertyPoliciesDeposits">
    <div class="ldgxPropertyPoliciesTitle">$label</div>
    <div class="ldgxPropertyPoliciesText">
EOT;
            foreach ($this->deposits as $deposit) {
                $label = LodgixTranslate::translate($deposit->title);
                $value = number_format($deposit->value, 2);
                $html .= <<<EOT
                    $label - ${currency}$value<br>
EOT;
            }
            $html .= <<<EOT
    </div>
</div>
EOT;
        }
        return $html;
    }

    protected function policies()
    {
        $html = '';
        if ($this->policies) {
            $date = strftime($this->dateFormat, strtotime($this->property->date_modified));
            $lastUpdated = LodgixTranslate::translate('Last updated');
            foreach ($this->policies as $policy) {
                if ($policy->cancellation_policy) {
                    $label = LodgixTranslate::translate('Cancellation Policy');
                    $value = nl2br($policy->cancellation_policy);
                    $html .= <<<EOT
<div class="ldgxPropertyPolicies ldgxPropertyPoliciesCancellationPolicy">
    <div class="ldgxPropertyPoliciesTitle">$label</div>
    <div class="ldgxPropertyPoliciesText">
        $value
    </div>
    <div class="ldgxPropertyPoliciesUpdated">$lastUpdated <span>$date</span></div>
</div>
EOT;
                }
                if ($policy->deposit_policy) {
                    $label = LodgixTranslate::translate('Deposit Policy');
                    $value = nl2br($policy->deposit_policy);
                    $html .= <<<EOT
<div class="ldgxPropertyPolicies ldgxPropertyPoliciesDepositPolicy">
    <div class="ldgxPropertyPoliciesTitle">$label</div>
    <div class="ldgxPropertyPoliciesText">
        $value
    </div>
    <div class="ldgxPropertyPoliciesUpdated">$lastUpdated <span>$date</span></div>
</div>
EOT;
                }
            }
        }
        return $html;
    }

    function calendar()
    {
        $label = LodgixTranslate::translate('Availability &amp; Booking Calendar');
        $ownerId = $this->property->owner_id;
        $propertyId = $this->property->id;
        $html = <<<EOT
<div id="booking" class="ldgxPropertyBlock ldgxPropertyBlockCalendar">
    <h2>$label</h2>
    <script src="https://www.lodgix.com/static/booking-calendar/single/iframe/lodgix.min.js"></script>
    <script>new LodgixCalendarSingle(new LodgixOrigin('https://www.lodgix.com','/booking-calendar/websites/$ownerId/properties/$propertyId/'))</script>
</div>
EOT;
        return $html;
    }

    function map($isInit = true)
    {
        $label = LodgixTranslate::translate('Property Location');
        $lat = $this->property->latitude;
        $lon = $this->property->longitude;
        $zoom = $this->mapZoom;
        $html = <<<EOT
<div id="map" class="ldgxPropertyBlock ldgxPropertyBlockMap">
    <h2>$label</h2>
    <div id="lodgixMapCanvas" style="height:500px"></div>
</div>
<script>
    var lodgixMap = new LodgixMap({
        id: 'lodgixMapCanvas',
        lat: $lat,
        lon: $lon,
        zoom: $zoom
    });
</script>
EOT;
        if ($isInit) {
            $html .= <<<EOT
<script>lodgixMap.initOnDocumentReady()</script>
EOT;
        }
        return $html;
    }

    protected function lodgixLink()
    {
        global $wpdb;
        $table = $wpdb->prefix . LodgixConst::TABLE_LINK_ROTATORS;
        $rotators = $wpdb->get_results("SELECT url,title FROM $table ORDER BY RAND() LIMIT 1");
        if ($rotators) {
            foreach ($rotators as $rotator) {
                return '<a target="_blank" href="' . $rotator->url . '">' . $rotator->title . '</a>';
            }
        }
        return '<a href="https://www.lodgix.com">Vacation Rental Software</a>';
    }

}
