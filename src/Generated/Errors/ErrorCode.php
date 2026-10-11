<?php

declare(strict_types=1);

namespace Puntjes\Generated\Errors;

/**
 * Every error code the API documents, generated from the x-error-codes of spec/api.json. Do not edit.
 */
enum ErrorCode: string
{
    case BatchTooLarge = 'BATCH_TOO_LARGE';
    case BranchInactive = 'BRANCH_INACTIVE';
    case BranchNotFound = 'BRANCH_NOT_FOUND';
    case BranchRequired = 'BRANCH_REQUIRED';
    case CardSendThrottled = 'CARD_SEND_THROTTLED';
    case CodeAlreadyUsed = 'CODE_ALREADY_USED';
    case CodeCancelled = 'CODE_CANCELLED';
    case CodeExpired = 'CODE_EXPIRED';
    case CustomerAlreadyLinked = 'CUSTOMER_ALREADY_LINKED';
    case CustomerDeactivated = 'CUSTOMER_DEACTIVATED';
    case CustomerEmailSuppressed = 'CUSTOMER_EMAIL_SUPPRESSED';
    case CustomerHasNoEmail = 'CUSTOMER_HAS_NO_EMAIL';
    case CustomerNotFound = 'CUSTOMER_NOT_FOUND';
    case ExternalIdDuplicate = 'EXTERNAL_ID_DUPLICATE';
    case ExternalIdNotFound = 'EXTERNAL_ID_NOT_FOUND';
    case Forbidden = 'FORBIDDEN';
    case IdempotencyKeyConflict = 'IDEMPOTENCY_KEY_CONFLICT';
    case IdentifierDuplicate = 'IDENTIFIER_DUPLICATE';
    case InsufficientBalance = 'INSUFFICIENT_BALANCE';
    case InternalError = 'INTERNAL_ERROR';
    case InvalidClient = 'INVALID_CLIENT';
    case InvalidJson = 'INVALID_JSON';
    case LoyaltyCardNotFound = 'LOYALTY_CARD_NOT_FOUND';
    case MarketingConsentUnderAge = 'MARKETING_CONSENT_UNDER_AGE';
    case MethodNotAllowed = 'METHOD_NOT_ALLOWED';
    case NoWallet = 'NO_WALLET';
    case OutOfStock = 'OUT_OF_STOCK';
    case PlanLimitExceeded = 'PLAN_LIMIT_EXCEEDED';
    case ProductExternalIdDuplicate = 'PRODUCT_EXTERNAL_ID_DUPLICATE';
    case ProductNotFound = 'PRODUCT_NOT_FOUND';
    case RateLimited = 'RATE_LIMITED';
    case RedemptionLimitReached = 'REDEMPTION_LIMIT_REACHED';
    case RedemptionNotFound = 'REDEMPTION_NOT_FOUND';
    case RewardNotFound = 'REWARD_NOT_FOUND';
    case RewardUnavailable = 'REWARD_UNAVAILABLE';
    case RouteNotFound = 'ROUTE_NOT_FOUND';
    case SubscriptionExpired = 'SUBSCRIPTION_EXPIRED';
    case Unauthenticated = 'UNAUTHENTICATED';
    case UnsupportedMediaType = 'UNSUPPORTED_MEDIA_TYPE';
    case ValidationError = 'VALIDATION_ERROR';
    case VendorContextMissing = 'VENDOR_CONTEXT_MISSING';
    case VendorContextRequired = 'VENDOR_CONTEXT_REQUIRED';
    case VendorDeactivated = 'VENDOR_DEACTIVATED';
    case VendorInactive = 'VENDOR_INACTIVE';
    case VendorPending = 'VENDOR_PENDING';
    case VendorSuspended = 'VENDOR_SUSPENDED';
    case VerificationFailed = 'VERIFICATION_FAILED';
    case VoucherAlreadyUsed = 'VOUCHER_ALREADY_USED';
    case VoucherExpired = 'VOUCHER_EXPIRED';
    case VoucherNotFound = 'VOUCHER_NOT_FOUND';
    case WalletNotFound = 'WALLET_NOT_FOUND';

    public function status(): int
    {
        return match ($this) {
            self::BatchTooLarge => 422,
            self::BranchInactive => 422,
            self::BranchNotFound => 422,
            self::BranchRequired => 422,
            self::CardSendThrottled => 429,
            self::CodeAlreadyUsed => 422,
            self::CodeCancelled => 422,
            self::CodeExpired => 422,
            self::CustomerAlreadyLinked => 409,
            self::CustomerDeactivated => 422,
            self::CustomerEmailSuppressed => 422,
            self::CustomerHasNoEmail => 422,
            self::CustomerNotFound => 404,
            self::ExternalIdDuplicate => 409,
            self::ExternalIdNotFound => 404,
            self::Forbidden => 403,
            self::IdempotencyKeyConflict => 422,
            self::IdentifierDuplicate => 409,
            self::InsufficientBalance => 422,
            self::InternalError => 500,
            self::InvalidClient => 401,
            self::InvalidJson => 400,
            self::LoyaltyCardNotFound => 422,
            self::MarketingConsentUnderAge => 422,
            self::MethodNotAllowed => 405,
            self::NoWallet => 422,
            self::OutOfStock => 422,
            self::PlanLimitExceeded => 429,
            self::ProductExternalIdDuplicate => 409,
            self::ProductNotFound => 404,
            self::RateLimited => 429,
            self::RedemptionLimitReached => 422,
            self::RedemptionNotFound => 404,
            self::RewardNotFound => 404,
            self::RewardUnavailable => 422,
            self::RouteNotFound => 404,
            self::SubscriptionExpired => 403,
            self::Unauthenticated => 401,
            self::UnsupportedMediaType => 415,
            self::ValidationError => 422,
            self::VendorContextMissing => 500,
            self::VendorContextRequired => 400,
            self::VendorDeactivated => 403,
            self::VendorInactive => 403,
            self::VendorPending => 403,
            self::VendorSuspended => 403,
            self::VerificationFailed => 422,
            self::VoucherAlreadyUsed => 422,
            self::VoucherExpired => 422,
            self::VoucherNotFound => 404,
            self::WalletNotFound => 404,
        };
    }

    public static function tryFromString(?string $code): ?self
    {
        return $code === null ? null : self::tryFrom($code);
    }
}
