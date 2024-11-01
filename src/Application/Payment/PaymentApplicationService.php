<?php declare(strict_types=1);

namespace Yoyaku\Application\Payment;

use Exception;
use Yoyaku\Application\Placeholder\PlaceholderApplicationService;
use Yoyaku\Domain\EventType\EventBookingPlaceholdersData;
use Yoyaku\Domain\Payment\GatewayType;
use Yoyaku\Domain\Payment\PurchaseResult;
use Yoyaku\Domain\Setting\SettingsService;
use Yoyaku\Domain\ValueObject\Number\Price;
use Yoyaku\Infrastructure\Services\Payment\PaymentGatewayFactory;

/**
 *
 */
class PaymentApplicationService
{
    private PlaceholderApplicationService $placeholder_as;
    private SettingsService $settings_ds;

    /**
     * @param PlaceholderApplicationService $placeholder_as
     */
    public function __construct(
        PlaceholderApplicationService $placeholder_as,
    )
    {
        $this->placeholder_as = $placeholder_as;
        $this->settings_ds = SettingsService::get_instance();
    }

    /**
     * 支払い処理を実行
     * @param GatewayType $gateway
     * @param string $confirmation_token_id
     * @param Price $amount
     * @return PurchaseResult
     * @throws Exception stripeの場合で設定が不十分だと発生する
     */
    public function process_payment($gateway, $confirmation_token_id, $amount)
    {
        $payment_gateway = PaymentGatewayFactory::create($gateway);
        return $payment_gateway->purchase(
            [
                'confirmation_token_id' => $confirmation_token_id,
                'amount' => $amount,
                'description' => '',
                'metadata' => [],
            ]
        );
    }

    /**
     * Gatewayに転送したいデータを取得する プレースホルダーを置換する
     * todo stripeのdescriptionやmetadataのカスタムができるようにする
     * @codeCoverageIgnore
     * @param EventBookingPlaceholdersData $placeholder_data
     * @param GatewayType $gateway
     * @return array [string, array] 0番目はdescription, 1番目はmetadata
     */
    private function get_information_for_gateway($placeholder_data, $gateway)
    {
        $description = '';
        $metadata = [];
        if ($gateway === GatewayType::ON_SITE) {
            return [$description, $metadata];

        } elseif ($gateway === GatewayType::STRIPE) {
            $description = $this->settings_ds->get("stripe_description_event");
            $metadata = json_decode($this->settings_ds->get("stripe_metadata_event"));
        }

        $placeholder_as = $this->placeholder_as;

        // プレースホルダーの記号が未使用の場合は即終了
        if (!str_contains($description, '%') && !str_contains($metadata, '%')) {
            return ['', []];
        }

        $placeholders_data = $placeholder_data->get_placeholders_data();

        $applied_description = '';
        if (str_contains($description, '%')) {
            $applied_description = $placeholder_as->apply_placeholders($description, $placeholders_data);
        }

        $applied_metadata = [];
        if (str_contains($metadata, '%')) {
            foreach ($metadata as $key => $value) {
                $applied_metadata[$key] = $placeholder_as->apply_placeholders($value, $placeholders_data);
            }
        }

        return [$applied_description, $applied_metadata];
    }
}
