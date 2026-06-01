<?php

use App\Helpers\MetaConversionsHelper;

if (!function_exists('metaConversions')) {
    /**
     * Retorna uma instância do MetaConversionsHelper
     */
    function metaConversions()
    {
        return new MetaConversionsHelper();
    }
}

if (!function_exists('trackMetaEvent')) {
    /**
     * Função conveniente para rastrear eventos da Meta
     */
    function trackMetaEvent($eventName, $eventData = [], $userData = [])
    {
        try {
            return metaConversions()->sendEvent($eventName, $eventData, $userData);
        } catch (Exception $e) {
            error_log("Meta Conversions API Error: " . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('trackMetaPageView')) {
    /**
     * Rastreia Page View na Meta
     */
    function trackMetaPageView($pageUrl = null)
    {
        try {
            return metaConversions()->trackPageView($pageUrl);
        } catch (Exception $e) {
            error_log("Meta Conversions API PageView Error: " . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('trackMetaLead')) {
    /**
     * Rastreia Lead na Meta
     * @param string|null $clientEventId Event ID do client-side para deduplicação
     */
    function trackMetaLead($email = null, $phone = null, $firstName = null, $lastName = null, $customData = [], $clientEventId = null)
    {
        try {
            return metaConversions()->trackLead($email, $phone, $firstName, $lastName, $customData, $clientEventId);
        } catch (Exception $e) {
            error_log("Meta Conversions API Lead Error: " . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('trackMetaCompleteRegistration')) {
    /**
     * Rastreia Complete Registration na Meta
     */
    function trackMetaCompleteRegistration($email = null, $customData = [], $clientEventId = null, $phone = null, $firstName = null, $lastName = null)
    {
        try {
            return metaConversions()->trackCompleteRegistration($email, $customData, $clientEventId, $phone, $firstName, $lastName);
        } catch (Exception $e) {
            error_log("Meta Conversions API CompleteRegistration Error: " . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('trackMetaContact')) {
    /**
     * Rastreia Contact na Meta
     */
    function trackMetaContact($email = null, $customData = [], $clientEventId = null, $phone = null, $firstName = null, $lastName = null)
    {
        try {
            return metaConversions()->trackContact($email, $customData, $clientEventId, $phone, $firstName, $lastName);
        } catch (Exception $e) {
            error_log("Meta Conversions API Contact Error: " . $e->getMessage());
            return false;
        }
    }
}