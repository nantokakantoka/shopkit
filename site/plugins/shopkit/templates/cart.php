<?php snippet('header') ?>

<?php if ($page->slider()->isNotEmpty()) snippet('slider',['photos'=>$page->slider()]) ?>

<?php snippet('breadcrumb') ?>

<h1 dir="auto"><?php echo $page->title()->html() ?></h1>

<?php if ($cart->count() === 0) { ?>
    <div class="uk-alert">
        <p dir="auto"><?php echo l::get('no-cart-items') ?></p>
    </div>
<?php } else { ?>

    <!-- Cart items -->
    <div class="uk-overflow-container">
        <table dir="auto" class="cart uk-table uk-table-striped">
            <thead>
                <tr>
                    <th><?php echo l::get('product') ?></th>
                    <th class="uk-text-center"><?php echo l::get('quantity') ?></th>
                    <th class="uk-text-right"><?php echo l::get('price') ?></th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                <?php foreach($items as $i => $item) { ?>
                    <tr>
                        <td>
                            <a href="<?php echo url($item->uri) ?>" title="<?php echo $item->fullTitle() ?>">
                                <?php if ($item->imgSrc) { ?>
                                    <img class="uk-float-left uk-margin-small-right" src="<?php echo $item->imgSrc ?>" title="<?php echo $item->name ?>">
                                <?php } ?>
                                <strong><?php echo $item->name ?></strong><br>
                                <?php ecco($item->sku,'<strong>SKU</strong> '.$item->sku.' / ') ?>
                                <?php ecco($item->variant,$item->variant) ?>
                                <?php ecco($item->option,' / '.$item->option) ?>
                            </a>
                        </td>
                        <td class="uk-text-center">
                            <form class="uk-display-inline" action="" method="post">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="id" value="<?php echo $item->id ?>">
                                <button class="uk-button uk-button-small" type="submit">
                                    <?php 
                                        if ($item->quantity === 1) {
                                            echo '&#10005;'; // x (delete)
                                        } else {
                                            echo '&#9660;'; // Down arrow
                                        }
                                    ?>
                                </button>
                            </form>
                            <span class="uk-margin-small-left uk-margin-small-right"><?php echo $item->quantity ?></span>
                            <form class="uk-display-inline" action="" method="post">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="id" value="<?php echo $item->id ?>">
                                <button class="uk-button uk-button-small" <?php ecco($item->maxQty,'disabled') ?> type="submit">&#9650;</button>
                            </form>
                        </td>
                        <td class="uk-text-right">
                            <?php echo $item->priceText ?>
                            <?php e($item->notax == 1,'<br><span class="uk-badge">'.l::get('no-tax').'</span>') ?>
                            <?php e($item->noshipping == 1,'<br><span class="uk-badge">'.l::get('no-shipping').'</span>') ?>
                        </td>
                        <td>
                            <form action="" method="post">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $item->id ?>">
                                <button class="uk-button uk-button-small" type="submit"><?php echo l::get('delete') ?></button> 
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>

            <tfoot class="uk-text-right">
                <tr>
                    <td colspan="2"><?php echo l::get('subtotal') ?></td>
                    <td><?php echo formatPrice($cart->getAmount()) ?></td>
                    <td></td>
                </tr>
                <?php $discountAmount = $cart->getDiscountAmount() ? $cart->getDiscountAmount() : 0 ?>
                <?php if ($pages->find('shop')->discount_codes()->toStructure()->count() > 0) {  ?>
                    <tr>
                        <td colspan="2"><?php echo l::get('discount') ?></td>
                        <?php if ($discountAmount > 0) { ?>
                            <td class="uk-text-success"><?php echo '&ndash; '.formatPrice($discountAmount) ?></td>
                            <td></td>
                        <?php } else { ?>
                            <td colspan="2" class="uk-text-left">
                                <form method="post" class="uk-form discount">
                                    <input type="text" name="dc" class="uk-form-width-small" />
                                    <button class="uk-button" type="submit">
                                        <?php echo l::get('discount-apply') ?>
                                    </button>
                                </form>
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
                <tr>
                    <td colspan="2"><?php echo l::get('shipping') ?></td>
                    <td colspan="2" class="uk-text-left">

                        <!-- Set country -->
                        <form class="uk-form" id="setCountry" action="" method="POST">
                            <select class="uk-form-width-medium" name="country" onChange="document.forms['setCountry'].submit();">
                                <?php foreach ($countries as $c) { ?>
                                    <option <?php ecco(s::get('country') === $c->uid(), 'selected') ?> value="<?php echo $c->countrycode() ?>">
                                        <?php echo $c->title() ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <button type="submit"><?php echo l::get('update-country') ?></button>
                        </form>

                        <!-- Set shipping -->
                        <form class="uk-form" id="setShipping" action="" method="POST">
                            <select class="uk-form-width-medium" name="shipping" onChange="document.forms['setShipping'].submit();">
                                <?php if (count($shipping_rates) > 0) { ?>
                                    <?php foreach ($shipping_rates as $rate) { ?>
                                        <option value="<?php echo str::slug($rate['title']) ?>" <?php e($shipping['title'] === $rate['title'],'selected') ?>>
                                            <?php echo $rate['title'] ?> (<?php echo formatPrice($rate['rate']) ?>)
                                        </option>
                                    <?php } ?>
                                <?php } else { ?>
                                    <!-- If no shipping rates are set, show free shipping -->
                                    <option value="free-shipping"><?php echo l::get('free-shipping') ?></option>
                                <?php } ?>
                            </select>
                            <button type="submit"><?php echo l::get('update-shipping') ?></button>
                        </form>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><?php echo l::get('tax') ?></td>
                    <td>
                        <?php $tax = number_format($cart->getTax(),2,'.','') ?>
                        <?php echo formatPrice($tax) ?>
                    </td>
                    <td></td>
                </tr>
                <tr class="total">
                    <td colspan="2"><?php echo l::get('total') ?></td>
                    <td>
                        <?php echo formatPrice($cart->getAmount() + $tax + $shipping['rate'] - $discountAmount) ?>
                        <?php echo page('shop')->currency_code() ?>
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Terms and conditions -->
    <?php if ($tc = page('shop/terms-conditions') and $tc->text()->isNotEmpty()) { ?>
        <div class="uk-alert">
            <p dir="auto"><?php echo l::get('terms-conditions') ?> <a href="<?php echo $tc->url() ?>" target="_blank"><?php echo $tc->title() ?></a>.</p>
        </div>
    <?php } ?>
    
    <!-- Gateway payment buttons -->
    <?php foreach($gateways as $gateway) { ?>
        <?php if ($gateway == 'paylater' and !$cart->canPayLater()) continue ?>
        <?php $g = $kirby->get('option', 'gateway-'.$gateway) ?>
        <form method="post" action="<?= url('shop/cart/process') ?>">
            
            <input type="hidden" name="gateway" value="<?= $gateway ?>">

            <div class="forRobots">
              <label for="subject"><?= l::get('honeypot-label') ?></label>
              <input type="text" name="subject">
            </div>

            <div class="uk-container uk-padding-remove">
                <button class="uk-button uk-button-primary uk-width-small-1-1 uk-width-medium-2-3 uk-align-medium-right" type="submit">
                    <?php if (!$g['logo']) { ?>
                        <?= $g['label'] ?>
                    <?php } else { ?>
                        <?php
                            $logo_path = $g['logo'];
                            $logo_data = base64_encode(file_get_contents($logo_path));
                            $logo_src = 'data: '.mime_content_type($logo_path).';base64,'.$logo_data;
                        ?>
                        <img class="uk-margin uk-margin-top" src="<?= $logo_src ?>" alt="<?= $g['label'] ?>">
                    <?php } ?>

                    <?php if (isset($g['sandbox']) and $g['sandbox']) { ?>
                        <div class="uk-alert uk-alert-warning uk-margin-top-remove">
                            <?= l::get('sandbox-message') ?>
                        </div>
                    <?php } ?>
                </button>
            </div>
        </form>
    <?php } ?>

    <script type="text/javascript">
        // Hide setCountry and setShipping submit buttons
        document.querySelector('#setCountry button').style.display = 'none';
        document.querySelector('#setShipping button').style.display = 'none';
    </script>

<?php } ?>

<?php snippet('footer') ?>