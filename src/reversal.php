<?php
include('./index.php');

class  ReversalServices extends VodacomServices
{

    private static $country = 'TZN';
    private static $service_provider_code = '000000';

    private static $amount = 0.0;
    private static $input_ThirdPartyConversationID = '';
    private static $input_TransactionID = '';

    private static $reversal = '/sandbox/ipg/v2/vodacomTZN/reversal/';

    public function __construct()
    {
        // Get Body Values
        $Body = json_decode(file_get_contents('php://input'));
        self::$amount = $Body->input_Amount;
        self::$country = $Body->input_Country;
        self::$service_provider_code = $Body->input_ServiceProviderCode;
        self::$input_ThirdPartyConversationID = $Body->input_ThirdPartyConversationID;
        self::$input_TransactionID = $Body->input_TransactionID;
    }

    public function Reversal($sessionID)
    {
        if ($sessionID) {
            // The above call issued a sessionID which can be used as the API key in calls that needs the sessionID
            $context = new APIContext();
            $context->set_api_key($sessionID);
            $context->set_public_key(VodacomServices::$public_key);
            $context->set_ssl(true);
            $context->set_method_type(APIMethodType::PUT);
            $context->set_address(VodacomServices::$base_url);
            $context->set_port(VodacomServices::$port);
            $context->set_path(self::$reversal);

            $context->add_header('Origin', '*');

            $context->add_parameter('input_ReversalAmount', self::$amount);
            $context->add_parameter('input_Country', self::$country);
            $context->add_parameter('input_ServiceProviderCode', self::$service_provider_code);
            $context->add_parameter('input_ThirdPartyConversationID', self::$input_ThirdPartyConversationID);
            $context->add_parameter('input_TransactionID', self::$input_TransactionID); // transaction ID to reverse

            $request = new APIRequest($context);

            $response = null;

            try {
                $response = $request->execute();
            } catch (exception $e) {
                // echo 'Call failed: ' . $e->getMessage() . '<br>';
                echo json_encode(null);
            }

            if ($response === null || $response->get_body() == null) {
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
            // return result
            if ($result) echo json_encode($result);
            else echo json_encode(null);
        } else {
            echo json_encode(null);
        }
    }
}

$voda = new ReversalServices();
// Get Session ID
$tokenID = json_decode(VodacomServices::generateSessionKey()->get_body());
//Send Request
$voda->Reversal($tokenID->output_SessionID);
// $voda->TransactionStatus($tokenID->output_SessionID);
