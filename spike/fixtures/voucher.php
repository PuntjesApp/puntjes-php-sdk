use App\Domain\Campaign\Actions\ClaimMomentOccurrenceAction;
use App\Domain\Campaign\Actions\IssueCampaignVoucherAction;
use App\Domain\Campaign\Models\Campaign;
use App\Domain\Customer\Models\Customer;
use App\Support\VendorContext;
use Carbon\CarbonImmutable;

$vendorId = (int) (getenv('SPIKE_VENDOR_ID') ?: 2);
app(VendorContext::class)->setVendor($vendorId);
$customer = Customer::factory()->create(['vendor_id' => $vendorId]);
$campaign = Campaign::factory()
    ->customerMoment('birthday', ['gift' => ['type' => 'discount', 'discount' => ['kind' => 'percentage', 'percentage' => 15], 'validity_days' => 30]])
    ->create(['vendor_id' => $vendorId, 'name' => 'sdk-spike '.now()->toIso8601String(), 'starts_at' => now()->subDay(), 'ends_at' => null])
    ->refresh();
$award = ClaimMomentOccurrenceAction::run($campaign, $customer->id, 'birthday:'.now()->year, CarbonImmutable::now());
$voucher = IssueCampaignVoucherAction::run($campaign, $customer->id, $award);
echo 'SPIKE_VOUCHER='.json_encode(['code' => $voucher->code, 'customer' => $customer->id]).PHP_EOL;
