<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use App\Models\Item;
use App\Services\StripeService;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    public function handle(Request $request, StripeService $stripe)
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = $stripe->verifyWebhookSignature($payload, $sigHeader);
        } catch(SignatureVerificationException $e) {
            Log::error('Stripe signature verification failed.', ['error' => $e->getMessage()]);
            return response('Invalid signature', 400);
        }

        // イベントタイプごとの処理
        switch($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                Log::info('Checkout session completed!', ['session_id' => $session->id]);

                $intent = $stripe->retrievePaymentIntent($session->payment_intent);

                Log::info('🧪 intent metadata', ['metadata' => $intent->metadata]);

                $userId = $intent->metadata->user_id ?? null;
                $itemId = $intent->metadata->item_id ?? null;

                Log::info('🧪 user_id', ['user_id' => $userId]);
                Log::info('🧪 item_id', ['item_id' => $itemId]);


                $paymentMethod = $session->payment_method_types[0] ?? 'unknown';

                if($userId && $itemId) {
                    $user = User::find($userId);
                    $item = Item::find($itemId);

                    if ($user && $item) {
                        $purchase = $user->purchaseItem($item, $paymentMethod);
                        Log::info('purchaseItem 実行完了', ['purchase_id' => $purchase->id]);
                    } else {
                        Log::warning('⚠️ ユーザーまたは商品が見つかりません', [
                            'user_id' => $userId,
                            'item_id' => $itemId,
                        ]);
                    }
                } else {
                    Log::warning('⚠️ metadataに必要な情報が不足しています', [
                        'metadata' => $session->metadata ?? [],
                    ]);
                }

                break;

            case 'payment_intent_succeeded':
                $intent = $event->data->object;
                Log::info('Payment succeeded!', ['payment_intent' => $intent->id]);

                break;

            default:
                Log::info('Unhandled event type', ['type' => $event->type]);

                break;
            }

            return response('webhook handled', Response::HTTP_OK);
        }
    }

