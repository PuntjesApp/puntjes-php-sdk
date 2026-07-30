<?php

declare(strict_types=1);

namespace Puntjes\Exception;

/**
 * 422 VALIDATION_ERROR — the request body failed server-side validation.
 *
 * `error.details` carries Laravel's field => messages map, exposed here as
 * {@see errors()}. Messages are English only; the API does not localise them.
 *
 * Note that 422 is also used for domain refusals that are NOT validation failures
 * (INSUFFICIENT_BALANCE, CUSTOMER_DEACTIVATED, OUT_OF_STOCK, …). Those surface as a
 * plain {@see ApiException}, so catching this type never swallows them.
 */
final class ValidationException extends ApiException
{
    /**
     * Field-keyed validation messages, e.g. `['total_amount' => ['The total amount field is required.']]`.
     *
     * @return array<string, array<int, string>>
     */
    public function errors(): array
    {
        /** @var array<string, array<int, string>> */
        return $this->details() ?? [];
    }

    /**
     * All messages flattened, in field order.
     *
     * @return array<int, string>
     */
    public function messages(): array
    {
        $flat = [];

        foreach ($this->errors() as $fieldMessages) {
            foreach ($fieldMessages as $message) {
                $flat[] = $message;
            }
        }

        return $flat;
    }

    /** @return array<int, string> */
    public function errorsFor(string $field): array
    {
        return $this->errors()[$field] ?? [];
    }
}
