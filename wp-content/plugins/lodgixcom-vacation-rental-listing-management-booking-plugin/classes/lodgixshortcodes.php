<?php

class LodgixShortcodes
{

    private $db;
    private $config;
    private $currentLanguage;

    function __construct()
    {
        global $wpdb, $sitepress;
        $this->db = $wpdb;
        $this->config = new LodgixServiceConfig();
        $this->currentLanguage = $sitepress ? $sitepress->get_current_language() : 'en';
    }

    public function availability($args)
    {
        return (new LodgixAvailability($this->config))->page();
    }

    public function vacationRentals($args)
    {
        if ((int)$this->config->get("p_lodgix_vacation_rentals_page_$this->currentLanguage") > 0) {
            $inventory = new LodgixInventory($this->config, $this->currentLanguage);
            $inventoryItems = $inventory->inventoryItems();
            return "<div class=\"ldgxInventoryContainer\">$inventoryItems</div>";
        }
        return '';
    }

    public function searchRentals($args)
    {
        $sort = LodgixInventory::searchParam('lodgix-property-list-sort');
        $categoryId = LodgixInventory::searchParam('lodgix-custom-search-area');
        $bedrooms = LodgixInventory::searchParam('lodgix-custom-search-bedrooms');
        $priceFrom = LodgixInventory::searchParam('lodgix-custom-search-daily-price-from');
        $priceTo = LodgixInventory::searchParam('lodgix-custom-search-daily-price-to');
        $id = LodgixInventory::searchParam('lodgix-custom-search-id');
        $arrival = LodgixInventory::searchParam('lodgix-custom-search-arrival');
        $nights = LodgixInventory::searchParam('lodgix-custom-search-nights');
        $petFriendly = LodgixInventory::searchParam('lodgix-custom-search-pet-friendly');
        $petFriendly = ($petFriendly == 'on');
        $amenities = LodgixInventory::searchParamArr('lodgix-custom-search-amenity');
        $tags = LodgixInventory::searchParamArr('lodgix-custom-search-tag');

        $inventory = new LodgixInventory($this->config, $this->currentLanguage);
        $inventoryItems = $inventory->inventoryItems($sort, $categoryId, $bedrooms, $id, $arrival, $nights, $amenities, $priceFrom, $priceTo, $petFriendly, $tags);

        $query = LodgixInventory::searchQuery();
        $modHist = $query ? "<script>window.history.pushState('','','?$query')</script>" : '';

        return "<div class=\"ldgxInventoryContainer\">$modHist$inventoryItems</div>";
    }

    public function category($args)
    {
        $categoryId = $args[0];
        $inventory = new LodgixInventory($this->config, $this->currentLanguage);
        $inventoryItems = $inventory->inventoryItems('', $categoryId);
        return "<div class=\"ldgxInventoryContainer\">$inventoryItems</div>";
    }

    public function property($args)
    {
        $propertyId = $args[0];

        $tableProperties = $this->db->prefix . LodgixConst::TABLE_PROPERTIES;
        $tableLangProperties = $this->db->prefix . LodgixConst::TABLE_LANG_PROPERTIES;
        $tableLangPages = $this->db->prefix . LodgixConst::TABLE_LANG_PAGES;

        $html = '';
        $properties = $this->db->get_results("SELECT * FROM $tableProperties WHERE id=$propertyId");
        if ($properties) {
            $property = $properties[0];

            $book_dates = @esc_sql($_GET['bookdates']);
            if ($book_dates) {
                $property->booklink = (new LodgixServiceProperty($property))->bookLink($this->config->get('p_lodgix_owner_id'), $book_dates);
                $property->really_available = true;
            } else {
                $property->really_available = false;
            }

            if ($this->currentLanguage == 'en') {
                $permalink = get_permalink($property->post_id);
            } else {
                $translated_details = $this->db->get_results("SELECT * FROM $tableLangProperties WHERE id=$property->id AND language_code='$this->currentLanguage'");
                $translated_details = $translated_details[0];
                $property->description = $translated_details->description;
                $property->description_long = $translated_details->description_long;
                $property->details = $translated_details->details;
                $post_id = $this->db->get_var("select page_id from $tableLangPages WHERE property_id=$property->id AND language_code='$this->currentLanguage'");
                $permalink = get_permalink($post_id);
            }

            $contactUrlOption = $this->config->get("p_lodgix_contact_url_$this->currentLanguage");
            $emailUrl = $contactUrlOption ? $contactUrlOption : '';

            $mapZoomLevel = $this->config->get('p_lodgix_gmap_zoom_level');
            $mapZoom = $mapZoomLevel == 0 ? 13 : $mapZoomLevel;

            $lpd = new LodgixPropertyDetail(
                $property,
                $this->config->get('p_lodgix_date_format'),
                $this->config->get('p_lodgix_display_daily_rates'),
                $this->config->get('p_lodgix_display_weekly_rates'),
                $this->config->get('p_lodgix_display_monthly_rates'),
                $this->config->get('p_lodgix_display_property_min_stay'),
                $this->config->get('p_lodgix_rates_display') == 0,
                $this->config->get('p_lodgix_rates_display') == 1,
                $this->config->get('p_lodgix_rates_display') == 3,
                $permalink,
                $emailUrl,
                $this->config->get('p_lodgix_icon_set'),
                $this->config->get('p_lodgix_display_property_book_now_always'),
                $this->config->get('p_lodgix_display_beds'),
                $this->config->get('p_lodgix_display_city_registration'),
                $this->config->get('p_lodgix_image_size'),
                $mapZoom,
                $this->currentLanguage,
                false
            );
            if ($this->config->get('p_lodgix_single_page_design') == 1) {
                $html = $lpd->tabs(
                    $this->config->get('p_lodgix_single_page_tab_details_is_visible') ? $this->config->get('p_lodgix_single_page_tab_details') : '',
                    $this->config->get('p_lodgix_single_page_tab_calendar_is_visible') ? $this->config->get('p_lodgix_single_page_tab_calendar') : '',
                    $this->config->get('p_lodgix_single_page_tab_location_is_visible') ? $this->config->get('p_lodgix_single_page_tab_location') : '',
                    $this->config->get('p_lodgix_single_page_tab_amenities_is_visible') ? $this->config->get('p_lodgix_single_page_tab_amenities') : '',
                    $this->config->get('p_lodgix_single_page_tab_policies_is_visible') ? $this->config->get('p_lodgix_single_page_tab_policies') : '',
                    $this->config->get('p_lodgix_single_page_tab_reviews_is_visible') ? $this->config->get('p_lodgix_single_page_tab_reviews') : ''
                );
            } else {
                $html = $lpd->single(
                    $this->config->get('p_lodgix_single_page_tab_details_is_visible'),
                    $this->config->get('p_lodgix_single_page_tab_amenities_is_visible'),
                    $this->config->get('p_lodgix_single_page_tab_reviews_is_visible'),
                    $this->config->get('p_lodgix_single_page_tab_calendar_is_visible'),
                    $this->config->get('p_lodgix_single_page_tab_policies_is_visible'),
                    $this->config->get('p_lodgix_single_page_tab_location_is_visible')
                );
            }
        }
        return do_shortcode($html);
    }

}
