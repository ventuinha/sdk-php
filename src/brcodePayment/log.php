<?php

namespace StarkBank\BrcodePayment;
use StarkBank\Utils\Resource;
use StarkBank\Utils\Checks;
use StarkBank\Utils\Rest;
use StarkBank\Utils\API;
use StarkBank\BrcodePayment;


class Log extends Resource
{
    /**
    # BrcodePayment \Log object

    Every time a BrcodePayment entity is updated, a corresponding BrcodePayment\Log
    is generated for the entity. This log is never generated by the
    user, but it can be retrieved to check additional information
    on the BrcodePayment.

    ## Attributes:
        - id [string]: unique id returned when the log is created. ex: "5656565656565656"
        - payment [BrcodePayment]: BrcodePayment entity to which the log refers to.
        - errors [array of strings]: array of errors linked to this BrcodePayment event
        - type [string]: type of the BrcodePayment event which triggered the log creation. ex: "success" or "failed"
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
        - user [Project object]: Project object. Not necessary if StarkBank\User.setDefaut() was set before function call
    
    ## Return:
        - Log object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, Log::resource(), $id);
    }

    /**
    # Retrieve Logs

    Receive a enumerator of Log objects previously created in the Stark Bank API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [DateTime or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [DateTime or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - types [array of strings, default null]: filter for log event types. ex: "success" or "failed"
        - brcodePaymentIds [array of strings, default null]: array of BrcodePayment ids to filter logs. ex: ["5656565656565656", "4545454545454545"]
        - user [Project object, default null]: Project object. Not necessary if StarkBank\User.setDefaut() was set before function call

    ## Return:
        - enumerator of Log objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = Checks::checkDateTime(Checks::checkParam($options, "after"));
        $options["before"] = Checks::checkDateTime(Checks::checkParam($options, "before"));
        return Rest::getList($user, Log::resource(), $options);
    }

    private static function resource()
    {
        $paymentLog = function ($array) {
            $payment = function ($array) {
                return new BrcodePayment($array);
            };
            $array["payment"] = API::fromApiJson($payment, $array["payment"]);
            return new Log($array);
        };
        return [
            "name" => "BrcodePaymentLog",
            "maker" => $paymentLog,
        ];
    }
}
