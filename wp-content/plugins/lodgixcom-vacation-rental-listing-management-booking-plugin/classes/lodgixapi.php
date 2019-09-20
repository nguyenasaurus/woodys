<?php

class LodgixApi
{

    function __construct($ownerId, $apiKey)
    {
        $this->ownerId = $ownerId;
        $this->apiKey = $apiKey;
        $this->baseUrl = LodgixConst::BASE_URL;
        $this->dbVersion = LodgixConst::DB_VERSION;
        $this->availableProperties = '';
        $this->availablePropertiesAfterRules = '';
        $this->arrival = null;
        $this->nights = null;
    }

    /**
     * @return SimpleXMLElement
     * @throws LogidxHTTPRequestException
     */
    function getOwner()
    {
        $url = "$this->baseUrl/api/xml/owners/get?Token=$this->apiKey&IncludeLanguages=Yes&IncludeRotators=Yes&IncludeAmenities=Yes&OwnerID=$this->ownerId&Version=$this->dbVersion";
        return $this->fetchData($url);
    }

    /**
     * @return SimpleXMLElement
     * @throws LogidxHTTPRequestException
     */
    function getProperties()
    {
        $url = "$this->baseUrl/api/xml/properties/get?Token=$this->apiKey&IncludeAmenities=Yes&IncludePhotos=Yes&IncludeConditions=Yes&IncludeLanguages=Yes&IncludeTaxes=Yes&IncludeReviews=Yes&IncludeMergedRates=Yes&OwnerID=$this->ownerId&Version=$this->dbVersion";
        return $this->fetchData($url);
    }

    /**
     * @param null $arrival
     * @param null $nights
     * @return string
     * @throws LogidxHTTPRequestException
     */
    function getAvailableProperties($arrival = null, $nights = null)
    {
        if ($arrival !== null && $nights !== null) {
            if (strtotime($arrival) === false || !is_numeric($nights)) {
                return 'ALL';
            }
            $this->fetchAvailableProperties($arrival, $nights);
        }
        return $this->availableProperties;
    }

    /**
     * @param null $arrival
     * @param null $nights
     * @return string
     * @throws LogidxHTTPRequestException
     */
    function getAvailablePropertiesAfterRules($arrival = null, $nights = null)
    {
        if ($arrival !== null && $nights !== null) {
            if (strtotime($arrival) === false || !is_numeric($nights)) {
                return 'ALL';
            }
            $this->fetchAvailableProperties($arrival, $nights);
        }
        return $this->availablePropertiesAfterRules;
    }

    /**
     * @param $arrival
     * @param $nights
     * @throws LogidxHTTPRequestException
     */
    private function fetchAvailableProperties($arrival, $nights)
    {
        if ($this->arrival !== $arrival || $this->nights !== $nights) {
            $departure = date('Y-m-d', strtotime("$arrival + $nights days"));
            $url = "$this->baseUrl/system/api-lite/xml?Action=GetAvailableProperties&PropertyOwnerID=$this->ownerId&FromDate=$arrival&ToDate=$departure&Version=$this->dbVersion";
            $x = $this->fetchData($url);
            $this->availableProperties = strval($x->Results->AvailableProperties);
            $this->availablePropertiesAfterRules = strval($x->Results->AvailablePropertiesAfterRules);
            $this->arrival = $arrival;
            $this->nights = $nights;
        }
    }

    /**
     * @param $url
     * @return SimpleXMLElement
     * @throws LogidxHTTPRequestException
     */
    protected function fetchData($url)
    {
        $xml = (new LogidxHTTPRequest($url))->downloadToString();
        return simplexml_load_string($xml);
    }

}
