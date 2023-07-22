<?php

namespace App\Traits;

use Anand\LaravelPaytmWallet\Facades\PaytmWallet;
use App\Library\SslCommerz\SslCommerzNotification;
use App\Models\Currency;
use App\Repositories\Admin\CurrencyRepository;
use Illuminate\Support\Str;
use Razorpay\Api\Api;
use Stripe\Charge;
use Stripe\Stripe;

trait PaymentTrait
{

    public function methodCheck($data, $orders = [], $user = null)
    {
        $currency = new CurrencyRepository();

        if (session()->has('currency')) {
            $user_currency = session()->get('currency');
        } else {
            $user_currency = settingHelper('default_currency');
        }

        $active_currency = $currency->get($user_currency);

        if ($data['payment_type'] == 'paypal') {
            return $this->paypal($data);
        } elseif ($data['payment_type'] == 'stripe') {
            return $this->stripe();
        } elseif ($data['payment_type'] == 'ssl_commerze') {
            return $this->sslResponseOutput($data);
        } elseif ($data['payment_type'] == 'paytm') {
            return $this->paytm($data);
        } elseif ($data['payment_type'] == 'razor_pay') {
            return $this->razorPay($data, $active_currency);
        } elseif ($data['payment_type'] == 'jazz_cash') {
            return $this->jazzCash();
        } elseif ($data['payment_type'] == 'mollie') {
            return $this->mollie($data);
        }elseif ($data['payment_type'] == 'paystack') {
            return $this->verifyPayStack($data);
        }elseif ($data['payment_type'] == 'flutter_wave') {
            return $this->verifyFW($data);
        }
    }

    protected function paypal($data): array
    {
        $result = [];

        if (array_key_exists('paymentSource', $data) && $data['paymentSource'] == 'card') {
            $payer = @$data['order']['payer'];
            if ($payer) {
                $result = [
                    'name' => $payer['name']['given_name'] . ' ' . $payer['name']['surname'],
                    'email' => $payer['email_address'],
                    'link' => $data['order']['links'][0]['href']
                ];
            }
        }

        return $result;
    }

    protected function stripe(): array
    {
        $payment_details = [];
        $payment = '';
        Stripe::setApiKey(settingHelper('stripe_secret'));

        $charge = Charge::all(['payment_intent' =>session()->get('payment_intent'),'limit' => 1]);

        if (count($charge->data) > 0)
        {
            $payment = $charge->data[0];

            $payment_details = [
                'payment_intent' => $payment->payment_intent,
                'customer' => $payment->customer,
                'email' => @$payment->billing_details->email,
                'name' => @$payment->billing_details->name,
                'receipt' => $payment->receipt_url,
            ];

            $payment_method = $payment->payment_method_details->type;

            if ($payment_method == 'card')
            {
                $payment_details['card'] = $payment->payment_method_details->card->brand;
                $payment_details['last4'] = $payment->payment_method_details->card->last4;
            }
            if ($payment_method == 'alipay')
            {
                $payment = $payment->payment_method_details;
                $payment_details['fingerprint'] = $payment->alipay->fingerprint;
                $payment_details['transaction_id'] = $payment->alipay->transaction_id;
            }
            if ($payment_method == 'klarna')
            {
                $payment = $payment->payment_method_details;
                $payment_details['payment_method_category'] = $payment->klarna->payment_method_category;
                $payment_details['preferred_locale'] = $payment->klarna->preferred_locale;
            }
            if ($payment_method == 'us_bank_account')
            {
                $payment = $payment->payment_method_details;

                $payment_details['account_holder_type'] = $payment->us_bank_account->account_holder_type;
                $payment_details['account_type'] = $payment->us_bank_account->account_type;
                $payment_details['bank_name'] = $payment->us_bank_account->bank_name;
                $payment_details['fingerprint'] = $payment->us_bank_account->fingerprint;
                $payment_details['last4'] = $payment->us_bank_account->last4;
                $payment_details['routing_number'] = $payment->us_bank_account->routing_number;
            }
            if ($payment_method == 'bancontact' || $payment_method == 'sofort')
            {
                $payment = $payment->payment_method_details;

                $type = $payment_method == 'bancontact' ? $payment->bancontact : $payment->sofort;

                $payment_details['bank_code'] = $type->bank_code;
                $payment_details['bank_name'] = $type->bank_name;
                $payment_details['country'] = property_exists($type,'country') ? $type->country : '';
                $payment_details['bic'] = $type->bic;
                $payment_details['generated_sepa_debit'] = $type->generated_sepa_debit;
                $payment_details['generated_sepa_debit_mandate'] = $type->generated_sepa_debit_mandate;
                $payment_details['iban_last4'] = $type->iban_last4;
                $payment_details['verified_name'] = $type->verified_name;
            }
            if ($payment_method == 'sepa_debit')
            {
                $payment = $payment->payment_method_details;
                $payment_details['bank_code'] = $payment->sepa_debit->bank_code;
                $payment_details['branch_code'] = $payment->sepa_debit->branch_code;
                $payment_details['country'] = $payment->sepa_debit->country;
                $payment_details['fingerprint'] = $payment->sepa_debit->fingerprint;
                $payment_details['last4'] = $payment->sepa_debit->last4;
                $payment_details['mandate'] = $payment->sepa_debit->mandate;
            }
            if ($payment_method == 'ideal')
            {
                $payment = $payment->payment_method_details;

                $payment_details['bank'] = $payment->ideal->bank;
                $payment_details['bic'] = $payment->ideal->bic;
                $payment_details['generated_sepa_debit'] = $payment->ideal->generated_sepa_debit;
                $payment_details['generated_sepa_debit_mandate'] = $payment->ideal->generated_sepa_debit_mandate;
                $payment_details['iban_last4'] = $payment->ideal->iban_last4;
                $payment_details['verified_name'] = $payment->ideal->verified_name;
            }
            if ($payment_method == 'p24' || $payment_method == 'eps')
            {
                $payment = $payment->payment_method_details;
                $type = $payment_method == 'p24' ? $payment->p24 : $payment->eps;

                $payment_details['bank'] = $type->bank;
                $payment_details['reference'] = property_exists($type,'reference') ? $type->reference : '';
                $payment_details['verified_name'] = $type->verified_name;
            }
        }
        session()->forget('payment_intent');

        return $payment_details;
    }

    public function razorPay($data, $active_currency): array
    {
        config(['services.razorpay.RAZORPAY_KEY' => settingHelper('razorpay_key')]);
        config(['services.razorpay.RAZORPAY_SECRET' => settingHelper('razorpay_secret')]);

        $api = new Api(config('services.razorpay.RAZORPAY_KEY'), config('services.razorpay.RAZORPAY_SECRET'));

        $payment = $api->payment->fetch($data['razorpay_payment_id']);

        if (count($data) && !empty($data['razorpay_payment_id'])) {
            try {
                $result = $api->payment->fetch($data['razorpay_payment_id'])->capture(array('amount' => $payment['amount']));
                $details = [];

                if ($result->method == 'card') {
                    $details = [
                        'name' => @$result->card->name,
                        'email' => $result->email,
                        'phone' => $result->contact,
                        'card' => $result->card->network,
                        'last4' => $result->card->last4,
                        'receipt' => 'https://dashboard.razorpay.com/app/payments/' . $result->id,
                    ];
                } elseif ($result->method == 'upi') {
                    $details = [
                        'vpa' => @$result->vpa,
                        'email' => $result->email,
                        'phone' => $result->contact,
                        'rrn' => $result->acquirer_data['rrn'],
                        'upi_transaction_id' => $result->acquirer_data['upi_transaction_id'],
                        'receipt' => 'https://dashboard.razorpay.com/app/payments/' . $result->id,

                    ];
                } elseif ($result->method == 'netbanking') {
                    $details = [
                        'bank' => @$result->bank,
                        'email' => $result->email,
                        'phone' => $result->contact,
                        'bank_transaction_id' => $result->acquirer_data['bank_transaction_id'],
                    ];
                } elseif ($result->method == 'wallet') {
                    $details = [
                        'wallet' => @$result->wallet,
                        'email' => $result->email,
                        'phone' => $result->contact,
                        'transaction_id' => $result->acquirer_data['transaction_id'],
                    ];
                }
                return $details;
            } catch (\Exception $e) {
                return [];
            }
        }
        return [];
    }

    public function sslResponseOutput($data): array
    {
        return [
            'card_type'     => $data['card_type'],
            'store_amount'  => $data['store_amount'],
            'card_no'       => $data['card_no'],
            'card_issuer'   => $data['card_issuer'],
            'card_brand'    => $data['card_brand'],
            'base_fair'     => $data['base_fair'],
        ];
    }

    public function sslCommerze($data, $orders, $user)
    {
        $bdt_currency = Currency::where('code','BDT')->first();

        if (!$bdt_currency)
        {
            return false;
        }

        if (session()->has('currency')) {
            $user_currency = session()->get('currency');
        } else {
            $user_currency = settingHelper('default_currency');
        }

        $currency = new CurrencyRepository();

        $active_currency = $currency->get($user_currency);

        $amount = count($orders) > 0 ? $orders->sum('total_payable') : $data['amount'];

        $rate = $amount/$active_currency->exchange_rate;


        if ($active_currency->code != $bdt_currency->code)
        {
            $post_data['total_amount'] = $rate * $bdt_currency->exchange_rate;
        }
        else{
            $post_data['total_amount'] = $amount;
        }

        $post_data['currency'] = "BDT";
        $post_data['tran_id'] = $data['trx_id'] ?? Str::random(); // tran_id must be unique

        # CUSTOMER INFORMATION
        $post_data['cus_name'] = 'cus_name';
        $post_data['cus_email'] = 'cus_email';
        $post_data['cus_add1'] = 'Customer Address';
        $post_data['cus_add2'] = "";
        $post_data['cus_city'] = "";
        $post_data['cus_state'] = "";
        $post_data['cus_postcode'] = "";
        $post_data['cus_country'] = "Bangladesh";
        $post_data['cus_phone'] = 'cus_phone';
        $post_data['cus_fax'] = "";

        # SHIPMENT INFORMATION
        $post_data['ship_name'] = "Store Test";
        $post_data['ship_add1'] = "Dhaka";
        $post_data['ship_add2'] = "Dhaka";
        $post_data['ship_city'] = "Dhaka";
        $post_data['ship_state'] = "Dhaka";
        $post_data['ship_postcode'] = "1000";
        $post_data['ship_phone'] = "";
        $post_data['ship_country'] = "Bangladesh";

        $post_data['shipping_method'] = "NO";
        $post_data['product_name'] = "Computer";
        $post_data['product_category'] = "Goods";
        $post_data['product_profile'] = "physical-goods";

        if (array_key_exists('type', $data) && $data['type'] == 'wallet') {
            $str = Str::random(21);
            if ($user) {
                $url = \Illuminate\Support\Facades\URL::temporarySignedRoute('recharge.wallet', now()->addMinutes(5), ['user_id' => $user->id, 'total' => $post_data['total_amount'], 'transaction_id' => $str, 'response' => 'yes']);
            } else {
                $url = \Illuminate\Support\Facades\URL::temporarySignedRoute('recharge.wallet', now()->addMinutes(5), ['total' => $post_data['total_amount'], 'transaction_id' => $str, 'response' => 'yes']);
            }

            config(['sslcommerz.success_url' => str_replace(url('/'), '', $url)]);
        } else {
            if ($user || array_key_exists('token',$data)) {
                if (array_key_exists('payment_mode', $data) && $data['payment_mode'] == 'api') {
                    config(['sslcommerz.success_url' => '/api/complete-order?payment_type=ssl_commerze&trx_id=' . $data['trx_id'].'&token='.$data['token']]);
                    config(['sslcommerz.cancel_url' => '/api/v100/payment?trx_id=' . $data['trx_id'].'&token='.$data['token']]);
                } else {
                    config(['sslcommerz.success_url' => '/user/complete-order?payment_type=ssl_commerze&trx_id=' . $data['trx_id']]);
                    config(['sslcommerz.cancel_url' => '/payment']);
                }
            } else {
                if (array_key_exists('payment_mode', $data) && $data['payment_mode'] == 'api') {
                    config(['sslcommerz.success_url' => '/api/complete-order?payment_type=ssl_commerze&guest=1&trx_id=' . $data['trx_id']]);
                    config(['sslcommerz.cancel_url' => '/api/v100/payment?trx_id=' . $data['trx_id']]);
                } else {
                    config(['sslcommerz.success_url' => '/user/complete-order?payment_type=ssl_commerze&guest=1&trx_id=' . $data['trx_id']]);
                    config(['sslcommerz.cancel_url' => '/payment']);
                }
            }
        }
        config(['sslcommerz.apiCredentials.store_id' => settingHelper('sslcommerz_id')]);
        config(['sslcommerz.apiCredentials.store_password' => settingHelper('sslcommerz_password')]);

        $sslc = new SslCommerzNotification();

        return $sslc->makePayment($post_data);
    }

    public function paytm($data)
    {
        $transaction = PaytmWallet::with('receive');
        $response = $transaction->response();
        $order_id = $transaction->getOrderId();

        if ($transaction->isSuccessful()) {
            return $data;
        } else if ($transaction->isFailed()) {
            return false;
        } else if ($transaction->isOpen()) {
            return false;
        }
        $transaction->getResponseMessage(); //Get Response Message If Available
        //get important parameters via public methods
        $transaction->getOrderId(); // Get order id
        $transaction->getTransactionId(); // Get transaction id

        return false;
    }

    protected function mollie($data)
    {
        $id = session()->get('id');
        if (!$id)
        {
            $id = cache()->get('id');
        }
        $mollie = new \Mollie\Api\MollieApiClient();
        $mollie->setApiKey(settingHelper('mollie_api_key'));
        $payment = $mollie->payments->get($id);

        $payment_details = [];

        $payment_details['status'] = $payment->status;
        $payment_details['method'] = $payment->method;

        if ($payment->method == 'creditcard')
        {
            $details = (array)$payment->details;

            $payment_details['cardNumber'] = $details['cardNumber'];
            $payment_details['cardHolder'] = $details['cardHolder'];
            $payment_details['cardAudience'] = $details['cardAudience'];
            $payment_details['cardLabel'] = $details['cardLabel'];
            $payment_details['cardCountryCode'] = $details['cardCountryCode'];
            $payment_details['cardSecurity'] = $details['cardSecurity'];
            $payment_details['feeRegion'] = $details['feeRegion'];
        }

        if ($payment->method == 'belfius' || $payment->method == 'kbc' || $payment->method == 'giropay' || $payment->method == 'eps' || $payment->method == 'sofort' || $payment->method == 'ideal')
        {
            $details = (array)$payment->details;

            $payment_details['consumerName'] = $details['consumerName'];
            $payment_details['consumerAccount'] = $details['consumerAccount'];
            $payment_details['consumerBic'] = $details['consumerBic'];
        }

        if ($payment->method == 'przelewy24')
        {
            $details = (array)$payment->details;

            $payment_details['billingEmail'] = $details['billingEmail'];
        }

        if ($payment->method == 'banktransfer')
        {
            $details = (array)$payment->details;

            $payment_details['bankName'] = $details['bankName'];
            $payment_details['bankAccount'] = $details['bankAccount'];
            $payment_details['transferReference'] = $details['transferReference'];
            $payment_details['consumerName'] = $details['consumerName'];
            $payment_details['consumerAccount'] = $details['consumerAccount'];
            $payment_details['consumerBic'] = $details['consumerBic'];
        }

        if ($payment->method == 'paypal')
        {
            $details = (array)$payment->details;

            $payment_details['consumerName'] = $details['consumerName'];
            $payment_details['consumerAccount'] = $details['consumerAccount'];
            $payment_details['paypalReference'] = $details['paypalReference'];
            $payment_details['paypalPayerId'] = $details['paypalPayerId'];
            $payment_details['sellerProtection'] = $details['sellerProtection'];
            $payment_details['paypalFee_amount'] = $details['paypalFee']->value;
            $payment_details['paypalFee_currency'] = $details['paypalFee']->currency;
        }

        return $payment_details;
    }

    public function jazzCashPayment(): array
    {

        /* $jc = new Jazzcash;
         $jc->reqType = 'Authorize';*/
        $DateTime = new \DateTime();
        $pp_TxnDateTime = $DateTime->format('YmdHis');

        $ExpiryDateTime = $DateTime;
        $ExpiryDateTime->modify('+' . 1 . ' hours');
        $pp_TxnExpiryDateTime = $ExpiryDateTime->format('YmdHis');

        $pp_TxnRefNo = 'T' . $pp_TxnDateTime;

        $pp_Amount = '50000';

        $post_data = [
            "pp_Version" => config('jazz_cash.VERSION'),
            "pp_TxnType" => "MIGS",
            "pp_Language" => config('jazz_cash.LANGUAGE'),
            "pp_MerchantID" => config('jazz_cash.MERCHANT_ID'),
            "pp_SubMerchantID" => "",
            "pp_Password" => config('jazz_cash.PASSWORD'),
            "pp_BankID" => "TBANK",
            "pp_ProductID" => "RETL",
            "pp_IsRegisteredCustomer" => "No",
            "pp_TokenizedCardNumber" => "",
            "pp_TxnRefNo" => $pp_TxnRefNo,
            "pp_Amount" => $pp_Amount,
            "pp_TxnCurrency" => config('jazz_cash.CURRENCY_CODE'),
            "pp_TxnDateTime" => $pp_TxnDateTime,
            "pp_BillReference" => "billRef",
            "pp_Description" => "Description of transaction",
            "pp_TxnExpiryDateTime" => $pp_TxnExpiryDateTime,
            "pp_ReturnURL" => url('/') . config('jazz_cash.RETURN_URL'),
            "pp_SecureHash" => "",
            "ppmpf_1" => "1",
            "ppmpf_2" => "2",
            "ppmpf_3" => "3",
            "ppmpf_4" => "4",
            "ppmpf_5" => "5",
            "pp_CustomerID" => "Test",
            "pp_CustomerEmail" => "test@gmail.com",
            "pp_MobileNumber" => "03123456789",
            "pp_CINIC" => "345678",
        ];

        $post_data['pp_SecureHash'] = $this->get_SecureHash($post_data);
        /*
                $jc->set_data($post_data);
                $jc->send();*/

        return $post_data;
    }

    private function get_SecureHash($data_array)
    {
        ksort($data_array);

        $str = '';
        foreach ($data_array as $key => $value) {
            if (!empty($value)) {
                $str = $str . '&' . $value;
            }
        }

        $str = config('jazz_cash.INTEGERITY_SALT') . $str;

        return hash_hmac('sha256', $str, config('jazz_cash.INTEGERITY_SALT'));
    }

    protected function verifyPayStack($request)
    {

        $payment_details = [];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/".$request['ref'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer ".settingHelper('paystack_secret_key'),
                "Cache-Control: no-cache",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return $payment_details;
        } else {
            $data = json_decode($response)->data;

            $payment_details['channel'] = $data->channel;
            $payment_details['status'] = $data->status;
            $payment_details['email'] = $data->customer->email;
            $payment_details['phone'] = array_key_exists('phone',$request) ? $request['phone'] : '';
            $payment_details['name'] = array_key_exists('phone',$request) ? $request['name'] : '';

            if ($data->channel == 'mobile_money')
            {
                $payment_details['bank'] = $data->authorization->bank;
                $payment_details['brand'] = $data->authorization->brand;
                $payment_details['account_name'] = $data->authorization->account_name;
                $payment_details['receiver_bank_account_number'] = $data->authorization->receiver_bank_account_number;
                $payment_details['receiver_bank'] = $data->authorization->receiver_bank;
            }

            if ($data->channel == 'card')
            {
                $payment_details['card_type'] = $data->authorization->card_type;
                $payment_details['last4'] = $data->authorization->last4;
                $payment_details['exp_month'] = $data->authorization->exp_month;
                $payment_details['exp_year'] = $data->authorization->exp_year;
            }

            return $payment_details;
        }
    }

    protected function verifyFW($request)
    {
        $payment_details = [];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/".$request['transaction_id']."/verify",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer ".settingHelper('flutterwave_secret_key'),
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return $payment_details;
        } else {
            $data = json_decode($response)->data;

            $payment_details['payment_type'] = $data->payment_type;
            $payment_details['status'] = $data->status;
            $payment_details['email'] = $request['email'];
            $payment_details['phone'] = $request['phone'];
            $payment_details['name'] = $request['name'];

            if ($data->payment_type == 'ussd')
            {
                $payment_details['auth_model'] = $data->auth_model;
                $payment_details['app_fee'] = $data->app_fee;
                $payment_details['merchant_fee'] = $data->merchant_fee;
                $payment_details['device_fingerprint'] = $data->device_fingerprint;
            }

            if ($data->payment_type == 'card')
            {
                $payment_details['first_6digits'] = $data->card->first_6digits;
                $payment_details['last4'] = $data->card->last_4digits;
                $payment_details['issuer'] = $data->card->issuer;
                $payment_details['country'] = $data->card->country;
                $payment_details['type'] = $data->card->type;
                $payment_details['expiry'] = $data->card->expiry;
            }

            if ($data->payment_type == 'bank_transfer')
            {
                $payment_details['app_fee'] = $data->app_fee;
                $payment_details['merchant_fee'] = $data->merchant_fee;
                $payment_details['account_id'] = $data->account_id;
                $payment_details['originatoraccountnumber'] = $data->meta->originatoraccountnumber;
                $payment_details['originatorname'] = $data->meta->originatorname;
                $payment_details['bankname'] = $data->meta->bankname;
                $payment_details['originatoramount'] = $data->meta->originatoramount;
            }

            return $payment_details;
        }
    }
}
