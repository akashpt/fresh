<?php
/**
 * The footer for our theme.
 */
?>

    <!-- FOOTER AREA START -->
    <footer class="ltn__footer-area  ">
        
        <div class="ltn__copyright-area ltn__copyright-2 section-bg-2 ltn__border-top-2 plr--5">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="ltn__copyright-design clearfix">
                            <p>All Rights Reserved @ Company <span class="current-year"></span></p>
                        </div>
                    </div>
                    <div class="col-md-6 col-12 align-self-center">
                        <div class="ltn__copyright-menu text-right text-end">
                            <ul>
                                <li><a href="#">Terms & Conditions</a></li>
                                <li><a href="#">Claim</a></li>
                                <li><a href="#">Privacy & Policy</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- FOOTER AREA END -->

    <?php $fresh_whatsapp_number = function_exists('fresh_whatsapp_number') ? fresh_whatsapp_number() : ''; ?>
    <?php if ($fresh_whatsapp_number) : ?>
        <a class="fresh-whatsapp-float" href="<?php echo esc_url('https://wa.me/' . rawurlencode($fresh_whatsapp_number)); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e('Chat on WhatsApp', 'fresh'); ?>">
            <i class="fab fa-whatsapp"></i>
        </a>
    <?php endif; ?>

    <!-- MODAL AREA START (Quick View Modal) -->
    <div class="ltn__modal-area ltn__quick-view-modal-area">
        <div class="modal fade" id="quick_view_modal" tabindex="-1">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            <!-- <i class="fas fa-times"></i> -->
                        </button>
                    </div>
                    <div class="modal-body">
                         <div class="ltn__quick-view-modal-inner">
                             <div class="modal-product-item">
                                <div class="row">
                                    <div class="col-lg-6 col-12">
                                        <div class="modal-product-img">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/product/4.png" alt="#">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-12">
                                        <div class="modal-product-info">
                                            <div class="product-ratting">
                                                <ul>
                                                    <li><a href="#"><i class="fas fa-star"></i></a></li>
                                                    <li><a href="#"><i class="fas fa-star"></i></a></li>
                                                    <li><a href="#"><i class="fas fa-star"></i></a></li>
                                                    <li><a href="#"><i class="fas fa-star-half-alt"></i></a></li>
                                                    <li><a href="#"><i class="far fa-star"></i></a></li>
                                                    <li class="review-total"> <a href="#"> ( 95 Reviews )</a></li>
                                                </ul>
                                            </div>
                                            <h3>Vegetables Juices</h3>
                                            <div class="product-price">
                                                <span>$149.00</span>
                                                <del>$165.00</del>
                                            </div>
                                            <div class="modal-product-meta ltn__product-details-menu-1">
                                                <ul>
                                                    <li>
                                                        <strong>Categories:</strong> 
                                                        <span>
                                                            <a href="#">Parts</a>
                                                            <a href="#">Car</a>
                                                            <a href="#">Seat</a>
                                                            <a href="#">Cover</a>
                                                        </span>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="ltn__product-details-menu-2">
                                                <ul>
                                                    <li>
                                                        <div class="cart-plus-minus">
                                                            <input type="text" value="02" name="qtybutton" class="cart-plus-minus-box">
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <a href="#" class="theme-btn-1 btn btn-effect-1" title="Add to Cart" data-bs-toggle="modal" data-bs-target="#add_to_cart_modal">
                                                            <i class="fas fa-shopping-cart"></i>
                                                            <span>ADD TO CART</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="ltn__product-details-menu-3">
                                                <ul>
                                                    <li>
                                                        <a href="#" class="" title="Wishlist" data-bs-toggle="modal" data-bs-target="#liton_wishlist_modal">
                                                            <i class="far fa-heart"></i>
                                                            <span>Add to Wishlist</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#" class="" title="Compare" data-bs-toggle="modal" data-bs-target="#quick_view_modal">
                                                            <i class="fas fa-exchange-alt"></i>
                                                            <span>Compare</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <hr>
                                            <div class="ltn__social-media">
                                                <ul>
                                                    <li>Share:</li>
                                                    <li><a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a></li>
                                                    <li><a href="#" title="Twitter"><i class="fab fa-twitter"></i></a></li>
                                                    <li><a href="#" title="Linkedin"><i class="fab fa-linkedin"></i></a></li>
                                                    <li><a href="#" title="Instagram"><i class="fab fa-instagram"></i></a></li>
                                                    
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                             </div>
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- MODAL AREA END -->

    <!-- MODAL AREA START (Add To Cart Modal) -->
    <div class="ltn__modal-area ltn__add-to-cart-modal-area">
        <div class="modal fade" id="add_to_cart_modal" tabindex="-1">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                         <div class="ltn__quick-view-modal-inner">
                             <div class="modal-product-item">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="modal-product-img">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/product/1.png" alt="#">
                                        </div>
                                         <div class="modal-product-info">
                                            <h5><a href="product-details.html">Vegetables Juices</a></h5>
                                            <p class="added-cart"><i class="fa fa-check-circle"></i>  Successfully added to your Cart</p>
                                            <div class="btn-wrapper">
                                                <a href="cart.html" class="theme-btn-1 btn btn-effect-1">View Cart</a>
                                                <a href="checkout.html" class="theme-btn-2 btn btn-effect-2">Checkout</a>
                                            </div>
                                         </div>
                                         <!-- additional-info -->
                                         <div class="additional-info d-none">
                                            <p>We want to give you <b>10% discount</b> for your first order, <br>  Use discount code at checkout</p>
                                            <div class="payment-method">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icons/payment.png" alt="#">
                                            </div>
                                         </div>
                                    </div>
                                </div>
                             </div>
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- MODAL AREA END -->

    <!-- MODAL AREA START (Wishlist Modal) -->
    <div class="ltn__modal-area ltn__add-to-cart-modal-area">
        <div class="modal fade" id="liton_wishlist_modal" tabindex="-1">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                         <div class="ltn__quick-view-modal-inner">
                             <div class="modal-product-item">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="modal-product-img">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/product/7.png">
                                        </div>
                                         <div class="modal-product-info">
                                            <h5><a href="product-details.html">Vegetables Juices</a></h5>
                                            <p class="added-cart"><i class="fa fa-check-circle"></i>  Successfully added to your Wishlist</p>
                                            <div class="btn-wrapper">
                                                <a href="<?php echo esc_url(fresh_page_url('wishlist')); ?>" class="theme-btn-1 btn btn-effect-1">View Wishlist</a>
                                            </div>
                                         </div>
                                         <!-- additional-info -->
                                         <div class="additional-info d-none">
                                            <p>We want to give you <b>10% discount</b> for your first order, <br>  Use discount code at checkout</p>
                                            <div class="payment-method">
                                                <img src="img/icons/payment.png" alt="#">
                                            </div>
                                         </div>
                                    </div>
                                </div>
                             </div>
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- MODAL AREA END -->

</div>
<!-- Body main wrapper end -->

    <!-- preloader area start -->
    <div class="preloader d-none" id="preloader">
        <div class="preloader-inner">
            <div class="spinner">
                <div class="dot1"></div>
                <div class="dot2"></div>
            </div>
        </div>
    </div>
    <!-- preloader area end -->

<?php wp_footer(); ?>
</body>
</html>
