(function ($) {
    'use strict';

    function showNotice(message) {
        var $notice = $('.fresh-shop-notice');

        if (!$notice.length) {
            $notice = $('<div class="fresh-shop-notice" role="status" aria-live="polite"></div>').appendTo('body');
        }

        $notice.text(message).addClass('is-visible');
        window.clearTimeout($notice.data('timer'));
        $notice.data('timer', window.setTimeout(function () {
            $notice.removeClass('is-visible');
        }, 2200));
    }

    function followFallback($trigger, quantity) {
        var href = $trigger.attr('href');

        if (href && href !== '#') {
            if (quantity) {
                href = href.replace(/([?&]quantity=)[^&]*/, '$1' + Math.max(1, parseInt(quantity, 10) || 1));
            }

            window.location.href = href;
        }
    }

    function postAction(action, productId, successMessage, $trigger, quantity) {
        if (!productId || !window.freshShop) {
            followFallback($trigger, quantity);
            return;
        }

        $trigger.addClass('is-loading').attr('aria-busy', 'true');

        $.post(freshShop.ajaxUrl, {
            action: action,
            nonce: freshShop.nonce,
            product_id: productId,
            quantity: Math.max(1, parseInt(quantity, 10) || 1)
        }).done(function (response) {
            if (!response || !response.success) {
                showNotice((response && response.data && response.data.message) || freshShop.errorMessage);
                followFallback($trigger, quantity);
                return;
            }

            if (typeof response.data.cartCount !== 'undefined') {
                $('.fresh-cart-count').text(response.data.cartCount);
            }

            if (typeof response.data.wishlistCount !== 'undefined') {
                $('.fresh-wishlist-count').text(response.data.wishlistCount);
            }

            $trigger.addClass('is-added');
            showNotice(response.data.message || successMessage);
        }).fail(function () {
            showNotice(freshShop.errorMessage);
            followFallback($trigger, quantity);
        }).always(function () {
            $trigger.removeClass('is-loading').removeAttr('aria-busy');
        });
    }

    function formatPrice(amount) {
        return '$' + Number(amount || 0).toFixed(2);
    }

    function cartDiscount(subtotal, type, amount) {
        amount = Number(amount || 0);

        if (!type || amount <= 0 || subtotal <= 0) {
            return 0;
        }

        if (type === 'percent') {
            return Math.min(subtotal, subtotal * Math.min(100, amount) / 100);
        }

        return Math.min(subtotal, amount);
    }

    function refreshCartTotals() {
        var $form = $('.fresh-cart-form');
        var subtotal = 0;
        var count = 0;

        if (!$form.length) {
            return;
        }

        $('.fresh-cart-line-subtotal').each(function () {
            var $line = $(this);
            var $item = $line.closest('.fresh-cart-item');
            var quantity = Math.max(0, parseInt($item.find('.fresh-cart-qty').val(), 10) || 0);
            var price = Number($line.data('price') || 0);
            var lineSubtotal = price * quantity;

            subtotal += lineSubtotal;
            count += quantity;
            $line.text(formatPrice(lineSubtotal));
            $line.siblings('small').text(quantity + ' x ' + formatPrice(price));
        });

        var discount = cartDiscount(subtotal, $form.data('coupon-type'), $form.data('coupon-amount'));
        var total = Math.max(0, subtotal - discount);

        $('.fresh-cart-subtotal').text(formatPrice(subtotal));
        $('.fresh-cart-discount').text(discount > 0 ? '-' + formatPrice(discount) : formatPrice(0));
        $('.fresh-cart-total').text(formatPrice(total));
        $('.fresh-cart-count').text(count);
    }

    $(document).on('click', '.fresh-add-to-cart', function (event) {
        var $trigger = $(this);
        var quantity = $trigger.closest('.ltn__product-item, tr, .fresh-single-product, .product-details-area').find('.fresh-card-qty, input[name="quantity"], .cart-plus-minus-box').first().val();
        var successMessage = window.freshShop ? freshShop.addedToCart : '';

        event.preventDefault();
        postAction('fresh_add_to_cart', $trigger.data('product-id'), successMessage, $trigger, quantity);
    });

    $(document).on('click', '.fresh-add-to-wishlist', function (event) {
        var $trigger = $(this);
        var successMessage = window.freshShop ? freshShop.addedWishlist : '';

        event.preventDefault();
        postAction('fresh_add_to_wishlist', $trigger.data('product-id'), successMessage, $trigger);
    });

    $(document).on('click', '.fresh-remove-from-wishlist', function (event) {
        var $trigger = $(this);
        var productId = $trigger.data('product-id');

        if (!productId || !window.freshShop) {
            return;
        }

        event.preventDefault();
        $trigger.addClass('is-loading').attr('aria-busy', 'true');

        $.post(freshShop.ajaxUrl, {
            action: 'fresh_remove_from_wishlist',
            nonce: freshShop.nonce,
            product_id: productId
        }).done(function (response) {
            if (!response || !response.success) {
                showNotice((response && response.data && response.data.message) || freshShop.errorMessage);
                followFallback($trigger);
                return;
            }

            if (typeof response.data.wishlistCount !== 'undefined') {
                $('.fresh-wishlist-count').text(response.data.wishlistCount);
            }

            $trigger.closest('.fresh-wishlist-item').remove();
            showNotice(response.data.message || 'Product removed from wishlist.');

            if (response.data.isEmpty) {
                window.location.reload();
            }
        }).fail(function () {
            showNotice(freshShop.errorMessage);
            followFallback($trigger);
        }).always(function () {
            $trigger.removeClass('is-loading').removeAttr('aria-busy');
        });
    });

    $(document).on('input change', '.fresh-cart-qty', refreshCartTotals);
    $(document).on('click', '.fresh-cart-form .qtybutton', function () {
        window.setTimeout(refreshCartTotals, 0);
    });
})(jQuery);
