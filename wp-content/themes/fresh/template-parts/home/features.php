<?php
$variant = $args['variant'] ?? 'top';
$features = fresh_home_features($variant);
?>

<?php if ($variant === 'bottom') : ?>
    <div class="ltn__feature-area before-bg-bottom-2-- mb--30--- plr--5 mb-60">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ltn__feature-item-box-wrap ltn__border-between-column white-bg">
                        <div class="row">
                            <?php foreach ($features as $feature) : ?>
                                <div class="col-xl-3 col-md-6 col-12">
                                    <div class="ltn__feature-item ltn__feature-item-8">
                                        <div class="ltn__feature-icon">
                                            <img <?php echo fresh_image_attrs(get_template_directory_uri() . '/assets/img/' . $feature['icon'], '', ['fallback_width' => 80, 'fallback_height' => 80]); ?>>
                                        </div>
                                        <div class="ltn__feature-info">
                                            <h4><?php echo esc_html($feature['title']); ?></h4>
                                            <p><?php echo esc_html($feature['text']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else : ?>
    <div class="ltn__feature-area mt-100 mt--65 d-none">
        <div class="container">
            <div class="row ltn__custom-gutter--- justify-content-center">
                <div class="col-lg-12">
                    <div class="ltn__feature-item-box-wrap ltn__feature-item-box-wrap-2 ltn__border section-bg-6">
                        <?php foreach ($features as $feature) : ?>
                            <div class="ltn__feature-item ltn__feature-item-8">
                                <div class="ltn__feature-icon">
                                    <img <?php echo fresh_image_attrs(get_template_directory_uri() . '/assets/img/' . $feature['icon'], '', ['fallback_width' => 80, 'fallback_height' => 80]); ?>>
                                </div>
                                <div class="ltn__feature-info">
                                    <h4><?php echo esc_html($feature['title']); ?></h4>
                                    <p><?php echo esc_html($feature['text']); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
