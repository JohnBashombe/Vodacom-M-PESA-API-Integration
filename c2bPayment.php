<?php
include('./index.php');

class  C2BPaymentServices extends VodacomServices
{

    private static $country = 'TZN';
    private static $currency = 'TZS';
    private static $service_provider_code = '000000';

    private static $amount = 0.0;
    private static $input_CustomerMSISDN = 0;
    private static $input_PurchasedItemsDesc = '';
    private static $input_TransactionReference = '';
    private static $input_ThirdPartyConversationID = '';

    private static $c2b_endpoint = '/sandbox/ipg/v2/vodacomTZN/c2bPayment/singleStage/';

    public function __construct()
    {
        // Get Body Values
        $Body = json_decode(file_get_contents('php://input'));
        self::$amount = $Body->input_Amount;
        self::$country = $Body->input_Country;
        self::$currency = $Body->input_Currency;
        self::$input_CustomerMSISDN = $Body->input_CustomerMSISDN;
        self::$service_provider_code = $Body->input_ServiceProviderCode;
        self::$input_PurchasedItemsDesc = $Body->input_PurchasedItemsDesc;
        self::$input_TransactionReference = $Body->input_TransactionReference;
        self::$input_ThirdPartyConversationID = $Body->input_ThirdPartyConversationID;
    }

    public function  C2BPayment($sessionID)
    {
        if ($sessionID) {
            // The above call issued a sessionID which can be used as the API key in calls that needs the sessionID
            $context = new APIContext();
            $context->set_api_key($sessionID);
            $context->set_public_key(VodacomServices::$public_key);
            $context->set_ssl(true);
            $context->set_method_type(APIMethodType::POST);
            $context->set_address(VodacomServices::$base_url);
            $context->set_port(VodacomServices::$port);
            $context->set_path(self::$c2b_endpoint);

            $context->add_header('Origin', '*');

            $context->add_parameter('input_Amount', self::$amount);
            $context->add_parameter('input_Country', self::$country);
            $context->add_parameter('input_Currency', self::$currency);
            $context->add_parameter('input_CustomerMSISDN', self::$input_CustomerMSISDN);
            $context->add_parameter('input_ServiceProviderCode', self::$service_provider_code);
            $context->add_parameter('input_ThirdPartyConversationID', self::$input_ThirdPartyConversationID);
            $context->add_parameter('input_TransactionReference', self::$input_TransactionReference);
            $context->add_parameter('input_PurchasedItemsDesc', self::$input_PurchasedItemsDesc);

            $request = new APIRequest($context);

            $response = null;

            try {
                $response = $request->execute();
            } catch (exception $e) {
                // echo 'Call failed: ' . $e->getMessage() . '<br>';
                echo json_encode(null);
            }

            if ($response->get_body() == null) {
                // throw new Exception('API call failed to get result. Please check.');
                echo json_encode(null);
            }
            $result = null;
            $result = ([
                'output_ResponseCode' => json_decode($response->get_body())->output_ResponseCode,
                'output_ResponseDesc' => json_decode($response->get_body())->output_ResponseDesc,
                'output_TransactionID' => json_decode($response->get_body())->output_TransactionID,
                'output_ConversationID' => json_decode($response->get_body())->output_ConversationID,
                'output_ThirdPartyConversationID' => json_decode($response->get_body())->output_ThirdPartyConversationID,
            ]);

            // return response
            if ($result) echo json_encode($result);
            else echo json_encode(null);
        } else {
            echo json_encode(null);
        }
    }
}


$voda = new C2BPaymentServices();

// Generating Token ID
$tokenID = json_decode(VodacomServices::generateSessionKey()->get_body());
// C2B Payment
$voda->C2BPayment($tokenID->output_SessionID);
