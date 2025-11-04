#!/usr/bin/env python3
"""
Script to generate Postman Collection from API documentation
"""

import json
import uuid
from datetime import datetime

def create_postman_collection():
    """Create a comprehensive Postman collection for DailyDose APIs"""
    
    collection = {
        "info": {
            "name": "DailyDose API Collection",
            "description": "Complete API collection for DailyDose application including Customer/User APIs and Rider APIs",
            "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json",
            "_exporter_id": str(uuid.uuid4())
        },
        "variable": [
            {
                "key": "base_url",
                "value": "https://your-domain.com",
                "type": "string"
            },
            {
                "key": "user_api_base",
                "value": "{{base_url}}/app/v1/api",
                "type": "string"
            },
            {
                "key": "rider_api_base",
                "value": "{{base_url}}/rider/app/v1/api",
                "type": "string"
            }
        ],
        "item": []
    }
    
    # Customer/User APIs
    customer_apis = create_customer_apis()
    collection["item"].extend(customer_apis)
    
    # Rider APIs
    rider_apis = create_rider_apis()
    collection["item"].extend(rider_apis)
    
    return collection

def create_customer_apis():
    """Create all customer/user API endpoints"""
    items = []
    
    # Authentication & User Management
    auth_folder = {
        "name": "Authentication & User Management",
        "item": [
            create_request("login", "{{user_api_base}}/login", {
                "mobile": "9874565478",
                "fcm_id": "FCM_ID"
            }, description="Login with mobile number"),
            create_request("update_fcm", "{{user_api_base}}/update_fcm", {
                "user_id": "12",
                "fcm_id": "FCM_ID"
            }),
            create_request("reset_password", "{{user_api_base}}/reset_password", {
                "mobile_no": "7894561235",
                "new": "pass@123"
            }),
            create_request("get_login_identity", "{{user_api_base}}/get_login_identity", {}),
            create_request("verify_user", "{{user_api_base}}/verify_user", {
                "mobile": "9874565478"
            }),
            create_request("verify_otp", "{{user_api_base}}/verify_otp", {
                "mobile": "9874565478",
                "otp": "123456"
            }),
            create_request("resend_otp", "{{user_api_base}}/resend_otp", {
                "mobile": "9874565478"
            }),
            create_request("register_user", "{{user_api_base}}/register_user", {
                "name": "John Doe",
                "email": "john@example.com",
                "mobile": "9874565478",
                "country_code": "91",
                "referral_code": "MY_CODE",
                "fcm_id": "YOUR_FCM_ID",
                "friends_code": "45dsrwr",
                "latitude": "66.89",
                "longitude": "67.8"
            }),
            create_request("sign_up", "{{user_api_base}}/sign_up", {
                "name": "John Doe",
                "email": "john@example.com",
                "mobile": "9874565478",
                "country_code": "91",
                "referral_code": "MY_CODE",
                "fcm_id": "YOUR_FCM_ID"
            }),
            create_request("update_user", "{{user_api_base}}/update_user", {
                "user_id": "34",
                "username": "hiten",
                "mobile": "7852347890",
                "email": "amangoswami@gmail.com",
                "address": "Time Square",
                "latitude": "45.453",
                "longitude": "45.453",
                "image": [],
                "city_id": "1",
                "referral_code": "Userscode"
            }),
            create_request("delete_my_account", "{{user_api_base}}/delete_my_account", {
                "user_id": "1"
            })
        ]
    }
    items.append(auth_folder)
    
    # Location & Delivery
    location_folder = {
        "name": "Location & Delivery",
        "item": [
            create_request("is_city_deliverable", "{{user_api_base}}/is_city_deliverable", {
                "id": "1",
                "name": "bhuj"
            }),
            create_request("is_order_deliverable", "{{user_api_base}}/is_order_deliverable", {
                "user_id": "1",
                "address_id": "1"
            }),
            create_request("get_cities", "{{user_api_base}}/get_cities", {
                "sort": "c.name",
                "order": "ASC",
                "search": "value",
                "limit": "25",
                "offset": "0"
            }),
            create_request("get_delivery_charges", "{{user_api_base}}/get_delivery_charges", {
                "user_id": "1",
                "address_id": "1"
            }),
            create_request("search_location", "{{user_api_base}}/search_location", {
                "input": "string"
            }),
            create_request("get_location_details", "{{user_api_base}}/get_location_details", {
                "place_id": "string"
            }),
            create_request("get_location_details_by_lat_long", "{{user_api_base}}/get_location_details_by_lat_long", {
                "latitude": "40.7128",
                "longitude": "-74.0060"
            }),
            create_request("get_live_tracking_details", "{{user_api_base}}/get_live_tracking_details", {
                "order_id": "1"
            }),
            create_request("get_time_slots", "{{user_api_base}}/get_time_slots", {
                "date": "2024-01-01"
            })
        ]
    }
    items.append(location_folder)
    
    # Address Management
    address_folder = {
        "name": "Address Management",
        "item": [
            create_request("add_address", "{{user_api_base}}/add_address", {
                "user_id": "1",
                "mobile": "9727800638",
                "address": "#123,Time Square Empire,bhuj",
                "city": "1",
                "latitude": "1234",
                "longitude": "1234",
                "area": "umiya nagar",
                "type": "Home",
                "name": "John Smith",
                "country_code": "+91",
                "alternate_mobile": "9876543210",
                "landmark": "prince hotel",
                "pincode": "370001",
                "state": "Gujarat",
                "country": "India",
                "is_default": "1"
            }),
            create_request("update_address", "{{user_api_base}}/update_address", {
                "id": "1",
                "user_id": "1",
                "mobile": "9727800638",
                "address": "#123,Time Square,bhuj",
                "city": "1",
                "type": "Home",
                "name": "John Smith",
                "country_code": "+91",
                "alternate_mobile": "9876543210",
                "landmark": "prince hotel",
                "area": "umiya nagar",
                "pincode": "370001",
                "state": "Gujarat",
                "country": "India",
                "latitude": "1234",
                "longitude": "1234",
                "is_default": "1"
            }),
            create_request("get_address", "{{user_api_base}}/get_address", {
                "user_id": "3",
                "address_id": "1",
                "partner_id": "1234"
            }),
            create_request("delete_address", "{{user_api_base}}/delete_address", {
                "id": "3"
            })
        ]
    }
    items.append(address_folder)
    
    # Content & UI
    content_folder = {
        "name": "Content & UI",
        "item": [
            create_request("get_slider_images", "{{user_api_base}}/get_slider_images", {}),
            create_request("get_offer_images", "{{user_api_base}}/get_offer_images", {}),
            create_request("get_settings", "{{user_api_base}}/get_settings", {
                "type": "all",
                "user_id": "15"
            }),
            create_request("get_sections", "{{user_api_base}}/get_sections", {
                "limit": "10",
                "offset": "0",
                "user_id": "12",
                "section_id": "4",
                "top_rated_foods": "1",
                "p_limit": "10",
                "p_offset": "0",
                "p_sort": "pv.price",
                "p_order": "asc",
                "filter_by": "p.id",
                "latitude": "123",
                "longitude": "123"
            }),
            create_request("get_faqs", "{{user_api_base}}/get_faqs", {}),
            create_request("get_languages", "{{user_api_base}}/get_languages", {}),
            create_request("get_branches", "{{user_api_base}}/get_branches", {
                "city_id": "1"
            })
        ]
    }
    items.append(content_folder)
    
    # Products & Categories
    products_folder = {
        "name": "Products & Categories",
        "item": [
            create_request("get_categories", "{{user_api_base}}/get_categories", {
                "id": "15",
                "limit": "25",
                "offset": "0",
                "sort": "id",
                "order": "DESC"
            }),
            create_request("get_products", "{{user_api_base}}/get_products", {
                "id": "101",
                "category_id": "29",
                "user_id": "15",
                "search": "keyword",
                "tags": "tag1, tag2",
                "highlights": "tag1, tag2",
                "attribute_value_ids": "34,23,12",
                "limit": "25",
                "offset": "0",
                "sort": "p.id",
                "order": "DESC",
                "top_rated_foods": "1",
                "discount": "5",
                "min_price": "10000",
                "max_price": "50000",
                "partner_id": "1255",
                "product_ids": "19,20",
                "product_variant_ids": "44,45,40",
                "vegetarian": "1",
                "filter_by": "p.id",
                "latitude": "123",
                "longitude": "123",
                "city_id": "1"
            }),
            create_request("get_offline_products", "{{user_api_base}}/get_offline_products", {
                "category_id": "29"
            }),
            create_request("get_partners", "{{user_api_base}}/get_partners", {
                "id": "1",
                "city_id": "1",
                "user_id": "1",
                "limit": "25",
                "offset": "0",
                "sort": "p.id",
                "order": "DESC",
                "top_rated_partner": "1",
                "only_opened_partners": "1",
                "vegetarian": "1",
                "latitude": "123",
                "longitude": "123"
            })
        ]
    }
    items.append(products_folder)
    
    # Cart Management
    cart_folder = {
        "name": "Cart Management",
        "item": [
            create_request("manage_cart", "{{user_api_base}}/manage_cart", {
                "user_id": "2",
                "product_variant_id": "23",
                "clear_cart": "0",
                "is_saved_for_later": "0",
                "qty": "2",
                "add_on_id": "1",
                "add_on_qty": "1"
            }, description="Add/Update cart items"),
            create_request("get_user_cart", "{{user_api_base}}/get_user_cart", {
                "user_id": "1",
                "is_saved_for_later": "0"
            }),
            create_request("remove_from_cart", "{{user_api_base}}/remove_from_cart", {
                "user_id": "2",
                "product_variant_id": "23"
            })
        ]
    }
    items.append(cart_folder)
    
    # Orders
    orders_folder = {
        "name": "Orders",
        "item": [
            create_request("place_order", "{{user_api_base}}/place_order", {
                "user_id": "5",
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
                "promo_code": "NEW20",
                "payment_method": "COD",
                "address_id": "17",
                "is_wallet_used": "1",
                "wallet_balance_used": "1",
                "active_status": "pending",
                "order_note": "text",
                "delivery_tip": "text",
                "is_self_pick_up": "0"
            }),
            create_request("get_orders", "{{user_api_base}}/get_orders", {
                "user_id": "101",
                "id": "123",
                "active_status": "pending",
                "limit": "25",
                "offset": "0",
                "sort": "o.id",
                "order": "DESC",
                "download_invoice": "0"
            }),
            create_request("update_order_status", "{{user_api_base}}/update_order_status", {
                "status": "cancelled",
                "order_id": "1201",
                "reason": "test"
            }),
            create_request("delete_order", "{{user_api_base}}/delete_order", {
                "order_id": "1"
            }),
            create_request("re_order", "{{user_api_base}}/re_order", {
                "user_id": "1",
                "order_id": "123"
            })
        ]
    }
    items.append(orders_folder)
    
    # Ratings
    ratings_folder = {
        "name": "Ratings",
        "item": [
            create_request("set_product_rating", "{{user_api_base}}/set_product_rating", {
                "user_id": "21",
                "product_id": "33",
                "rating": "4.2",
                "comment": "Done",
                "images[]": []
            }),
            create_request("get_product_rating", "{{user_api_base}}/get_product_rating", {
                "product_id": "12",
                "user_id": "1",
                "limit": "25",
                "offset": "0",
                "sort": "type",
                "order": "DESC"
            }),
            create_request("delete_product_rating", "{{user_api_base}}/delete_product_rating", {
                "rating_id": "32"
            }),
            create_request("set_rider_rating", "{{user_api_base}}/set_rider_rating", {
                "user_id": "21",
                "rider_id": "33",
                "rating": "4.2",
                "comment": "Done"
            }),
            create_request("get_rider_rating", "{{user_api_base}}/get_rider_rating", {
                "rider_id": "12",
                "user_id": "1",
                "limit": "25",
                "offset": "0",
                "sort": "u.id",
                "order": "DESC"
            }),
            create_request("delete_rider_rating", "{{user_api_base}}/delete_rider_rating", {
                "rating_id": "32"
            }),
            create_request("set_order_rating", "{{user_api_base}}/set_order_rating", {
                "user_id": "21",
                "order_id": "33",
                "rating": "4.2",
                "comment": "Done"
            }),
            create_request("get_order_rating", "{{user_api_base}}/get_order_rating", {
                "order_id": "12",
                "user_id": "1"
            }),
            create_request("delete_order_rating", "{{user_api_base}}/delete_order_rating", {
                "rating_id": "32"
            }),
            create_request("get_partner_ratings", "{{user_api_base}}/get_partner_ratings", {
                "partner_id": "12",
                "limit": "25",
                "offset": "0"
            })
        ]
    }
    items.append(ratings_folder)
    
    # Favorites
    favorites_folder = {
        "name": "Favorites",
        "item": [
            create_request("add_to_favorites", "{{user_api_base}}/add_to_favorites", {
                "user_id": "15",
                "type": "partners",
                "type_id": "60"
            }),
            create_request("remove_from_favorites", "{{user_api_base}}/remove_from_favorites", {
                "user_id": "15",
                "type": "partners",
                "type_id": "60"
            }),
            create_request("get_favorites", "{{user_api_base}}/get_favorites", {
                "user_id": "12",
                "type": "partners",
                "limit": "10",
                "offset": "0"
            })
        ]
    }
    items.append(favorites_folder)
    
    # Notifications
    notifications_folder = {
        "name": "Notifications",
        "item": [
            create_request("get_notifications", "{{user_api_base}}/get_notifications", {
                "sort": "id",
                "order": "DESC",
                "limit": "10",
                "offset": "0"
            })
        ]
    }
    items.append(notifications_folder)
    
    # Transactions
    transactions_folder = {
        "name": "Transactions",
        "item": [
            create_request("add_transaction", "{{user_api_base}}/add_transaction", {
                "transaction_type": "transaction",
                "user_id": "15",
                "order_id": "23",
                "type": "COD",
                "payment_method": "razorpay",
                "txn_id": "201567892154",
                "amount": "450",
                "status": "success",
                "message": "Done"
            }),
            create_request("transactions", "{{user_api_base}}/transactions", {
                "user_id": "73",
                "id": "1001",
                "transaction_type": "transaction",
                "type": "COD",
                "search": "Search keyword",
                "limit": "25",
                "offset": "0",
                "sort": "id",
                "order": "DESC"
            }),
            create_request("send_withdrawal_request", "{{user_api_base}}/send_withdrawal_request", {
                "user_id": "15",
                "payment_address": "12343535",
                "amount": "560"
            }),
            create_request("get_withdrawal_request", "{{user_api_base}}/get_withdrawal_request", {
                "user_id": "15",
                "limit": "10",
                "offset": "0"
            })
        ]
    }
    items.append(transactions_folder)
    
    # Support Tickets
    tickets_folder = {
        "name": "Support Tickets",
        "item": [
            create_request("get_ticket_types", "{{user_api_base}}/get_ticket_types", {}),
            create_request("add_ticket", "{{user_api_base}}/add_ticket", {
                "ticket_type_id": "1",
                "subject": "product_image not displaying",
                "email": "test@gmail.com",
                "description": "its not showing images of products in web",
                "user_id": "1"
            }),
            create_request("edit_ticket", "{{user_api_base}}/edit_ticket", {
                "ticket_id": "1",
                "ticket_type_id": "1",
                "subject": "product_image not displaying",
                "email": "test@gmail.com",
                "description": "its not showing attachments of products in web",
                "user_id": "1",
                "status": "3"
            }),
            create_request("send_message", "{{user_api_base}}/send_message", {
                "user_type": "user",
                "user_id": "1",
                "ticket_id": "1",
                "message": "test",
                "attachments[]": []
            }),
            create_request("get_tickets", "{{user_api_base}}/get_tickets", {
                "ticket_id": "1001",
                "ticket_type_id": "1001",
                "user_id": "1001",
                "status": "1",
                "search": "Search keyword",
                "limit": "25",
                "offset": "0",
                "sort": "id",
                "order": "DESC"
            }),
            create_request("get_messages", "{{user_api_base}}/get_messages", {
                "ticket_id": "1001",
                "user_type": "user",
                "user_id": "1001",
                "search": "Search keyword",
                "limit": "25",
                "offset": "0",
                "sort": "id",
                "order": "DESC"
            })
        ]
    }
    items.append(tickets_folder)
    
    # Promo Codes
    promo_folder = {
        "name": "Promo Codes",
        "item": [
            create_request("validate_promo_code", "{{user_api_base}}/validate_promo_code", {
                "promo_code": "NEWOFF10",
                "user_id": "28",
                "final_total": "300"
            }),
            create_request("get_promo_codes", "{{user_api_base}}/get_promo_codes", {
                "search": "Search keyword",
                "limit": "25",
                "offset": "0",
                "sort": "id",
                "order": "DESC"
            }),
            create_request("validate_refer_code", "{{user_api_base}}/validate_refer_code", {
                "referral_code": "USERS_CODE_TO_BE_VALIDATED"
            })
        ]
    }
    items.append(promo_folder)
    
    # Payment Methods - PayPal
    paypal_folder = {
        "name": "Payments - PayPal",
        "item": [
            create_request("get_paypal_link", "{{user_api_base}}/get_paypal_link", {
                "user_id": "73",
                "order_id": "11",
                "amount": "150"
            }),
            create_request("paypal_transaction_webview", "{{user_api_base}}/paypal_transaction_webview", {
                "order_id": "11",
                "amount": "150"
            }),
            create_request("app_payment_status", "{{user_api_base}}/app_payment_status", {
                "order_id": "11",
                "status": "success"
            }),
            create_request("ipn", "{{user_api_base}}/ipn", {
                "order_id": "11"
            })
        ]
    }
    items.append(paypal_folder)
    
    # Payment Methods - Stripe
    stripe_folder = {
        "name": "Payments - Stripe",
        "item": [
            create_request("payment_intent", "{{user_api_base}}/payment_intent", {
                "amount": "100",
                "order_id": "11",
                "user_id": "73"
            }),
            create_request("stripe_webhook", "{{user_api_base}}/stripe_webhook", {})
        ]
    }
    items.append(stripe_folder)
    
    # Payment Methods - Razorpay
    razorpay_folder = {
        "name": "Payments - Razorpay",
        "item": [
            create_request("razorpay_create_order", "{{user_api_base}}/razorpay_create_order", {
                "amount": "100",
                "order_id": "11",
                "user_id": "73"
            }),
            create_request("razorpay_webhook", "{{user_api_base}}/razorpay_webhook", {})
        ]
    }
    items.append(razorpay_folder)
    
    # Payment Methods - Paytm
    paytm_folder = {
        "name": "Payments - Paytm",
        "item": [
            create_request("generate_paytm_checksum", "{{user_api_base}}/generate_paytm_checksum", {
                "order_id": "1001",
                "amount": "1099",
                "user_id": "73",
                "industry_type": "Industry",
                "channel_id": "WAP",
                "website": "website link"
            }),
            create_request("generate_paytm_txn_token", "{{user_api_base}}/generate_paytm_txn_token", {
                "amount": "100.00",
                "order_id": "102",
                "user_id": "73",
                "industry_type": "",
                "channel_id": "",
                "website": ""
            }),
            create_request("validate_paytm_checksum", "{{user_api_base}}/validate_paytm_checksum", {
                "paytm_checksum": "PAYTM_CHECKSUM",
                "order_id": "1001",
                "amount": "1099",
                "user_id": "73",
                "industry_type": "Industry",
                "channel_id": "WAP",
                "website": "website link"
            })
        ]
    }
    items.append(paytm_folder)
    
    # Payment Methods - Flutterwave
    flutterwave_folder = {
        "name": "Payments - Flutterwave",
        "item": [
            create_request("flutterwave_webview", "{{user_api_base}}/flutterwave_webview", {
                "amount": "100",
                "user_id": "73",
                "reference": "eShop-165232013-400"
            }),
            create_request("flutterwave_payment_response", "{{user_api_base}}/flutterwave_payment_response", {
                "reference": "eShop-165232013-400"
            }),
            create_request("flutterwave_webhook", "{{user_api_base}}/flutterwave_webhook", {})
        ]
    }
    items.append(flutterwave_folder)
    
    # Payment Methods - Midtrans
    midtrans_folder = {
        "name": "Payments - Midtrans",
        "item": [
            create_request("create_midtrans_transaction", "{{user_api_base}}/create_midtrans_transaction", {
                "amount": "100",
                "order_id": "11",
                "user_id": "73"
            }),
            create_request("get_midtrans_transaction_status", "{{user_api_base}}/get_midtrans_transaction_status", {
                "order_id": "11"
            }),
            create_request("midtrans_payment_process", "{{user_api_base}}/midtrans_payment_process", {
                "order_id": "11",
                "status": "success"
            }),
            create_request("midtrans_wallet_transaction", "{{user_api_base}}/midtrans_wallet_transaction", {
                "user_id": "73",
                "amount": "100"
            }),
            create_request("midtrans_webhook", "{{user_api_base}}/midtrans_webhook", {})
        ]
    }
    items.append(midtrans_folder)
    
    # Payment Methods - PhonePe
    phonepe_folder = {
        "name": "Payments - PhonePe",
        "item": [
            create_request("phonepe_webview", "{{user_api_base}}/phonepe_webview", {
                "amount": "100",
                "order_id": "11",
                "user_id": "73"
            }),
            create_request("phonepe_app", "{{user_api_base}}/phonepe_app", {
                "amount": "100",
                "order_id": "11",
                "user_id": "73"
            }),
            create_request("phonepe_web", "{{user_api_base}}/phonepe_web", {
                "amount": "100",
                "order_id": "11",
                "user_id": "73"
            }),
            create_request("phonepe_webhook", "{{user_api_base}}/phonepe_webhook", {})
        ]
    }
    items.append(phonepe_folder)
    
    return items

def create_rider_apis():
    """Create all rider API endpoints"""
    items = []
    
    rider_folder = {
        "name": "Rider APIs",
        "item": [
            create_request("login", "{{rider_api_base}}/login", {
                "mobile": "9874565478",
                "password": "12345678",
                "fcm_id": "FCM_ID"
            }),
            create_request("get_rider_details", "{{rider_api_base}}/get_rider_details", {
                "id": "15"
            }),
            create_request("get_orders", "{{rider_api_base}}/get_orders", {
                "user_id": "101",
                "active_status": "pending",
                "limit": "25",
                "offset": "0",
                "sort": "id",
                "order": "DESC"
            }),
            create_request("get_fund_transfers", "{{rider_api_base}}/get_fund_transfers", {
                "user_id": "101",
                "limit": "25",
                "offset": "0",
                "sort": "id",
                "order": "DESC"
            }),
            create_request("update_user", "{{rider_api_base}}/update_user", {
                "user_id": "34",
                "username": "hiten",
                "mobile": "7852347890",
                "email": "amangoswami@gmail.com",
                "address": "address",
                "old": "12345",
                "new": "345234",
                "status": "1"
            }),
            create_request("update_fcm", "{{rider_api_base}}/update_fcm", {
                "user_id": "12",
                "fcm_id": "FCM_ID"
            }),
            create_request("reset_password", "{{rider_api_base}}/reset_password", {
                "mobile_no": "7894561235",
                "new": "pass@123"
            }),
            create_request("verify_user", "{{rider_api_base}}/verify_user", {
                "mobile": "1234567890",
                "email": "test@gmail.com"
            }),
            create_request("get_settings", "{{rider_api_base}}/get_settings", {
                "type": "rider_privacy_policy"
            }),
            create_request("send_withdrawal_request", "{{rider_api_base}}/send_withdrawal_request", {
                "user_id": "15",
                "payment_address": "12343535",
                "amount": "560"
            }),
            create_request("get_withdrawal_request", "{{rider_api_base}}/get_withdrawal_request", {
                "user_id": "15",
                "limit": "10",
                "offset": "10"
            }),
            create_request("update_order_status", "{{rider_api_base}}/update_order_status", {
                "rider_id": "12",
                "order_id": "137",
                "status": "confirmed",
                "otp": "1234"
            }),
            create_request("get_pending_orders", "{{rider_api_base}}/get_pending_orders", {
                "user_id": "101",
                "limit": "25",
                "offset": "0",
                "sort": "id",
                "order": "DESC"
            }),
            create_request("update_order_request", "{{rider_api_base}}/update_order_request", {
                "rider_id": "12",
                "order_id": "137",
                "accept_order": "1"
            }),
            create_request("get_rider_cash_collection", "{{rider_api_base}}/get_rider_cash_collection", {
                "rider_id": "15",
                "status": "rider_cash",
                "limit": "25",
                "offset": "0",
                "sort": "id",
                "order": "DESC",
                "search": "value"
            }),
            create_request("manage_live_tracking", "{{rider_api_base}}/manage_live_tracking", {
                "order_id": "137",
                "order_status": "out_for_delivery",
                "latitude": "12345678",
                "longitude": "14654654"
            }),
            create_request("delete_live_tracking", "{{rider_api_base}}/delete_live_tracking", {
                "order_id": "137"
            })
        ]
    }
    items.append(rider_folder)
    
    return items

def create_request(name, url, body_params, description=None):
    """Create a Postman request item"""
    request = {
        "name": name,
        "request": {
            "method": "POST",
            "header": [
                {
                    "key": "Content-Type",
                    "value": "application/json"
                }
            ],
            "body": {
                "mode": "urlencoded",
                "urlencoded": []
            },
            "url": {
                "raw": url,
                "host": ["{{user_api_base}}"],
                "path": []
            }
        }
    }
    
    if description:
        request["request"]["description"] = description
    
    # Parse URL
    if "{{user_api_base}}" in url:
        url_parts = url.replace("{{user_api_base}}/", "").split("/")
        request["request"]["url"]["host"] = ["{{", "user_api_base", "}}"]
        request["request"]["url"]["path"] = url_parts
    elif "{{rider_api_base}}" in url:
        url_parts = url.replace("{{rider_api_base}}/", "").split("/")
        request["request"]["url"]["host"] = ["{{", "rider_api_base", "}}"]
        request["request"]["url"]["path"] = url_parts
    
    # Add body parameters
    for key, value in body_params.items():
        param = {
            "key": key,
            "value": str(value),
            "type": "text"
        }
        request["request"]["body"]["urlencoded"].append(param)
    
    return request

if __name__ == "__main__":
    collection = create_postman_collection()
    
    output_file = "DailyDose_API_Collection.postman_collection.json"
    with open(output_file, "w", encoding="utf-8") as f:
        json.dump(collection, f, indent=2, ensure_ascii=False)
    
    print(f"✅ Postman collection generated successfully!")
    print(f"📁 File: {output_file}")
    print(f"📊 Total folders: {len(collection['item'])}")
    
    total_requests = sum(len(folder.get("item", [])) for folder in collection["item"])
    print(f"📝 Total requests: {total_requests}")

