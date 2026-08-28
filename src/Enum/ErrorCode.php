<?php

declare(strict_types=1);

namespace Puntjes\Enum;

use Puntjes\Exception\ApiException;

/**
 * The stable catalogue of `error.code` values the Puntjes API emits.
 *
 * Exposed on {@see ApiException::errorCode()} so integrators can
 * branch on a domain outcome without string matching. Unknown codes — a newer API
 * than this SDK — resolve to `null` there; the raw string stays available via
 * `ApiException::code()`, so a new server-side code can never break a client.
 */
enum ErrorCode: string
{
    // Auth / transport-level
    case Unauthenticated = 'UNAUTHENTICATED';
    case InvalidClient = 'INVALID_CLIENT';
    case Forbidden = 'FORBIDDEN';
    case RouteNotFound = 'ROUTE_NOT_FOUND';
    case MethodNotAllowed = 'METHOD_NOT_ALLOWED';
    case RateLimited = 'RATE_LIMITED';
    case ValidationError = 'VALIDATION_ERROR';
    case InternalError = 'INTERNAL_ERROR';

    // Vendor account state (403) and context resolution
    case VendorContextRequired = 'VENDOR_CONTEXT_REQUIRED';
    case VendorContextMissing = 'VENDOR_CONTEXT_MISSING';
    case VendorInactive = 'VENDOR_INACTIVE';
    case VendorPending = 'VENDOR_PENDING';
    case VendorSuspended = 'VENDOR_SUSPENDED';
    case VendorDeactivated = 'VENDOR_DEACTIVATED';

    // Plan / quota
    case PlanLimitExceeded = 'PLAN_LIMIT_EXCEEDED';

    // Branches — all 422. NotFound and Inactive are deliberately distinct: one means
    // you typed a key the vendor has never had, the other that they closed that shop.
    case BranchNotFound = 'BRANCH_NOT_FOUND';
    case BranchInactive = 'BRANCH_INACTIVE';
    /** The reward or voucher is limited to branches, and this is not one of them. */
    case BranchRequired = 'BRANCH_REQUIRED';

    // Customers
    case CustomerNotFound = 'CUSTOMER_NOT_FOUND';
    case CustomerDeactivated = 'CUSTOMER_DEACTIVATED';
    case IdentifierDuplicate = 'IDENTIFIER_DUPLICATE';
    case ExternalIdDuplicate = 'EXTERNAL_ID_DUPLICATE';
    case ExternalIdNotFound = 'EXTERNAL_ID_NOT_FOUND';

    // Wallet
    case NoWallet = 'NO_WALLET';
    case WalletNotFound = 'WALLET_NOT_FOUND';
    case InsufficientBalance = 'INSUFFICIENT_BALANCE';

    // Rewards & redemptions
    case RewardNotFound = 'REWARD_NOT_FOUND';
    case RewardUnavailable = 'REWARD_UNAVAILABLE';
    case OutOfStock = 'OUT_OF_STOCK';
    case RedemptionNotFound = 'REDEMPTION_NOT_FOUND';
    case CodeAlreadyUsed = 'CODE_ALREADY_USED';
    case CodeExpired = 'CODE_EXPIRED';
    case VerificationFailed = 'VERIFICATION_FAILED';
    case IdempotencyKeyConflict = 'IDEMPOTENCY_KEY_CONFLICT';

    // Products
    case ProductNotFound = 'PRODUCT_NOT_FOUND';
    case ProductExternalIdDuplicate = 'PRODUCT_EXTERNAL_ID_DUPLICATE';
    case BatchTooLarge = 'BATCH_TOO_LARGE';

    /** Resolve a wire code, tolerating codes this SDK version does not know yet. */
    public static function tryFromString(?string $code): ?self
    {
        return $code === null ? null : self::tryFrom($code);
    }
}
