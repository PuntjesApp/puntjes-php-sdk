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
    /**
     * The access token is missing, unreadable, expired or revoked, or its client was
     * revoked (401). A new token fixes it, and the SDK gets one by itself once.
     */
    case Unauthenticated = 'UNAUTHENTICATED';
    /**
     * The token is valid, but its OAuth client has no vendor or cannot use client
     * credentials (401). A new token does not fix it: the client itself is wrong.
     */
    case InvalidClient = 'INVALID_CLIENT';
    case Forbidden = 'FORBIDDEN';
    /**
     * The request body could not be read: the JSON is cut off, or it is not valid UTF-8 (400).
     * The API created, changed and sent nothing. Sending the same body again fails the same
     * way, so fix the body first. The SDK never replays it.
     */
    case InvalidJson = 'INVALID_JSON';
    /**
     * The request has a body, but its `Content-Type` is not JSON or a form, so the API
     * cannot read it (415). Nothing was created, changed or sent. The SDK always sends
     * JSON, so this means something between the SDK and the API changed the request.
     * The SDK never replays it.
     */
    case UnsupportedMediaType = 'UNSUPPORTED_MEDIA_TYPE';
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
    case CustomerAlreadyLinked = 'CUSTOMER_ALREADY_LINKED';

    // Loyalty card delivery
    case CustomerHasNoEmail = 'CUSTOMER_HAS_NO_EMAIL';
    /**
     * Earlier mail to the customer's address bounced or was marked as spam, so the API
     * refuses to send the card (422). Nothing is queued and the per-customer cooldown is
     * not spent: ask for an address that works, or switch sending back on from the
     * customer's page in the admin portal.
     */
    case CustomerEmailSuppressed = 'CUSTOMER_EMAIL_SUPPRESSED';
    case LoyaltyCardNotFound = 'LOYALTY_CARD_NOT_FOUND';
    case CardSendThrottled = 'CARD_SEND_THROTTLED';

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
    /** The shop cancelled this redemption and the customer got the points back (422). */
    case CodeCancelled = 'CODE_CANCELLED';
    case VerificationFailed = 'VERIFICATION_FAILED';
    case IdempotencyKeyConflict = 'IDEMPOTENCY_KEY_CONFLICT';

    // Campaign vouchers
    case VoucherNotFound = 'VOUCHER_NOT_FOUND';
    case VoucherExpired = 'VOUCHER_EXPIRED';
    case VoucherAlreadyUsed = 'VOUCHER_ALREADY_USED';

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
