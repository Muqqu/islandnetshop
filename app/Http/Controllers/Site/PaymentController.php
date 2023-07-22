<?php

namespace App\Http\Controllers\Site;

use Anand\LaravelPaytmWallet\Facades\PaytmWallet;
use App\Http\Controllers\Controller;
use App\Repositories\Admin\CurrencyRepository;
use App\Repositories\Interfaces\Admin\Addon\OfflineMethodInterface;
use App\Repositories\Interfaces\Admin\Addon\WalletInterface;
use App\Repositories\Interfaces\Admin\OrderInterface;
use App\Repositories\Interfaces\Site\CartInterface;
use App\Traits\HomePage;
use App\Traits\PaymentTrait;
use App\Utility\AppSettingUtility;
use Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Mollie\Api\Exceptions\ApiException;
use Stripe\Stripe;

class PaymentController extends Controller
{
    use HomePage,PaymentTrait;
    public $order;

    public function __construct(OrderInterface $order)
    {
        $this->order = $order;
    }

    public function stripeRedirect(Request $request,CartInterface $cart)
    {
        $data = $request->all();

        $code = '';

        $currency = new CurrencyRepository();

        if (session()->has('currency')) {
            $user_currency = session()->get('currency');
        } else {
            $user_currency = settingHelper('default_currency');
        }

        $active_currency = $currency->get($user_currency);

        Stripe::setApiKey(settingHelper('stripe_secret'));

        if (array_key_exists('code', $data) && $data['code'] && $data['code'] != 'undefined') {
            $orders = $this->order->orderByCodes($data['code']);
            $code = $data['code'];
        } else {
            $orders = $this->order->takePaymentOrder($data['trx_id']);
        }


        if (count($orders) == 0) {
            return back()->with(['error' => __('Oops.....Something Went Wrong')]);
        }

        $left = ['acss_debit','affirm','afterpay_clearpay','au_becs_debit','bacs_debit','boleto','fpx','grabpay','konbini','oxxo','paynow','promptpay','wechat_pay'];

        $us = ['card', 'alipay', 'us_bank_account','klarna'];

        $eur = ['bancontact','eps','ideal', 'p24','sepa_debit','sofort','card'];

        if (array_key_exists('code',$data) && !empty($data['code']))
        {
            $code = $data['code'];
        }

        $token = '';
        if (array_key_exists('token',$data))
        {
            $token = $data['token'];
        }

        if (authUser() || $token)
        {
            if ($request->payment_mode == 'api')
            {
                $url = url('api/complete-order?trx_id='.$data['trx_id'].'&code='.$code.'&payment_type=stripe&token='.$token);
            }
            else
            {
                $url = url('user/complete-order?trx_id='.$data['trx_id'].'&code='.$code.'&payment_type=stripe');
            }
        }
        else{
            if ($request->payment_mode == 'api')
            {
                $url = url('api/complete-order?guest=1&trx_id='.$data['trx_id'].'&payment_type=stripe');
            }
            else
            {
                $url = url('user/complete-order?guest=1&trx_id='.$data['trx_id'].'&payment_type=stripe');
            }
        }

        if ($active_currency && $active_currency->code == 'EUR')
        {
            $stripe_currency = strtolower($active_currency->code);
        }
        else{
            $stripe_currency = 'usd';
        }

        $amount = $orders->sum('total_payable') * 100;

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => $stripe_currency == 'eur' ? $eur : $us,
//            'payment_intent_data' => 'shipping',
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => $stripe_currency,
                        'product_data' => [
                            'name' => "Payment"
                        ],
                        'unit_amount' => $stripe_currency == 'eur' ? round($amount * $active_currency->exchange_rate,2) : round($amount,2),
                    ],

                    'quantity' => 1,
                ]
            ],
            'phone_number_collection' => [
                'enabled' => true,
            ],

            'mode' => 'payment',
            'success_url' => $url,
            'cancel_url' => $request->payment_mode == 'api' ? url("api/v100/payment?trx_id=".$data['trx_id'].'&token='.$token) : url('payment'),
        ]);

        session()->put('payment_intent',$session->payment_intent);

        return redirect($session->url);
    }

    public function stripeRecharge(Request $request)
    {
        $data = $request->all();
        $code = '';

        $currency = new CurrencyRepository();

        if (session()->has('currency')) {
            $user_currency = session()->get('currency');
        } else {
            $user_currency = settingHelper('default_currency');
        }

        $active_currency = $currency->get($user_currency);

        Stripe::setApiKey(settingHelper('stripe_secret'));


        $left = ['acss_debit','affirm','afterpay_clearpay','au_becs_debit','bacs_debit','boleto','fpx','grabpay','konbini','oxxo','paynow','promptpay','wechat_pay'];

        $us = ['card', 'alipay', 'us_bank_account','klarna'];

        $eur = ['bancontact','eps','ideal', 'p24','sepa_debit','sofort','card'];


        if ($active_currency && $active_currency->code == 'EUR')
        {
            $stripe_currency = strtolower($active_currency->code);
        }
        else{
            $stripe_currency = 'usd';
        }

        $amount = $request->amount * 100;
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => $stripe_currency == 'eur' ? $eur : $us,
//            'payment_intent_data' => 'shipping',
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => $stripe_currency,
                        'product_data' => [
                            'name' => "Recharge"
                        ],
                        'unit_amount' => $stripe_currency == 'eur' ? round($amount * $active_currency->exchange_rate,2) : round($amount,2),
                    ],

                    'quantity' => 1,
                ]
            ],
            'phone_number_collection' => [
                'enabled' => true,
            ],

            'mode' => 'payment',
            'success_url' =>url('stripe/recharge-success?amount='.$request->amount.'&payment_type=stripe'),
            'cancel_url' => url('payment'),
        ]);
        session()->put('payment_intent',$session->payment_intent);

        return redirect($session->url);
    }
    public function stripeRechargeSuccess(Request $request, WalletInterface $wallet){
        $data['payment_type'] = $request->payment_type;
        $data['amount'] = $request->amount;
        $source = 'wallet_recharge';
        $amount =$data['amount'];
        $payment_details = $this->methodCheck($data,[]);

        $currency = new CurrencyRepository();

        if (session()->has('currency')) {
            $user_currency = session()->get('currency');
        } else {
            $user_currency = settingHelper('default_currency');
        }

        $active_currency = $currency->get($user_currency);
        //remove shipping charges
        $userWallet['user_id']              = authId();
        $userWallet['order_id']             = null;
        $userWallet['amount']               = $amount / $active_currency->exchange_rate;
        $userWallet['source']               = $source;
        $userWallet['type']                 = 'income';
        $userWallet['status']               = 'pending';
        $userWallet['image']                = array_key_exists('file',$data) ? $data['file'] : [];
        $userWallet['transaction_id']       = array_key_exists('transaction_id',$data) ? $data['transaction_id'] : null;
        $userWallet['payment_method']       = $data['payment_type'];
        $userWallet['payment_details']      = $payment_details;
        $wallet->customerBalanceStore($userWallet, 'wallet_recharge');

        sendNotification(Sentinel::findById(1),"New Wallet Request Is Created.",'success',"wallet/recharge-requests",'');

        return redirect('my-wallet');
    }


    public function mollieRedirect(Request $request)
    {
        try {
            $data = $request->all();
            $code = '';
            if (array_key_exists('code', $data) && $data['code'] && $data['code'] != 'undefined') {
                $orders = $this->order->orderByCodes($data['code']);
                $code = $data['code'];
            } else {
                $orders = $this->order->takePaymentOrder($data['trx_id']);
            }
            if (count($orders) == 0) {
                return back()->with(['error' => __('Oops.....Something Went Wrong')]);
            }
            $trx_id = $orders->first()->trx_id;
            mollie()->setApiKey(settingHelper('mollie_api_key'));

            $euro_exchange_rate      = 1;
            $euro = AppSettingUtility::currencies()->where('code','EUR')->first();
            if($euro):
                $euro_exchange_rate     = $euro->exchange_rate;
            endif;

            $token = $request->token;

            if (authUser() || $token)
            {
                if ($request->payment_mode == 'api')
                {
                    $url = url("api/complete-order?trx_id=$trx_id&code=$code&token=$token&payment_type=mollie");
                }
                else{
                    $url = url('mollie/success?trx=' . $trx_id . '&code=' . $code);
                }
            }
            else{
                if ($request->payment_mode == 'api')
                {
                    $url = url("api/complete-order?trx_id=$trx_id&guest=1&payment_type=mollie");
                }
                else{
                    $url = url('mollie/success?trx=' . $trx_id . '&code=' . $code);
                }
            }


            $payment = mollie()->payments()->create([
                'amount' => [
                    'currency' => 'EUR', // Type of currency you want to send
                    'value' => number_format($orders->sum('total_payable') * $euro_exchange_rate, 2, '.', ''), // You must send the correct number of decimals, thus we enforce the use of strings
                ],
                'description' => __('payment_by') . ' ' . settingHelper('system_name'),
                'redirectUrl' => $url, // after the payment completion where you to redirect
                //            "webhookUrl" => route('webhooks.mollie'),
                "metadata" => [
                    "order_id" => $trx_id,
                ],
            ]);

            session()->put('id', $payment->id);// redirect customer to Mollie checkout page
            cache()->put('id', $payment->id);// redirect customer to Mollie checkout page

            return redirect($payment->getCheckoutUrl(), 303);
        } catch (ApiException $e) {
            return back()->with(['error' => __('Oops.....Something Went Wrong')]);
        }
    }

    public function mollieSuccess(Request $request, OfflineMethodInterface $offlineMethod)
    {
        DB::beginTransaction();
        $user = authUser();


        $data = [
            'trx_id' => $request->trx,
            'payment_type' => 'mollie',
        ];

        if (!$user) {
            $user = getWalkInCustomer();
            $data['guest'] = 1;
        }

        if ($request->code) {
            $data['code'] = $request->code;
        }

        try {
            $this->order->completeOrder($data, authUser(), $offlineMethod);
            $data = [
                'success' => __('Order Completed')
            ];

            DB::commit();

            if (request()->ajax()) {
                return response()->json($data);
            } else {
                if ($request->code) {
                    return redirect('get-invoice/' . $request->code);
                } else {
                    return redirect('invoice/' . session()->get('trx_id'));
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            session()->forget('trx_id');
            if (request()->ajax()) {
                return response()->json([
                    'error' => __('Oops.....Something Went Wrong')
                ]);
            } else {
                return redirect()->back()->with(['error' => __('oops...Something Went Wrong')]);
            }
        }
    }

    public function rechargeWithMollie(Request $request){
        try {
            $data = $request->all();
            $code = '';
            mollie()->setApiKey(settingHelper('mollie_api_key'));

            $euro_exchange_rate      = 1;
            $euro = AppSettingUtility::currencies()->where('code','EUR')->first();
            if($euro):
                $euro_exchange_rate     = $euro->exchange_rate;
            endif;

            $payment = mollie()->payments()->create([
                'amount' => [
                    'currency' => 'EUR', // Type of currency you want to send
                    'value' => number_format($data['amount'] * $euro_exchange_rate, 2, '.', ''), // You must send the correct number of decimals, thus we enforce the use of strings
                ],
                'description' => __('Recharge') . ' ' . settingHelper('system_name'),
                'redirectUrl' => url('mollie/recharge-success?amount='.$data['amount']), // after the payment completion where you to redirect
                //            "webhookUrl" => route('webhooks.mollie'),
            ]);
            $payment = mollie()->payments()->get($payment->id);
            session()->put('id', $payment->id);// redirect customer to Mollie checkout page
            return redirect($payment->getCheckoutUrl(), 303);
        } catch (ApiException $e) {
            return back()->with(['error' => __('Oops.....Something Went Wrong')]);
        }
    }

    public function mollieRechargeSuccess(Request $request, WalletInterface $wallet)
    {
        $data['payment_type'] = 'mollie';
        $data['amount'] = $request->amount;
        $source = 'wallet_recharge';
        $amount =$data['amount'];
        $payment_details = $this->methodCheck($data,[]);

        $currency = new CurrencyRepository();

        if (session()->has('currency')) {
            $user_currency = session()->get('currency');
        } else {
            $user_currency = settingHelper('default_currency');
        }

        $active_currency = $currency->get($user_currency);
        //remove shipping charges
        $userWallet['user_id']              = authId();
        $userWallet['order_id']             = null;
        $userWallet['amount']               = $amount / $active_currency->exchange_rate;
        $userWallet['source']               = $source;
        $userWallet['type']                 = 'income';
        $userWallet['status']               = 'pending';
        $userWallet['image']                = array_key_exists('file',$data) ? $data['file'] : [];
        $userWallet['transaction_id']       = array_key_exists('transaction_id',$data) ? $data['transaction_id'] : null;
        $userWallet['payment_method']       = $data['payment_type'];
        $userWallet['payment_details']      = $payment_details;
        $wallet->customerBalanceStore($userWallet, 'wallet_recharge');

        sendNotification(Sentinel::findById(1),"New Wallet Request Is Created.",'success',"wallet/recharge-requests",'');

        return redirect('my-wallet');
    }

    public function jazzCash(): bool
    {
        return true;
    }

    public function sslResponse(Request $request)
    {
        try {
            $url = '';
            $response = $this->order->sslResponse($request->all(), authUser());
            if ($response)
            {
                $data = json_decode($response);
                $url = $data->data;
            }
            if ($url)
            {
                return redirect($url);
            }
            else{
                return back()->with(['error' => __('Ops..!')]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'error' => __('Oops.....Something Went Wrong')
            ]);
        }
    }

    public function paytmPayment(CartInterface $cart, Request $request)
    {
        try {
            $user = authUser();
            if (!$request->amount) {
                $carts = $cart->all();

                $trx_id = $carts->first()->trx_id;
            } else {
                $trx_id = Str::random(21);
            }
            $orders = $this->order->invoiceByTrx($trx_id);
            $payment = PaytmWallet::with('receive');
            $payment->prepare([
                'order' => $orders->first()->id,
                'user' => $user->id,
                'mobile_number' => $user->phone ?? '00000001111',
                'email' => $user->email ?? Str::slug(settingHelper('system_name')) . '@yoori.com',
                'amount' => 200,
                'callback_url' => $request->amount ? url('user/recharge-wallet') : url('paytm/success?trx_id=' . $trx_id)
            ]);
            return $payment->receive();
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function payTmSuccess(Request $request, OfflineMethodInterface $offlineMethod)
    {
        DB::beginTransaction();

        $transaction = PaytmWallet::with('receive');

//        $response = $transaction->response();
//        dd($transaction);

        $user = authUser();

        $data = [
            'trx_id' => $request->trx,
            'payment_type' => 'paytm',
        ];

        if (!$user) {
            $user = getWalkInCustomer();
            $data['guest'] = 1;
        }

        if ($request->code) {
            $data['code'] = $request->code;
        }

        try {
            $this->order->completeOrder($data, authUser(), $offlineMethod);
            $data = [
                'success' => __('Order Completed')
            ];

            DB::commit();

            if (request()->ajax()) {
                return response()->json($data);
            } else {
                if ($request->code) {
                    return redirect('get-invoice/' . $request->code);
                } else {
                    return redirect('invoice/' . session()->get('trx_id'));
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            session()->forget('trx_id');
            if (request()->ajax()) {
                return response()->json([
                    'error' => __('Oops.....Something Went Wrong')
                ]);
            } else {
                return redirect()->back()->with(['error' => __('oops...Something Went Wrong')]);
            }
        }
    }

}
