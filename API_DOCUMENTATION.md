# API Documentation - DailyDose System

## Base URLs

### Customer/User APIs
```
BASE_URL: https://your-domain.com/app/v1/api/
```

### Rider APIs
```
BASE_URL: https://your-domain.com/rider/app/v1/api/
```

### Admin APIs
```
BASE_URL: https://your-domain.com/admin/app/v1/api/
```

---

## Customer/User APIs (62 Endpoints)

### Authentication & User Management

#### 1. Login
**Endpoint:** `POST /app/v1/api/login`

**Parameters:**
```json
{
  "mobile": "9874565478",
  "fcm_id": "FCM_ID" // optional
}
```

---

#### 2. Update FCM
**Endpoint:** `POST /app/v1/api/update_fcm`

**Parameters:**
```json
{
  "user_id": 12,
  "fcm_id": "FCM_ID"
}
```

---

#### 3. Reset Password
**Endpoint:** `POST /app/v1/api/reset_password`

**Parameters:**
```json
{
  "mobile_no": "7894561235",
  "new": "pass@123"
}
```

---

#### 4. Get Login Identity
**Endpoint:** `POST /app/v1/api/get_login_identity`

**Parameters:** None

---

#### 5. Verify User
**Endpoint:** `POST /app/v1/api/verify_user`

**Parameters:**
```json
{
  "mobile": "9874565478"
}
```

---

#### 6. Register User
**Endpoint:** `POST /app/v1/api/register_user`

**Parameters:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "mobile": "9874565478",
  "country_code": "91",
  "referral_code": "MY_CODE",
  "fcm_id": "YOUR_FCM_ID", // optional
  "friends_code": "45dsrwr", // optional
  "latitude": "66.89", // optional
  "longitude": "67.8" // optional
}
```

---

#### 7. Update User
**Endpoint:** `POST /app/v1/api/update_user`

**Parameters:**
```json
{
  "user_id": 34,
  "username": "hiten", // optional
  "mobile": "7852347890", // optional
  "email": "aman@gmail.com", // optional
  "address": "Time Square", // optional
  "latitude": "45.453", // optional
  "longitude": "45.453", // optional
  "image": [], // optional
  "city_id": 1, // optional
  "referral_code": "Userscode" // optional
}
```

---

#### 60. Delete My Account
**Endpoint:** `POST /app/v1/api/delete_my_account`

**Parameters:**
```json
{
  "user_id": 1
}
```

---

### Location & Delivery

#### 8. Is City Deliverable
**Endpoint:** `POST /app/v1/api/is_city_deliverable`

**Parameters:**
```json
{
  "id": 1, // optional
  "name": "bhuj" // optional
}
```

---

#### 12. Get Cities
**Endpoint:** `POST /app/v1/api/get_cities`

**Parameters:**
```json
{
  "sort": "c.name", // c.name / c.id - optional
  "order": "ASC", // DESC/ASC - default: ASC - optional
  "search": "value", // optional
  "limit": 25, // default: 25
  "offset": 0 // default: 0
}
```

---

#### 49. Get Delivery Charges
**Endpoint:** `POST /app/v1/api/get_delivery_charges`

**Parameters:**
```json
{
  "user_id": 1,
  "address_id": 1
}
```

---

#### 58. Search Places
**Endpoint:** `POST /app/v1/api/search_places`

**Parameters:**
```json
{
  "input": "string" // user typed input
}
```

---

#### 59. Get Live Tracking Details
**Endpoint:** `POST /app/v1/api/get_live_tracking_details`

**Parameters:**
```json
{
  "order_id": 1
}
```

---

### Address Management

#### 16. Add Address
**Endpoint:** `POST /app/v1/api/add_address`

**Parameters:**
```json
{
  "user_id": 1,
  "mobile": "9727800638",
  "address": "#123,Time Square Empire,bhuj",
  "city": 1,
  "latitude": "1234",
  "longitude": "1234",
  "area": "umiya nagar",
  "type": "Home", // Home | Office | Others - optional
  "name": "John Smith", // optional
  "country_code": "+91", // optional
  "alternate_mobile": "9876543210", // optional
  "landmark": "prince hotel", // optional
  "pincode": "370001", // optional
  "state": "Gujarat", // optional
  "country": "India", // optional
  "is_default": 1 // optional, default: 0
}
```

---

#### 17. Update Address
**Endpoint:** `POST /app/v1/api/update_address`

**Parameters:**
```json
{
  "id": 1,
  "user_id": 1, // optional
  "mobile": "9727800638", // optional
  "address": "#123,Time Square,bhuj", // optional
  "city": 1, // optional
  "type": "Home", // Home | Office | Others - optional
  "name": "John Smith", // optional
  "country_code": "+91", // optional
  "alternate_mobile": "9876543210", // optional
  "landmark": "prince hotel", // optional
  "area": "umiya nagar", // optional
  "pincode": "370001", // optional
  "state": "Gujarat", // optional
  "country": "India", // optional
  "latitude": "1234", // optional
  "longitude": "1234", // optional
  "is_default": 1 // optional, default: 0
}
```

---

#### 18. Get Address
**Endpoint:** `POST /app/v1/api/get_address`

**Parameters:**
```json
{
  "user_id": 3,
  "address_id": "bhuj", // optional - get specific address by id
  "partner_id": 1234 // optional - for delivery check
}
```

---

#### 19. Delete Address
**Endpoint:** `POST /app/v1/api/delete_address`

**Parameters:**
```json
{
  "id": 3
}
```

---

### Content & UI

#### 9. Get Slider Images
**Endpoint:** `POST /app/v1/api/get_slider_images`

**Parameters:** None

---

#### 10. Get Offer Images
**Endpoint:** `POST /app/v1/api/get_offer_images`

**Parameters:** None

---

#### 34. Get Sections
**Endpoint:** `POST /app/v1/api/get_sections`

**Parameters:**
```json
{
  "limit": 25, // default: 25 - optional
  "offset": 0, // default: 0 - optional
  "user_id": 12, // optional
  "section_id": 4, // optional
  "top_rated_foods": 1, // default: 0 - optional
  "p_limit": 10, // default: 10 - optional
  "p_offset": 0, // default: 0 - optional
  "p_sort": "pv.price", // default: pid - optional
  "p_order": "asc", // default: desc - optional
  "filter_by": "p.id", // p.id = product list | sd.user_id = partner list - default: p.id
  "latitude": "123", // optional
  "longitude": "123" // optional
}
```
**Response:** `indicator: 0 - none | 1 - veg | 2 - non-veg`

---

#### 20. Get Settings
**Endpoint:** `POST /app/v1/api/get_settings`

**Parameters:**
```json
{
  "type": "all", // payment_method | all - default: all - optional
  "user_id": 15 // optional
}
```

---

#### 46. Get FAQs
**Endpoint:** `POST /app/v1/api/get_faqs`

**Parameters:** None

---

### Categories & Products

#### 11. Get Categories
**Endpoint:** `POST /app/v1/api/get_categories`

**Parameters:**
```json
{
  "id": 15, // optional
  "limit": 25, // default: 25 - optional
  "offset": 0, // default: 0 - optional
  "sort": "id", // id / name - default: id - optional
  "order": "DESC" // DESC/ASC - default: DESC - optional
}
```

---

#### 13. Get Products
**Endpoint:** `POST /app/v1/api/get_products`

**Parameters:**
```json
{
  "id": 101, // optional
  "category_id": 29, // optional
  "user_id": 15, // optional
  "search": "keyword", // optional - search by product name and highlights
  "tags": "multiword tag1, tag2, another tag", // optional - search by restro and product tags
  "highlights": "multiword tag1, tag2, another tag", // optional
  "attribute_value_ids": "34,23,12", // optional - Use only for filteration
  "limit": 25, // default: 25 - optional
  "offset": 0, // default: 0 - optional
  "sort": "p.id", // p.id / p.date_added / pv.price - default: p.id - optional
  "order": "DESC", // DESC/ASC - default: DESC - optional
  "top_rated_foods": 1, // default: 0 - optional
  "discount": 5, // optional
  "min_price": 10000, // optional
  "max_price": 50000, // optional
  "partner_id": 1255, // optional
  "product_ids": "19,20", // optional
  "product_variant_ids": "44,45,40", // optional
  "vegetarian": 1, // 1 - veg | 2 - non-veg | 3 - Both - optional
  "filter_by": "sd.user_id", // p.id = product list | sd.user_id = partner list - default: sd.user_id
  "latitude": "123", // optional
  "longitude": "123", // optional
  "city_id": 1 // optional
}
```

---

#### 15. Get Partners
**Endpoint:** `POST /app/v1/api/get_partners`

**Parameters:**
```json
{
  "id": 1, // optional
  "city_id": 1, // optional
  "user_id": 1, // optional
  "limit": 25, // default: 25 - optional
  "offset": 0, // default: 0 - optional
  "sort": "p.id", // p.id / p.date_added / pv.price - default: p.id - optional
  "order": "DESC", // DESC/ASC - default: DESC - optional
  "top_rated_partner": 1, // default: 0 - optional
  "only_opened_partners": 1, // default: 0 - optional
  "vegetarian": 1, // 1 - veg | 2 - non-veg | 3 - both - optional
  "latitude": "123", // optional
  "longitude": "123" // optional
}
```

---

### Cart Management

#### 26. Manage Cart (Add/Update)
**Endpoint:** `POST /app/v1/api/manage_cart`

**Parameters:**
```json
{
  "user_id": 2,
  "product_variant_id": 23,
  "clear_cart": 0, // 1 => clear cart | 0 => default - optional
  "is_saved_for_later": 0, // default: 0
  "qty": 2, // pass 0 to remove qty
  "add_on_id": 1, // optional
  "add_on_qty": 1 // required when passing add on id
}
```

---

#### 27. Get User Cart
**Endpoint:** `POST /app/v1/api/get_user_cart`

**Parameters:**
```json
{
  "user_id": 1,
  "is_saved_for_later": 0 // default: 0
}
```

---

#### 48. Remove from Cart
**Endpoint:** `POST /app/v1/api/remove_from_cart`

**Parameters:**
```json
{
  "user_id": 2,
  "product_variant_id": 23 // optional - if not passed all items in the cart will be removed
}
```

---

### Favorites

#### 28. Add to Favorites
**Endpoint:** `POST /app/v1/api/add_to_favorites`

**Parameters:**
```json
{
  "user_id": 15,
  "type": "partners", // partners | products
  "type_id": 60
}
```

---

#### 29. Remove from Favorites
**Endpoint:** `POST /app/v1/api/remove_from_favorites`

**Parameters:**
```json
{
  "user_id": 15,
  "type": "partners", // partners | products - optional
  "type_id": 60 // optional
}
```

---

#### 30. Get Favorites
**Endpoint:** `POST /app/v1/api/get_favorites`

**Parameters:**
```json
{
  "user_id": 12,
  "type": "partners", // partners | products
  "limit": 10, // optional
  "offset": 0 // optional
}
```

---

### Orders

#### 21. Place Order
**Endpoint:** `POST /app/v1/api/place_order`

**Parameters:**
```json
{
  "user_id": 5,
  "mobile": "9974692496",
  "product_variant_id": "1,2,3",
  "quantity": "3,3,1",
  "total": "60.0",
  "delivery_charge": "20.0",
  "tax_amount": "10",
  "tax_percentage": "10",
  "final_total": "55",
  "latitude": "40.1451",
  "longitude": "-45.4545",
  "promo_code": "NEW20", // optional
  "payment_method": "COD", // Paypal / Payumoney / COD / PAYTM
  "address_id": 17,
  "is_wallet_used": 1, // default: 0
  "wallet_balance_used": 1,
  "active_status": "pending", // optional
  "order_note": "text", // optional
  "delivery_tip": "text", // optional
  "is_self_pick_up": 0 // 0|1 - default: 0 - required when its self pickup
}
```

---

#### 22. Get Orders
**Endpoint:** `POST /app/v1/api/get_orders`

**Parameters:**
```json
{
  "user_id": 101,
  "id": 123, // order_id - optional
  "active_status": "pending", // pending|confirmed|preparing|out_for_delivery|delivered|cancelled - optional
  "limit": 25, // default: 25 - optional
  "offset": 0, // default: 0 - optional
  "sort": "o.id", // o.id / date_added - default: o.id - optional
  "order": "DESC", // DESC/ASC - default: DESC - optional
  "download_invoice": 0 // default: 0 - optional
}
```

---

#### 32. Update Order Status
**Endpoint:** `POST /app/v1/api/update_order_status`

**Parameters:**
```json
{
  "status": "cancelled",
  "order_id": 1201,
  "reason": "test"
}
```

---

#### 36. Delete Order
**Endpoint:** `POST /app/v1/api/delete_order`

**Parameters:**
```json
{
  "order_id": 1
}
```

---

### Promo Codes

#### 14. Validate Promo Code
**Endpoint:** `POST /app/v1/api/validate_promo_code`

**Parameters:**
```json
{
  "promo_code": "NEWOFF10",
  "user_id": 28,
  "final_total": "300"
}
```

---

#### 47. Get Promo Codes
**Endpoint:** `POST /app/v1/api/get_promo_codes`

**Parameters:**
```json
{
  "search": "Search keyword", // optional
  "limit": 25, // default: 25 - optional
  "offset": 0, // default: 0 - optional
  "sort": "id", // id | date_created | last_updated - default: id - optional
  "order": "DESC" // DESC/ASC - default: DESC - optional
}
```

---

#### 50. Validate Refer Code
**Endpoint:** `POST /app/v1/api/validate_refer_code`

**Parameters:**
```json
{
  "referral_code": "USERS_CODE_TO_BE_VALIDATED"
}
```

---

### Ratings & Reviews

#### 23. Set Product Rating
**Endpoint:** `POST /app/v1/api/set_product_rating`

**Parameters:**
```json
{
  "user_id": 21,
  "product_id": 33,
  "rating": 4.2,
  "comment": "Done", // optional
  "images[]": [] // optional - array of images
}
```

---

#### 24. Delete Product Rating
**Endpoint:** `POST /app/v1/api/delete_product_rating`

**Parameters:**
```json
{
  "rating_id": 32
}
```

---

#### 25. Get Product Rating
**Endpoint:** `POST /app/v1/api/get_product_rating`

**Parameters:**
```json
{
  "product_id": 12,
  "user_id": 1, // optional
  "limit": 25, // default: 25 - optional
  "offset": 0, // default: 0 - optional
  "sort": "type", // default: type - optional
  "order": "DESC" // DESC/ASC - default: DESC - optional
}
```

---

#### 43. Set Rider Rating
**Endpoint:** `POST /app/v1/api/set_rider_rating`

**Parameters:**
```json
{
  "user_id": 21,
  "rider_id": 33,
  "rating": 4.2,
  "comment": "Done" // optional
}
```

---

#### 44. Get Rider Rating
**Endpoint:** `POST /app/v1/api/get_rider_rating`

**Parameters:**
```json
{
  "rider_id": 12,
  "user_id": 1, // optional
  "limit": 25, // default: 25 - optional
  "offset": 0, // default: 0 - optional
  "sort": "u.id", // default: u.id - optional
  "order": "DESC" // DESC/ASC - default: DESC - optional
}
```

---

#### 45. Delete Rider Rating
**Endpoint:** `POST /app/v1/api/delete_rider_rating`

**Parameters:**
```json
{
  "rating_id": 32
}
```

---

### Notifications

#### 31. Get Notifications
**Endpoint:** `POST /app/v1/api/get_notifications`

**Parameters:**
```json
{
  "sort": "id", // id / date_added - default: id - optional
  "order": "DESC", // DESC/ASC - default: DESC - optional
  "limit": 25, // default: 25 - optional
  "offset": 0 // default: 0 - optional
}
```

---

### Transactions & Wallet

#### 33. Add Transaction
**Endpoint:** `POST /app/v1/api/add_transaction`

**Parameters:**
```json
{
  "transaction_type": "transaction", // transaction / wallet - default: transaction - optional
  "user_id": 15,
  "order_id": 23,
  "type": "COD", // COD / stripe / razorpay / paypal / paystack / flutterwave - for transaction | credit / debit - for wallet
  "payment_method": "razorpay", // used for wallet credit option, required when transaction_type - wallet and type - credit
  "txn_id": "201567892154",
  "amount": 450,
  "status": "success", // success / failure
  "message": "Done"
}
```

---

#### 35. Transactions
**Endpoint:** `POST /app/v1/api/transactions`

**Parameters:**
```json
{
  "user_id": 73,
  "id": 1001, // optional
  "transaction_type": "transaction", // transaction / wallet - default: transaction - optional
  "type": "COD", // COD / stripe / razorpay / paypal / paystack / flutterwave - for transaction | credit / debit - for wallet - optional
  "search": "Search keyword", // optional
  "limit": 25, // default: 25 - optional
  "offset": 0, // default: 0 - optional
  "sort": "id", // id / date_created - default: id - optional
  "order": "DESC" // DESC/ASC - default: DESC - optional
}
```

---

#### 61. Send Withdrawal Request
**Endpoint:** `POST /app/v1/api/send_withdrawal_request`

**Parameters:**
```json
{
  "user_id": 15,
  "payment_address": "12343535",
  "amount": 560
}
```

---

#### 62. Get Withdrawal Request
**Endpoint:** `POST /app/v1/api/get_withdrawal_request`

**Parameters:**
```json
{
  "user_id": 15,
  "limit": 10, // optional
  "offset": 0 // optional
}
```

---

### Payment Gateway APIs

#### 51. Get PayPal Link
**Endpoint:** `POST /app/v1/api/get_paypal_link`

**Parameters:**
```json
{
  "user_id": 73,
  "order_id": 11,
  "amount": 150
}
```

---

#### 52. Stripe Webhook
**Endpoint:** `POST /app/v1/api/stripe_webhook`

**Note:** Used by Stripe webhook

---

#### 53. Generate Paytm Checksum
**Endpoint:** `POST /app/v1/api/generate_paytm_checksum`

**Parameters:**
```json
{
  "order_id": 1001,
  "amount": 1099,
  "user_id": 73, // optional
  "industry_type": "Industry", // optional
  "channel_id": "WAP", // optional
  "website": "website link" // optional
}
```

---

#### 54. Generate Paytm Transaction Token
**Endpoint:** `POST /app/v1/api/generate_paytm_txn_token`

**Parameters:**
```json
{
  "amount": "100.00",
  "order_id": 102,
  "user_id": 73,
  "industry_type": "", // optional
  "channel_id": "", // optional
  "website": "" // optional
}
```

---

#### 55. Validate Paytm Checksum
**Endpoint:** `POST /app/v1/api/validate_paytm_checksum`

**Parameters:**
```json
{
  "paytm_checksum": "PAYTM_CHECKSUM",
  "order_id": 1001,
  "amount": 1099,
  "user_id": 73, // optional
  "industry_type": "Industry", // optional
  "channel_id": "WAP", // optional
  "website": "website link" // optional
}
```

---

#### 56. Flutterwave Webview
**Endpoint:** `POST /app/v1/api/flutterwave_webview`

**Parameters:**
```json
{
  "amount": 100,
  "user_id": 73,
  "reference": "eShop-165232013-400" // optional
}
```

---

#### 57. Flutterwave Payment Response
**Endpoint:** `POST /app/v1/api/flutterwave_payment_response`

**Parameters:** See response from Flutterwave

---

### Support & Tickets

#### 37. Get Ticket Types
**Endpoint:** `POST /app/v1/api/get_ticket_types`

**Parameters:** None

---

#### 38. Add Ticket
**Endpoint:** `POST /app/v1/api/add_ticket`

**Parameters:**
```json
{
  "ticket_type_id": 1,
  "subject": "product_image not displaying",
  "email": "test@gmail.com",
  "description": "its not showing images of products in web",
  "user_id": 1
}
```

---

#### 39. Edit Ticket
**Endpoint:** `POST /app/v1/api/edit_ticket`

**Parameters:**
```json
{
  "ticket_id": 1,
  "ticket_type_id": 1,
  "subject": "product_image not displaying",
  "email": "test@gmail.com",
  "description": "its not showing attachments of products in web",
  "user_id": 1,
  "status": 3 // 1: pending | 2: opened | 3: resolved | 4: closed | 5: reopened
}
```

---

#### 40. Send Message
**Endpoint:** `POST /app/v1/api/send_message`

**Parameters:**
```json
{
  "user_type": "user",
  "user_id": 1,
  "ticket_id": 1,
  "message": "test",
  "attachments[]": [] // optional - files (type allowed: image, video, document, spreadsheet, archive)
}
```

---

#### 41. Get Tickets
**Endpoint:** `POST /app/v1/api/get_tickets`

**Parameters:**
```json
{
  "ticket_id": 1001, // optional
  "ticket_type_id": 1001, // optional
  "user_id": 1001, // optional
  "status": 1, // 1: pending | 2: opened | 3: resolved | 4: closed | 5: reopened - optional
  "search": "Search keyword", // optional
  "limit": 25, // default: 25 - optional
  "offset": 0, // default: 0 - optional
  "sort": "id", // id | date_created | last_updated - default: id - optional
  "order": "DESC" // DESC/ASC - default: DESC - optional
}
```

---

#### 42. Get Messages
**Endpoint:** `POST /app/v1/api/get_messages`

**Parameters:**
```json
{
  "ticket_id": 1001,
  "user_type": 1001, // optional
  "user_id": 1001, // optional
  "search": "Search keyword", // optional
  "limit": 25, // default: 25 - optional
  "offset": 0, // default: 0 - optional
  "sort": "id", // id | date_created | last_updated - default: id - optional
  "order": "DESC" // DESC/ASC - default: DESC - optional
}
```

---

## Rider APIs (17 Endpoints)

### Authentication & Profile

#### 1. Login
**Endpoint:** `POST /rider/app/v1/api/login`

**Parameters:**
```json
{
  "mobile": "9874565478",
  "password": "12345678",
  "fcm_id": "FCM_ID" // optional
}
```

---

#### 2. Get Rider Details
**Endpoint:** `POST /rider/app/v1/api/get_rider_details`

**Parameters:**
```json
{
  "id": 15
}
```

---

#### 5. Update User
**Endpoint:** `POST /rider/app/v1/api/update_user`

**Parameters:**
```json
{
  "user_id": 34,
  "username": "hiten",
  "mobile": "7852347890", // optional
  "email": "aman@gmail.com", // optional
  "address": "address", // optional
  "old": "12345", // old password
  "new": "345234", // new password
  "status": 1 // 1 or 0 - optional - default: 1
}
```

---

#### 6. Update FCM
**Endpoint:** `POST /rider/app/v1/api/update_fcm`

**Parameters:**
```json
{
  "user_id": 12,
  "fcm_id": "FCM_ID"
}
```

---

#### 7. Reset Password
**Endpoint:** `POST /rider/app/v1/api/reset_password`

**Parameters:**
```json
{
  "mobile_no": "7894561235",
  "new": "pass@123"
}
```

---

#### 8. Verify User
**Endpoint:** `POST /rider/app/v1/api/verify_user`

**Parameters:**
```json
{
  "mobile": "1234567890",
  "email": "test@gmail.com" // optional
}
```

---

#### 9. Get Settings
**Endpoint:** `POST /rider/app/v1/api/get_settings`

**Parameters:**
```json
{
  "type": "rider_privacy_policy" // rider_privacy_policy / rider_terms_conditions / system_settings
}
```

---

### Orders Management

#### 3. Get Orders
**Endpoint:** `POST /rider/app/v1/api/get_orders`

**Parameters:**
```json
{
  "user_id": 101,
  "active_status": "pending", // pending|confirmed|preparing|out_for_delivery|delivered|cancelled - optional
  "limit": 25, // default: 25 - optional
  "offset": 0, // default: 0 - optional
  "sort": "id", // id / date_added - default: id - optional
  "order": "DESC" // DESC/ASC - default: DESC - optional
}
```

---

#### 12. Update Order Status
**Endpoint:** `POST /rider/app/v1/api/update_order_status`

**Parameters:**
```json
{
  "rider_id": 12,
  "order_id": 137,
  "status": "confirmed", // confirmed|preparing|out_for_delivery|delivered|cancelled
  "otp": "value" // required when status is delivered
}
```

---

#### 13. Get Pending Orders
**Endpoint:** `POST /rider/app/v1/api/get_pending_orders`

**Parameters:**
```json
{
  "user_id": 101, // rider_id
  "limit": 25, // default: 25 - optional
  "offset": 0, // default: 0 - optional
  "sort": "id", // id / date_added - default: id - optional
  "order": "DESC" // DESC/ASC - default: DESC - optional
}
```

---

#### 14. Update Order Request
**Endpoint:** `POST /rider/app/v1/api/update_order_request`

**Parameters:**
```json
{
  "rider_id": 12,
  "order_id": 137,
  "accept_order": 1 // 1: accept_order | 0: reject order
}
```

---

### Financial

#### 4. Get Fund Transfers
**Endpoint:** `POST /rider/app/v1/api/get_fund_transfers`

**Parameters:**
```json
{
  "user_id": 101,
  "limit": 25, // default: 25 - optional
  "offset": 0, // default: 0 - optional
  "sort": "id", // id / date_added - default: id - optional
  "order": "DESC" // DESC/ASC - default: DESC - optional
}
```

---

#### 10. Send Withdrawal Request
**Endpoint:** `POST /rider/app/v1/api/send_withdrawal_request`

**Parameters:**
```json
{
  "user_id": 15,
  "payment_address": "12343535",
  "amount": 560
}
```

---

#### 11. Get Withdrawal Request
**Endpoint:** `POST /rider/app/v1/api/get_withdrawal_request`

**Parameters:**
```json
{
  "user_id": 15,
  "limit": 10,
  "offset": 10
}
```

---

#### 15. Get Rider Cash Collection
**Endpoint:** `POST /rider/app/v1/api/get_rider_cash_collection`

**Parameters:**
```json
{
  "rider_id": 15,
  "status": "", // rider_cash (rider collected) | rider_cash_collection (admin collected)
  "limit": 25, // default: 25 - optional
  "offset": 0, // default: 0 - optional
  "sort": "id", // optional
  "order": "DESC", // DESC/ASC - default: DESC - optional
  "search": "value" // optional
}
```

---

### Live Tracking

#### 16. Manage Live Tracking
**Endpoint:** `POST /rider/app/v1/api/manage_live_tracking`

**Parameters:**
```json
{
  "order_id": 137,
  "order_status": "out_for_delivery",
  "latitude": "12345678",
  "longitude": "14654654"
}
```

---

#### 17. Delete Live Tracking
**Endpoint:** `POST /rider/app/v1/api/delete_live_tracking`

**Parameters:**
```json
{
  "order_id": 137
}
```

---

## Testing Notes

### HTTP Method
All APIs use **POST** method

### Response Format
All APIs return JSON formatted responses

### Authentication
- Most APIs require `user_id` or `rider_id` for authentication
- Mobile apps should store and pass FCM tokens for push notifications

### Error Handling
Check the response for:
- `error` field for error status
- `message` field for error/success messages
- `data` field for response data

### Testing Tools Recommended
- **Postman** - For API testing and collection management
- **Insomnia** - Alternative REST client
- **cURL** - Command line testing
- **Thunder Client** - VS Code extension

### Example cURL Request
```bash
curl -X POST https://your-domain.com/app/v1/api/login \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "mobile=9874565478&fcm_id=YOUR_FCM_ID"
```

### Example Postman Setup
1. Create a new collection
2. Set base URL as environment variable
3. Add POST request for each endpoint
4. Use x-www-form-urlencoded for body
5. Add all required parameters

---

## Status Codes Reference

### Order Status
- `pending` - Order placed, waiting for confirmation
- `confirmed` - Order confirmed by restaurant
- `preparing` - Food is being prepared
- `out_for_delivery` - Rider picked up the order
- `delivered` - Order delivered successfully
- `cancelled` - Order cancelled

### Ticket Status
- `1` - Pending
- `2` - Opened
- `3` - Resolved
- `4` - Closed
- `5` - Reopened

### Transaction Types
- **For transactions:** COD, stripe, razorpay, paypal, paystack, flutterwave
- **For wallet:** credit, debit

---

## Contact
For API support and issues, please contact the development team.

**Developed by:** WRTeam Developers

