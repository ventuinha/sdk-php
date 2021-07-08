<?php

namespace StarkBank\UtilityPayment;
use StarkBank\Utils\Resource;
use StarkBank\Utils\Checks;
use StarkBank\Utils\Rest;
use StarkBank\Utils\API;
use StarkBank\Utils\StarkBankDate;
use StarkBank\UtilityPayment;


class Log extends Resource
{
    /**
    # UtilityPayment\Log object

    Every time a UtilityPayment entity is modified, a corresponding UtilityPayment\Log
    is generated for the entity. This log is never generated by the user, but it can
    be retrieved to check additional information on the UtilityPayment.

    ## Attributes:
        - id [string]: unique id returned when the log is created. ex: "5656565656565656"
        - payment [UtilityPayment]: UtilityPayment entity to which the log refers to.
        - errors [array of strings]: array of errors linked to this UtilityPayment event.
        - type [string]: type of the UtilityPayment event which triggered the log creation. ex: "processing" or "success"
        - created [DateTime]: creation datetime for the log.
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this->type = Checks::checkParam($params, "type");
        $this->errors = Checks::checkParam($params, "errors");
        $this->payment = Checks::checkParam($params, "payment");

        Checks::checkParams($params);
    }

    /**
    # Retrieve a specific Log

    Receive a single Log object previously created by the Stark Bank API by passing its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object]: Organization or Project object. Not necessary if StarkBank\Settings::setUser() was used before function call

    ## Return:
        - Log object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, Log::resource(), $id);
    }

    /**
    # Retrieve ogs

    Receive a enumerator of Log objects previously created in the Stark Bank API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [DateTime or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [DateTime or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - types [array of strings, default null]: filter retrieved objects by event types. ex: "paid" or "registered"
        - paymentIds [array of strings, default null]: array of UtilityPayment ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object]: Organization or Project object. Not necessary if StarkBank\Settings::setUser() was used before function call

    ## Return:
        - enumerator of Log objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkBankDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkBankDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, Log::resource(), $options);
    }

    /**
    # Retrieve paged UtilityPayment\Logs

    Receive a list of up to 100 UtilityPayment\Log objects previously created in the Stark Bank API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
    - cursor [string, default null]: cursor returned on the previous page function call
    - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
    - after [DateTime or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
    - before [DateTime or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
    - types [array of strings, default null]: filter retrieved objects by event types. ex: "paid" or "registered"
    - paymentIds [array of strings, default null]: array of UtilityPayment ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
    - user [Organization/Project object]: Organization or Project object. Not necessary if StarkBank\Settings::setUser() was used before function call
    
    ## Return:
    - list of UtilityPayment\Log objects with updated attributes
    - cursor to retrieve the next page of UtilityPayment\Log objects
     */
    public static function page($options = [], $user = null)
    {
        return Rest::getPage($user, Log::resource(), $options);
    }

    private static function resource()
    {
        $paymentLog = function ($array) {
            $payment = function ($array) {
                return new UtilityPayment($array);
            };
            $array["payment"] = API::fromApiJson($payment, $array["payment"]);
            return new Log($array);
        };
        return [
            "name" => "UtilityPaymentLog",
            "maker" => $paymentLog,
        ];
    }
}
