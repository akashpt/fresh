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

    function pulseCount(selector) {
        var $items = $(selector);

        $items.removeClass('is-updated');
        window.setTimeout(function () {
            $items.addClass('is-updated');
        }, 10);
    }

    function emptyState(title, buttonText, buttonUrl) {
        return '' +
            '<div class="fresh-empty-state text-center pt-60 pb-60">' +
                '<h2>' + title + '</h2>' +
                '<div class="btn-wrapper mt-30">' +
                    '<a class="theme-btn-1 btn btn-effect-1" href="' + buttonUrl + '">' + buttonText + '</a>' +
                '</div>' +
            '</div>';
    }

    function followFallback($trigger, quantity) {
        var href = $trigger.attr('href');
        var minimum = $trigger.data('cart-mode') === 'set' ? 0 : 1;

        if (href && href !== '#') {
            if (quantity) {
                href = href.replace(/([?&]quantity=)[^&]*/, '$1' + Math.max(minimum, parseInt(quantity, 10) || minimum));
            }

            window.location.href = href;
        }
    }

    function updateCardCartHref($input) {
        var minimum = parseInt($input.attr('min'), 10);
        var quantity = Math.max(isNaN(minimum) ? 1 : minimum, parseInt($input.val(), 10) || 0);
        var $link = $input.closest('.fresh-card-cart-control').find('.fresh-add-to-cart');
        var href = $link.attr('href');

        if (href && href.indexOf('quantity=') !== -1) {
            $link.attr('href', href.replace(/([?&]quantity=)[^&]*/, '$1' + quantity));
        }
    }

    function postAction(action, productId, successMessage, $trigger, quantity) {
        if (!productId || !window.freshShop) {
            followFallback($trigger, quantity);
            return;
        }

        var setQuantity = $trigger.data('cart-mode') === 'set';
        var normalizedQuantity = setQuantity ? Math.max(0, parseInt(quantity, 10) || 0) : Math.max(1, parseInt(quantity, 10) || 1);

        $trigger.addClass('is-loading').attr('aria-busy', 'true');

        $.post(freshShop.ajaxUrl, {
            action: action,
            nonce: freshShop.nonce,
            product_id: productId,
            quantity: normalizedQuantity,
            set_quantity: setQuantity ? 1 : 0
        }).done(function (response) {
            if (!response || !response.success) {
                showNotice((response && response.data && response.data.message) || freshShop.errorMessage);
                followFallback($trigger, quantity);
                return;
            }

            if (typeof response.data.cartCount !== 'undefined') {
                $('.fresh-cart-count').text(response.data.cartCount);
                pulseCount('.fresh-cart-count');
            }

            if (typeof response.data.wishlistCount !== 'undefined') {
                $('.fresh-wishlist-count').text(response.data.wishlistCount);
                pulseCount('.fresh-wishlist-count');
            }

            if (typeof response.data.productQuantity !== 'undefined') {
                $trigger.find('span').text(parseInt(response.data.productQuantity, 10) > 0 ? 'Update' : 'Add');
            }

            $trigger.addClass('is-added');
            if (typeof response.data.cartCount !== 'undefined') {
                if (setQuantity && parseInt(response.data.productQuantity, 10) === 0) {
                    showNotice('Removed from cart. Cart has ' + response.data.cartCount + ' item' + (parseInt(response.data.cartCount, 10) === 1 ? '' : 's') + '.');
                } else if (setQuantity) {
                    showNotice('Cart updated. Cart has ' + response.data.cartCount + ' item' + (parseInt(response.data.cartCount, 10) === 1 ? '' : 's') + '.');
                } else {
                    showNotice('Added to cart. Cart has ' + response.data.cartCount + ' item' + (parseInt(response.data.cartCount, 10) === 1 ? '' : 's') + '.');
                }
            } else if (typeof response.data.wishlistCount !== 'undefined') {
                showNotice('Added to wishlist. Wishlist has ' + response.data.wishlistCount + ' item' + (parseInt(response.data.wishlistCount, 10) === 1 ? '' : 's') + '.');
            } else {
                showNotice(response.data.message || successMessage);
            }
        }).fail(function () {
            showNotice(freshShop.errorMessage);
            followFallback($trigger, quantity);
        }).always(function () {
            $trigger.removeClass('is-loading').removeAttr('aria-busy');
        });
    }

    function formatPrice(amount) {
        return '₹' + Number(amount || 0).toFixed(2);
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

    function cartDeliveryCharge(total, threshold, amount) {
        total = Number(total || 0);
        threshold = Number(threshold || 0);
        amount = Number(amount || 0);

        if (total <= 0 || total > threshold) {
            return 0;
        }

        return amount;
    }

    function refreshCartTotals() {
        var $form = $('.fresh-cart-form');
        var subtotal = 0;
        var productDiscount = 0;
        var count = 0;

        if (!$form.length) {
            return;
        }

        $('.fresh-cart-line-subtotal').each(function () {
            var $line = $(this);
            var $item = $line.closest('.fresh-cart-item');
            var quantity = Math.max(0, parseInt($item.find('.fresh-cart-qty').val(), 10) || 0);
            var price = Number($line.data('price') || 0);
            var regularPrice = Number($line.data('regular-price') || price);
            var lineSubtotal = price * quantity;
            var lineProductDiscount = Math.max(0, regularPrice - price) * quantity;

            subtotal += lineSubtotal;
            productDiscount += lineProductDiscount;
            count += quantity;
            $line.text(formatPrice(lineSubtotal));
            $line.siblings('small').text(quantity + ' x ' + formatPrice(price));
            $line.siblings('.fresh-cart-line-regular').text(formatPrice(regularPrice * quantity));
            $line.siblings('.fresh-cart-line-save').text('Save ' + formatPrice(lineProductDiscount));
        });

        var couponDiscount = cartDiscount(subtotal, $form.data('coupon-type'), $form.data('coupon-amount'));
        var totalDiscount = productDiscount + couponDiscount;
        var itemsTotal = Math.max(0, subtotal - couponDiscount);
        var deliveryCharge = cartDeliveryCharge(itemsTotal, $form.data('delivery-threshold'), $form.data('delivery-charge'));
        var total = itemsTotal + deliveryCharge;

        $('.fresh-cart-subtotal').text(formatPrice(subtotal));
        $('.fresh-cart-product-discount').text(productDiscount > 0 ? '-' + formatPrice(productDiscount) : formatPrice(0));
        $('.fresh-cart-coupon-discount').text(couponDiscount > 0 ? '-' + formatPrice(couponDiscount) : formatPrice(0));
        $('.fresh-cart-discount').text(totalDiscount > 0 ? '-' + formatPrice(totalDiscount) : formatPrice(0));
        $('.fresh-cart-delivery').text(deliveryCharge > 0 ? formatPrice(deliveryCharge) : 'Free');
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
                pulseCount('.fresh-wishlist-count');
            }

            $trigger.closest('.fresh-wishlist-item').remove();
            showNotice(response.data.message || 'Product removed from wishlist.');

            if (response.data.isEmpty) {
                $('.shoping-cart-inner').replaceWith(emptyState('Your wishlist is empty.', 'Go to Shop', freshShop.shopUrl || '/shop/'));
            }
        }).fail(function () {
            showNotice(freshShop.errorMessage);
            followFallback($trigger);
        }).always(function () {
            $trigger.removeClass('is-loading').removeAttr('aria-busy');
        });
    });

    $(document).on('click', '.fresh-cart-remove', function (event) {
        var $trigger = $(this);
        var productId = $trigger.data('product-id');

        if (!productId || !window.freshShop) {
            return;
        }

        event.preventDefault();
        $trigger.addClass('is-loading').attr('aria-busy', 'true');

        $.post(freshShop.ajaxUrl, {
            action: 'fresh_remove_from_cart',
            nonce: freshShop.nonce,
            product_id: productId
        }).done(function (response) {
            if (!response || !response.success) {
                showNotice((response && response.data && response.data.message) || freshShop.errorMessage);
                followFallback($trigger);
                return;
            }

            $trigger.closest('.fresh-cart-item').remove();

            if (typeof response.data.cartCount !== 'undefined') {
                $('.fresh-cart-count').text(response.data.cartCount);
                pulseCount('.fresh-cart-count');
            }

            refreshCartTotals();
            showNotice(response.data.message || 'Product removed from cart.');

            if (response.data.isEmpty) {
                $('.fresh-cart-form').replaceWith(emptyState('Your cart is empty.', 'Go to Shop', freshShop.shopUrl || '/shop/'));
            }
        }).fail(function () {
            showNotice(freshShop.errorMessage);
            followFallback($trigger);
        }).always(function () {
            $trigger.removeClass('is-loading').removeAttr('aria-busy');
        });
    });

    $(document).on('click', '.fresh-card-qty-btn', function () {
        var $button = $(this);
        var $input = $button.closest('.fresh-card-qty-control').find('.fresh-card-qty');
        var minimum = parseInt($input.attr('min'), 10);
        minimum = isNaN(minimum) ? 1 : minimum;
        var quantity = Math.max(minimum, parseInt($input.val(), 10) || minimum);

        if ($button.hasClass('fresh-card-qty-minus')) {
            quantity = Math.max(minimum, quantity - 1);
        } else {
            quantity += 1;
        }

        $input.val(quantity).trigger('change');
        updateCardCartHref($input);
    });

    $(document).on('input change', '.fresh-card-qty', function () {
        var $input = $(this);
        var minimum = parseInt($input.attr('min'), 10);
        minimum = isNaN(minimum) ? 1 : minimum;
        var quantity = Math.max(minimum, parseInt($input.val(), 10) || minimum);

        $input.val(quantity);
        updateCardCartHref($input);
    });

    $(document).on('input change', '.fresh-cart-qty', refreshCartTotals);
    $(document).on('click', '.fresh-cart-form .qtybutton', function () {
        window.setTimeout(refreshCartTotals, 0);
    });
})(jQuery);
