# Payment Webhook Processing with Idempotency

This README explains how the webhook processing flow works, including
idempotency handling, middleware validation, and database locking.

## 1. Idempotency Key Middleware

Every webhook request **must include** an `idempotency-key` header.

If missing, the middleware returns an error:

    {
      "status": "error",
      "message": "Idempotency key is required"
    }

This ensures every webhook event is uniquely identified.

------------------------------------------------------------------------

## 2. Webhook Processing Logic

### Steps:

1.  Validate request using `ProcessWebhookRequest`.
2.  Extract:
    -   `idempotency-key`
    -   `order_id`
    -   `status`
3.  Run a database transaction to prevent race conditions.

### Key Points:

-   If the order doesn't exist → log as `pending_order`.
-   If the order exists → create a webhook log and update the order
    status.
-   For failed payments → order is cancelled + stock returned.
-   Cache for product stock is cleared after stock update.

------------------------------------------------------------------------

## 3. Idempotency Handling

To ensure the webhook **never runs twice**, the system relies on:

-   A **unique index** on the `idempotency_key`
-   A try/catch around database insert

If a duplicate happens (race condition), Laravel throws SQL error code
`23000`.

Instead of failing, the system returns the existing log:

    Webhook already processed (race condition handled)

------------------------------------------------------------------------

## 4. Optional Lock Release

Some examples online show:

``` php
optional($lock)->release();
```

This is used when you acquire a lock using Laravel Cache locks.

`optional()` allows you to call a method on a variable that may be null.

If `$lock` is null → nothing happens\
If `$lock` is a lock → `release()` frees the lock

You **do NOT** need this unless you are using cache locks manually.

------------------------------------------------------------------------

## 5. Return Responses

The controller uses `ApiResponseTrait` to return consistent JSON
responses:

### Success Example:

    {
      "status": "success",
      "message": "Webhook processed successfully",
      "data": { ... }
    }

### Error Example:

    {
      "status": "error",
      "message": "Webhook processing failed"
    }

------------------------------------------------------------------------

## 6. Summary

Your webhook is now:

-   **Safe from duplicates**
-   **Transaction-protected**
-   **Race-condition-proof**
-   **Order + stock updates handled cleanly**
-   **Using clean JSON API responses**