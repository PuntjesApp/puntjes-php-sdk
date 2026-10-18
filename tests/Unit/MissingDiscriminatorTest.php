<?php

declare(strict_types=1);

namespace Puntjes\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use Puntjes\Generated\Models\CreateRedemptionData;
use Puntjes\Generated\Models\DiscountRedemptionData;
use Puntjes\Generated\Models\FreeProductRedemptionData;
use Puntjes\Generated\Models\VerifyRedemptionData;
use Puntjes\Tests\Support\RedemptionAnswer;
use Puntjes\Tests\Support\TestCase;

/**
 * A redemption's type_specific_data without its "type" key is read by its shape, in all three answers that carry it.
 */
final class MissingDiscriminatorTest extends TestCase
{
    /**
     * @return iterable<string, array{0: string}>
     */
    public static function wrappers(): iterable
    {
        yield 'redemption created' => ['created'];
        yield 'redemption lookup' => ['lookup'];
        yield 'redemption verification' => ['verification'];
    }

    #[DataProvider('wrappers')]
    public function test_a_discount_shape_without_its_type_reads_as_the_discount_member(string $answer): void
    {
        $data = $this->typeSpecificData($answer, ['discount_type' => 'percentage', 'discount_value' => 10, 'product_reference' => 'SKU1']);

        self::assertNull($data->getFreeProductRedemptionData());
        self::assertInstanceOf(DiscountRedemptionData::class, $data->getDiscountRedemptionData());
        self::assertSame('percentage', $data->getDiscountRedemptionData()->getDiscountType()?->value());
        self::assertSame(10, $data->getDiscountRedemptionData()->getDiscountValue());
        self::assertSame('SKU1', $data->getDiscountRedemptionData()->getProductReference());
        self::assertNull($data->getDiscountRedemptionData()->getType());
    }

    #[DataProvider('wrappers')]
    public function test_a_discount_value_alone_reads_as_the_discount_member(string $answer): void
    {
        $data = $this->typeSpecificData($answer, ['discount_value' => 5, 'product_reference' => 'SKU1']);

        self::assertNull($data->getFreeProductRedemptionData());
        self::assertSame(5, $data->getDiscountRedemptionData()?->getDiscountValue());
    }

    #[DataProvider('wrappers')]
    public function test_a_free_product_shape_without_its_type_reads_as_the_free_product_member(string $answer): void
    {
        $data = $this->typeSpecificData($answer, ['product_reference' => 'SKU1', 'payment_amount' => 200]);

        self::assertNull($data->getDiscountRedemptionData());
        self::assertInstanceOf(FreeProductRedemptionData::class, $data->getFreeProductRedemptionData());
        self::assertSame('SKU1', $data->getFreeProductRedemptionData()->getProductReference());
        self::assertSame(200, $data->getFreeProductRedemptionData()->getPaymentAmount());
    }

    #[DataProvider('wrappers')]
    public function test_a_product_reference_alone_reads_as_the_free_product_member(string $answer): void
    {
        $data = $this->typeSpecificData($answer, ['product_reference' => 'SKU1']);

        self::assertNull($data->getDiscountRedemptionData());
        self::assertSame('SKU1', $data->getFreeProductRedemptionData()?->getProductReference());
    }

    #[DataProvider('wrappers')]
    public function test_a_type_that_is_sent_still_decides_the_member(string $answer): void
    {
        $discount = $this->typeSpecificData($answer, ['type' => 'discount', 'discount_type' => 'fixed_amount', 'discount_value' => 250, 'product_reference' => null]);
        $free = $this->typeSpecificData($answer, ['type' => 'free_product', 'product_reference' => 'SKU1', 'payment_amount' => 0]);

        self::assertSame('discount', $discount->getDiscountRedemptionData()?->getType()?->value());
        self::assertSame(250, $discount->getDiscountRedemptionData()->getDiscountValue());
        self::assertSame('free_product', $free->getFreeProductRedemptionData()?->getType()?->value());
        self::assertNull($free->getDiscountRedemptionData());
    }

    #[DataProvider('wrappers')]
    public function test_an_unknown_type_leaves_both_members_empty_without_failing(string $answer): void
    {
        $data = $this->typeSpecificData($answer, ['type' => 'experience', 'discount_value' => 10]);

        self::assertNull($data->getDiscountRedemptionData());
        self::assertNull($data->getFreeProductRedemptionData());
    }

    /**
     * @param  array<string, mixed>  $typeSpecificData
     */
    private function typeSpecificData(string $answer, array $typeSpecificData): object
    {
        $this->fake->queueData(RedemptionAnswer::lookup($typeSpecificData), $answer === 'created' ? 201 : 200);
        $code = $this->puntjes()->redemptions();

        $data = match ($answer) {
            'created' => $code->post(new CreateRedemptionData)->wait()->getData(),
            'lookup' => $code->byCode('ABC123')->get()->wait()->getData(),
            'verification' => $code->byCode('ABC123')->verify()->post(new VerifyRedemptionData)->wait()->getData(),
        };

        return $data->getTypeSpecificData();
    }
}
